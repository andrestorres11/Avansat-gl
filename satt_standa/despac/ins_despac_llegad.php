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
  @include_once("../".DIR_APLICA_CENTRAL."/lib/general/functions.inc");
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
        default:
          $this -> Listar();
          break;
       }//FIN SWITCH
     }// FIN ELSE GLOBALS OPCION
 }//FIN FUNCION PRINCIPAL

 function Listar()
 {
   $datos_usuario = $this -> usuario -> retornar();

   $listado_prin = new Despachos($_REQUEST[cod_servic],1,$this -> cod_aplica,$this -> conexion);
   $listado_prin -> ListadoPrincipal($datos_usuario);
 }

 function Formulario()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];
   //trae la fecha actual
   $fec_actual = date("d-m-Y");
   $hor_actual = date("H:i");

   //presenta por defecta la fecha actual
   if(!isset($_REQUEST[feclle]))
      $_REQUEST[feclle]=$fec_actual;

   if(!isset($_REQUEST[horlle]))
      $_REQUEST[horlle]=$hor_actual;

  echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/fecha.js\"></script>\n";
  echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/llegada.js\"></script>\n";

  $fecha_lleg = new Fecha();

  $formulario = new Formulario ("index.php","post","","form_ins");

  $formulario -> oculto("usuario","$usuario",0);
  $formulario -> oculto("tercero","$tercero",0);
  $formulario -> oculto("feclle","$fec_actual",0);
  $formulario -> oculto("horlle","$hor_actual",0);
  $formulario -> oculto("window","central",0);
  $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
  $formulario -> oculto("opcion",2,0);
  $formulario -> oculto("despac","$_REQUEST[despac]",0);

  $listado_prin = new Despachos($_REQUEST[cod_servic],2,$this -> cod_aplica,$this -> conexion);
  $listado_prin  -> Encabezado($_REQUEST[despac],$datos_usuario);

  $formulario -> nueva_tabla();
  $formulario -> linea("Fecha de Llegada",1,"t2");

  $formulario -> nueva_tabla();
  $feactual = date("Y-m-d H:i");
  $formulario -> fecha_calendar("Fecha/Hora","fecpronov","form_ins",$feactual,"yyyy/mm/dd hh:ii",1);

  $formulario -> nueva_tabla();
  $formulario -> texto ("Observaciones","textarea","obs",1,50,5,"","");

  
  $formulario -> nueva_tabla();
  $formulario -> botoni("Aceptar","aceptar_ins()",0);
  $formulario -> botoni("Borrar","form_ins.reset()",1);
  $formulario -> cerrar();
  
  //Para la carga del Popup
  echo '<div id="AplicationEndDIV"></div>
        <div id="popupDIV" style="position: absolute; left: 0px; top: 0px; width: 300px; height: 300px; z-index: 3; visibility: hidden; overflow: auto; border: 5px solid #333333; background: white; ">
          <div id="result" ></div>
        </div>
        ';

 }//FIN FUNCION FORMULARIO

//FUNCION INSERTAR

// *****************************************************

 function Insertar()
 {
  $fec_actual = date("Y-m-d H:i:s");
  $hor_actual = date("Y-m-d H:i:s");
  $fec_lleg = str_replace("/","-",$_REQUEST[fecpronov]);


  if($fec_lleg > $fec_actual)
  {
     $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">Intentar de Nuevo</a></b>";

     $mensaje =  "La Fecha de Llegada Debe ser Menor o Igual a la Fecha Actual".$link_a;
     $mens = new mensajes();
     $mens -> correcto("LLEGADA DE DESPACHOS",$mensaje);
     exit;

  }

   $query="SELECT  MAX(e.fec_noveda)
              FROM ".BASE_DATOS.".tab_despac_vehige c,".BASE_DATOS.".tab_despac_seguim d,
                   ".BASE_DATOS.".tab_despac_noveda e
             WHERE c.num_despac = d.num_despac AND
                   c.num_despac = e.num_despac AND
                   c.num_despac = '$_REQUEST[despac]' ";

   $consulta = new Consulta($query, $this -> conexion);
   //fecha del ultimo reporte
   $ultrep = $consulta -> ret_arreglo();


  if(isset($ultrep[0]))
  {
   if ($fec_lleg <= $ultrep[0])
   {
    $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">Intentar de Nuevo</a></b>";

     $mensaje =  "La Fecha de Llegada Debe ser Mayor a la Fecha de la Ultima Novedad".$link_a;
     $mens = new mensajes();
     $mens -> correcto("LLEGADA DE DESPACHOS",$mensaje);
     exit;
   }
  }


    //actualiza la hora de llegada del despacho
    $query="UPDATE ".BASE_DATOS.".tab_despac_despac
               SET fec_llegad = '$fec_lleg',
                   obs_llegad = '$_REQUEST[obs]',
		   usr_modifi = '".$_REQUEST[usuario]."',
		   fec_modifi = '".$fec_actual."'
             WHERE num_despac = '$_REQUEST[despac]' ";
   $consulta = new Consulta($query, $this -> conexion,"BR");

   //Consulta la ruta asignada al Despacho
   $query = "SELECT a.cod_rutasx,a.num_placax
                  FROM ".BASE_DATOS.".tab_despac_vehige a
                  WHERE a.num_despac = '$_REQUEST[despac]'
                   ";

   $consulta = new Consulta($query, $this -> conexion);
   $ruta_sad = $consulta -> ret_vector();

  //Manejo de la Interfaz Aplicaciones SAT
/*  $interfaz = new Interfaz_SAT(BASE_DATOS,NIT_TRANSPOR,$this -> usuario_aplicacion,$this -> conexion);

  if($interfaz -> totalact > 0)
  {
   for($i = 0; $i < $interfaz -> totalact; $i++)
   {
    $homolodespac = $interfaz -> getHomoloDespac($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$_REQUEST[despac]);

    if($homolodespac["DespacHomolo"] > 0)
    {
       $despac_ws["despac"] = $_REQUEST[despac];
       $despac_ws["fechax"] = $fec_lleg;
       $despac_ws["observ"] = $_REQUEST[obs];

       $resultado_ws = $interfaz -> insLlegad($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$despac_ws);

       if($resultado_ws["Confirmacion"] == "OK")
        $mensaje_sat .= "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\">La Llegada Fue Registrada Exitosamente en la Interfaz <b>".$interfaz -> interfaz[$i]["nombre"]."</b>.";
       else
        $mensaje_sat .= "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/error.gif\">Se Presento un Error al Insertar la Llegada en la Interfaz <b>".$interfaz -> interfaz[$i]["nombre"]."</b>. :: ".$resultado_ws["Confirmacion"];
    }
   }
  }
*/
  $query = "SELECT a.cod_transp
  		      FROM ".BASE_DATOS.".tab_despac_vehige a
  		     WHERE a.num_despac = ".$_REQUEST[despac]."
  		   ";

  $consulta = new Consulta($query, $this -> conexion);
  $transdes = $consulta -> ret_matriz();

  //Manejo de Interfaz GPS

  /*$interf_gps = new Interfaz_GPS();
  $interf_gps -> Interfaz_GPS_envio($transdes[0][0],BASE_DATOS,$_REQUEST[usuario],$this -> conexion);

  for($i = 0; $i  < $interf_gps -> cant_interf; $i++)
  {
        if($interf_gps -> getVehiculo($ruta_sad[1],$interf_gps -> cod_operad[$i][0],$transdes[0][0]))
        {
         $idgps = $interf_gps -> getIdGPS($ruta_sad[1],$interf_gps -> cod_operad[$i][0],$transdes[0][0]);

         if($interf_gps -> setLlegadGPS($interf_gps -> cod_operad[$i][0],$transdes[0][0],$ruta_sad[1],$idgps,$_REQUEST[despac]))
         {
            $mensaje_gps = "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\">Se Finalizo Seguimiento GPS Operador <b>".$interf_gps -> nom_operad[$i][0]."</b> Correctamente.";
         }
         else
         {
            $mensaje_gps = "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/error.gif\">Ocurrio un Error al Finalizar Seguimiento GPS Operador <b>".$interf_gps -> nom_operad[$i][0].".</b>";
         }
        }
  }
*/
  if($consulta = new Consulta("COMMIT", $this -> conexion))
  {
    $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">Insertar Otra Llegada</a></b>";


    //Quita el despacho en la central
    $consultaNit = "SELECT a.clv_filtro FROM ".BASE_DATOS.".tab_aplica_filtro_perfil a WHERE a.cod_perfil = ".$_SESSION['datos_usuario']['cod_perfil']." ";
    $nit = new Consulta($consultaNit, $this->conexion);
    $nit = $nit->ret_matriz();
    $nit = $nit[0]['clv_filtro'];
    if ($this->getInterfParame('85', $nit) == true)
    {
       
      require_once URL_ARCHIV_STANDA."/interf/app/APIClienteApp/controlador/DespachoControlador.php";
      $controlador = new DespachoControlador(); 
      $response = $controlador -> finalizar($this -> conexion ,  $_REQUEST["despac"], $nit);   
      $message = $response -> msg_respon; 
      $mensaje .= $message; 

      
      $mens = new mensajes();
      if ($response->cod_respon == 1000) {
        $mens->correcto("REGISTRO MOVIL", $mensaje);
      } else {
        $mens->advert("REGISTRO MOVIL", $mensaje);
      }
    }


    $mensaje = "Se Registro la Llegada del Despacho ".$_REQUEST["despac"]." Exitosamente.".$mensaje_sat.$mensaje_gps.$link_a;
    $mens = new mensajes();
    $mens -> correcto("LLEGADA DE DESPACHOS",$mensaje);
    
    /*******
    * 
    *  Se reporta llegada en Destino Seguro si la Transportadora tiene activa la interfaz
    * 
    * *****/
   
    $query = "SELECT b.nom_usuari, b.clv_usuari, a.cod_transp
  	      FROM ".BASE_DATOS.".tab_despac_vehige a,
               ".BASE_DATOS.".tab_interf_parame b
  	     WHERE a.num_despac = '".$_REQUEST[despac]."'
               AND a.cod_transp = b.cod_transp
               AND b.cod_operad = '35' ";

    $consulta = new Consulta($query, $this -> conexion);
    $datos_ds = $consulta -> ret_matriz();
    
    if( $datos_ds ) 
    {
      include( "kd_xmlrpc.php" );
      
      define( "XMLRPC_DEBUG", true );
      define( "SITEDS", "www.destinoseguro.net" );
      define( "LOCATIONDS", "/WS/server.php" );
      
      $query = "SELECT a.cod_manifi, b.num_placax 
  	      FROM ".BASE_DATOS.".tab_despac_despac a, 
               ".BASE_DATOS.".tab_despac_vehige b
  	     WHERE a.num_despac = '".$_REQUEST[despac]."'
           AND a.num_despac = b.num_despac";

      $consulta = new Consulta( $query, $this -> conexion );
      $despacho = $consulta -> ret_matriz();
      
      $datosDespac['usuario'] =     $datos_ds[0][0];
      $datosDespac['clave'] =       $datos_ds[0][1];
      $datosDespac['fecha'] =       date( "Y-m-d", strtotime( $fec_lleg ) );
      $datosDespac['hora'] =        date( "H:i:s", strtotime( $fec_lleg ) );
      $datosDespac['nittra'] =      $datos_ds[0][2];
      $datosDespac['manifiesto'] =  $despacho[0][0];
      $datosDespac['placa'] =       $despacho[0][1];
      $datosDespac['observacion'] = $_REQUEST[obs];
      
      //print_r($datosDespac);
      
      /* XMLRPC_prepare works on an array and converts it to XML-RPC parameters */
      list( $success, $response ) = XMLRPC_request
                                  ( SITEDS,
                                    LOCATIONDS,
                                    'wsds.DarLlegada',
                                    array( XMLRPC_prepare( $datosDespac ),
                                           'HarryFsXMLRPCClient' )
                                  );
      $mReturn = explode( "-", $response['faultString'] );
      
      if( 0 == $mReturn[0] )
      {
        $mMessage = "******** Encabezado ******** \n";
        $mMessage .= "Fecha y hora: ".date( "Y-m-d H:i" )." \n";
        $mMessage .= "Empresa de transporte: ".$datosDespac['nittra']." \n";
        $mMessage .= "Numero de manifiesto: ".$datosDespac['manifiesto']." \n";
        $mMessage .= "Placa del vehiculo: ".$datosDespac['placa']." \n";
        $mMessage .= "Observacion Dar llegada: ".$datosDespac['observacion']." \n";
        $mMessage .= "******** Detalle ******** \n";
        $mMessage .= "Codigo de error: ".$mReturn[1]." \n";
        $mMessage .= "Mesaje de error: ".$mReturn[2]." \n";
        //mail( "soporte.ingenieros@intrared.net", "Web service Trafico-Destino seguro", $mMessage,'From: soporte.ingenieros@intrared.net' );
      }
      //print_r($response);
    }
    /*******
       * 
       *  Fin Interfaz Destino Seguro
       * 
       * *****/


        //Quita el despacho en el integrador
      $query = "SELECT a.*, b.*
                  FROM ".BASE_DATOS.".tab_despac_despac a
            INNER JOIN ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac
                 WHERE a.num_despac = '".$_REQUEST['despac']."'";
      $mDataDespac = new Consulta($query, $this->conexion);
      $mDataDespac = $mDataDespac->ret_matriz(); 
      // validacion de interfaz con integrador GPS
      $mIntegradorGPS = getValidaInterfaz($this->conexion, '53', $mDataDespac[0]['cod_transp'], true, 'data');
      if( sizeof($mIntegradorGPS) > 0 )
      {
        if ($mIntegradorGPS['ind_operad'] == '3') // SOLO REPORTES UBICACION SI TIENE IND_OPERAD = 3 --> HUB
        {   
            $mHubGPS = new InterfHubIntegradorGPS($this->conexion, ['cod_transp' => $mDataDespac[0]['cod_transp']] );

            // Proceso de generar itinerario a placa del manifiesto---------------------------------------------------------------------------
            $mDesGPS = $mHubGPS -> setTrakingEnd([
                                                  'num_placax' => $mDataDespac[0]['num_placax'],
                                                  'num_docume' => $mDataDespac[0]['cod_manifi'],
                                                  'num_despac' => $mDataDespac[0]['num_despac'],
                                                  'fec_inicio' => date("Y-m-d H:i:s"),
                                                  'fec_finali' => date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")."+ 5 day ")),
                                                  'cod_itiner' => $mDataDespac[0]['cod_itiner']
                                                  ]);
            if($mDesGPS['code'] == '1000'){ 
                ShowMessage("s", "REGISTRO HUB GPS", $mDesGPS['message']);
            }
            else if($mDesGPS['code'] != '1000' && isset($mDesGPS) ){
                ShowMessage("e", "REGISTRO HUB GPS", $mDesGPS['message']);
            }
            // Fin proceso de generar itinerario HUB al despacho ---------------------------------------------------------------------------
        }
        else
        {
            include("../".DIR_APLICA_CENTRAL."/lib/InterfGPS.inc");
            $mInterfGps = new InterfGPS( $this->conexion ); 
            $mResp = $mInterfGps -> setPlacaIntegradorGPS( $_REQUEST['despac'], ['ind_transa' => 'Q'] );  
            $mens = new mensajes();
            if($mResp['code_resp'] == '1000'){
              $mens -> correcto("Envio despacho: ".$_REQUEST['despac']." con placa: ".$mDataDespac[0]['num_placax'],
                                "Este es un envio asincrono al integrador GPS<br><b>Respuesta:</b> ".$mResp['msg_resp']);
            } else {
              $mens -> error("Envio despacho: ".$_REQUEST['despac']." con placa: ".$mDataDespac[0]['num_placax'],
                             "Este es un envio asincrono al integrador GPS<br><b>Respuesta:</b> ".$mResp['msg_resp']);
            }
            unset($mResp);
        }
      }
  }

 }

  //---------------------------------------------
  /*! \fn: getInterfParame
   *  \brief:Verificar la interfaz con destino seguro esta activa
   *  \author: Nelson Liberato
   *  \date: 21/12/2015
   *  \date modified: 21/12/2015
   *  \return BOOL
   */
  function getInterfParame($mCodInterf = NULL, $nit = NULL) {
    $mSql = "SELECT ind_estado
                   FROM ".BASE_DATOS.".tab_interf_parame a
                  WHERE a.cod_operad = '".$mCodInterf."'
                    AND a.cod_transp = '".$nit."'";
    $mMatriz = new Consulta($mSql, $this->conexion);
    $mMatriz = $mMatriz->ret_matriz("a");
    return $mMatriz[0]['ind_estado'] == '1'?true:false;
  }

}//FIN CLASE PROC_DESPAC

   $proceso = new Proc_despac($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>