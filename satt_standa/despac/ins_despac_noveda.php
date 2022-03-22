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
      switch($_REQUEST[opcion])
      {
        case "1":
          $this -> Datos();
          break;
        case "2":
          $this -> Formulario1();
          break;
        case "3":
          $this -> Insertar();
          break;
        default:
          $this -> Listar();
          break;
      }
 }


 function Listar()
 {
   $datos_usuario = $this -> usuario -> retornar();

   $GLOBALS_ADD[0]["campo"] = "alacla";
   $GLOBALS_ADD[0]["valor"] = $_REQUEST[alacla];
   $GLOBALS_ADD[1]["campo"] = "totregif";
   $GLOBALS_ADD[1]["valor"] = $_REQUEST[totregif];
   $GLOBALS_ADD[atras]=$_GET[atras];

   $listado_prin = new Despachos($_REQUEST[cod_servic],1,$this -> cod_aplica,$this -> conexion);
   $listado_prin -> ListadoPrincipal($datos_usuario,0,"",0,NULL,$GLOBALS_ADD);
 }


 function Datos()
 {
   $datos_usuario = $this -> usuario -> retornar();

   $formulario = new Formulario ("index.php","post","Informacion del Despacho","form_ins");

   $listado_prin = new Despachos($_REQUEST[cod_servic],2,$this -> cod_aplica,$this -> conexion);
   $listado_prin  -> Encabezado($_REQUEST[despac],$formulario,$datos_usuario);
   $listado_prin  -> PlanDeRuta($_REQUEST[despac],$formulario,1,0,0,$datos_usuario);

   $formulario -> nueva_tabla();
   $formulario -> oculto("opcion",$_REQUEST[opcion],0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
   $formulario -> botoni("Atras","javascript:history.go(-1)",0);
   $formulario -> cerrar();

	//Para la carga del Popup
    echo '<tr><td><div id="AplicationEndDIV"></div>
          <div id="RyuCalendarDIV" style="position: absolute; background: white; left: 0px; top: 0px; z-index: 3; display: none; border: 1px solid black; "></div>
          <div id="popupDIV" style="position: absolute; left: 0px; top: 0px; width: 300px; height: 300px; z-index: 3; visibility: hidden; overflow: auto; border: 5px solid #333333; background: white; ">
            <div id="filtros" ></div>
            <div id="result" ></div>
      </div><div id="alg"><table></table></div></td></tr>';
 }

 function Formulario1()
 {

   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

   //trae la fecha actual
   $fec_actual = date("d-m-Y");
   $hor_actual = date("H:i:s");

   //presenta por defecta la fecha actual
   if(!isset($_REQUEST[fecnov]))
      $_REQUEST[fecnov]=$fec_actual;
   if(!isset($_REQUEST[hornov]))
      $_REQUEST[hornov]=$hor_actual;

   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/noveda.js\"></script>\n";
   $formulario = new Formulario ("index.php","post","Informacion del Despacho","form_ins");

   $listado_prin = new Despachos($_REQUEST[cod_servic],3,$this -> cod_aplica,$this -> conexion,2);
   $listado_prin  -> Encabezado($_REQUEST[despac],$formulario,$datos_usuario);

   $inicio[0][0] = 0;
   $inicio[0][1] = '-';

   //lista las novedades
   $query = "SELECT cod_noveda,if(ind_alarma = 'S',CONCAT(nom_noveda,' (Genera Alarma)'),nom_noveda), ind_tiempo
               FROM ".BASE_DATOS.".tab_genera_noveda ";
   $consulta = new Consulta($query, $this -> conexion);
   $novedades = $consulta -> ret_matriz();

   $query = "SELECT cod_noveda,if(ind_alarma = 'S',CONCAT(nom_noveda,' (Genera Alarma)'),nom_noveda),obs_preted,ind_alarma
               FROM ".BASE_DATOS.".tab_genera_noveda
               WHERE cod_noveda = '".$_REQUEST[novedad]."'";
   $consulta = new Consulta($query, $this -> conexion);
   $novedades_a = $consulta -> ret_matriz();

   if($_REQUEST[novedad])
     $novedades = array_merge($novedades_a,$inicio,$novedades);
   else
     $novedades = array_merge($inicio,$novedades);

      //trae el indicador de solicitud tiempo en novedad
   $query = "SELECT ind_tiempo
               FROM ".BASE_DATOS.".tab_genera_noveda
               WHERE cod_noveda = '".$_REQUEST[novedad]."'";
   $consulta = new Consulta($query, $this -> conexion);
   $ind_tiempo = $consulta -> ret_arreglo();

   //trae el indicador de Security Question
   /*$query = "SELECT ind_secque, num_secque, por_secque
               FROM ".BASE_DATOS.".tab_config_parame";

   $consulta = new Consulta($query, $this -> conexion);
   $indsec = $consulta -> ret_arreglo();*/

   /*echo '<pre>';
   print_r($indsec);
   echo '</pre>';*/

   $query="SELECT  MAX(e.fec_noveda)

              FROM ".BASE_DATOS.".tab_despac_vehige c,".BASE_DATOS.".tab_despac_seguim d,

                   ".BASE_DATOS.".tab_despac_noveda e

             WHERE c.num_despac = d.num_despac AND

                   c.num_despac = e.num_despac AND

                   c.num_despac = '$_REQUEST[despac]' ";



   $consulta = new Consulta($query, $this -> conexion);
   $ultrep = $consulta -> ret_matriz();

   $formulario -> nueva_tabla();
   $formulario -> linea("Fecha y Hora Novedad",1,"t2");

   $formulario -> nueva_tabla();
   if(!$_REQUEST[fecpronov])
    $feactual = date("Y-m-d H:i");
   else
    $feactual = $_REQUEST[fecpronov];

   $feactual = str_replace("/","-",$feactual);
   $formulario -> fecha_calendar("Fecha/Hora","fecpronov","form_ins",$feactual,"yyyy-mm-dd hh:ii",1);

   $formulario -> nueva_tabla();
   $formulario -> linea("Asignaci&oacute;n del Puesto de Control y Novedad",1,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("Puesto de Control",0,"t");
   $formulario -> linea($_REQUEST[pc],0,"i");

  $formulario -> lista("NOVEDAD","novedad\" onChange=\"form_ins.submit()\"", $novedades,0);
  if($ind_tiempo[0]){
  $formulario -> texto("TIEMPO DURACION ","text","tiem_duraci\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '0'){alert('La Cantidad No es Valida');this.value='';this.focus()}}else{this.value=''}\" id=\"duracion",1,4,4,"","");
  }

  if ( $novedades_a[0][3] == "S" )
  {
  	$mQuery = "SELECT a.cod_transp
                 FROM ".BASE_DATOS.".tab_despac_vehige a
                WHERE a.num_despac = ".$_REQUEST[despac]."
              ";

    $mConsulta = new Consulta( $mQuery, $this -> conexion );
    $mTranspor = $mConsulta -> ret_matriz();

    $mQuery = "SELECT a.nom_transp
  			     FROM ".CENTRAL.".tab_mensaj_bdsata a
  			 	WHERE a.cod_transp = '".$mTranspor[0][0]."' AND
  			 		  a.nom_bdsata = '".BASE_DATOS."' AND
  			 		  a.ind_estado = '1'
  		   ";

    $mConsulta = new Consulta( $mQuery, $this -> conexion );
    $mActivaEnv = $mConsulta -> ret_matriz();

    if ( $mActivaEnv )
    {
      $mArrayValueMen = array (

  					    "despac" => $_REQUEST[despac],
  					    "transp" => $mTranspor[0][0],
  					    "bdsata" => BASE_DATOS,
  					    "perfil" => $datos_usuario["cod_perfil"]
  				    );

      $listado_prin  -> getMailAssignedNov( $formulario, $mArrayValueMen );
    }
  }

  $formulario -> nueva_tabla();
  //$formulario -> texto("Observaciones","textarea","obs",1,50,5,"",$novedades_a[0][2]);
  
  $formulario -> texto("Sitio Seguimiento","text","obs" ,30,30,30,"","");
  
  $formulario -> caja ("Habilitar Disponibilidad PAD:","habPAD",1,0,0);
  $formulario -> nueva_tabla();
  $formulario -> linea ("Fecha de la novedad (dd-mm-yyyy)",0,"t");
  $formulario -> linea ("".$_REQUEST[fecnov]."",1,"i");
  $formulario -> linea ("Hora de la novedad (HH:mm)",0,"t");
  $formulario -> linea ("<b>".$_REQUEST[hornov]."</b>",1,"i");
 //$formulario -> linea ("".$_REQUEST[fecnov]."",1,"i");


  $formulario -> nueva_tabla();
  $formulario -> oculto("usuario","$usuario",0);
  $formulario -> oculto("tercero","$tercero",0);
//  $formulario -> oculto("fecnov","$fec_actual",0);
//  $formulario -> oculto("hornov","$hor_actual",0);
	$formulario->oculto("fecnov\" id=\"fecnovID", "$fec_actual", 0);
  $formulario -> oculto("hornov\" id=\"hornovID","$hor_actual",0);
  $formulario -> oculto("window","central",0);
  $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
  $formulario -> oculto("opcion",2,0);

  $formulario -> oculto("pc",$_REQUEST[pc],0);
  $formulario -> oculto("codpc",$_REQUEST[codpc],0);

  $formulario -> oculto("ultrep",$ultrep[0][0],0);
  if($ind_tiempo[0])
  $formulario -> oculto("tiem",1,0);
  else
  $formulario -> oculto("tiem",0,0);

  $formulario -> oculto("despac","$_REQUEST[despac]",0);

  $formulario -> nueva_tabla();
  $formulario -> botoni("Aceptar","aceptar_ins()",0);
  $formulario -> botoni("Borrar","form_ins.reset()",1);
  $formulario -> cerrar();

	//Para la carga del Popup
    echo '<tr><td><div id="AplicationEndDIV"></div>
          <div id="RyuCalendarDIV" style="position: absolute; background: white; left: 0px; top: 0px; z-index: 3; display: none; border: 1px solid black; "></div>
          <div id="popupDIV" style="position: absolute; left: 0px; top: 0px; width: 300px; height: 300px; z-index: 3; visibility: hidden; overflow: auto; border: 5px solid #333333; background: white; ">
            <div id="filtros" ></div>
            <div id="result" ></div>
      </div><div id="alg"><table></table></div></td></tr>';


 }

 function Insertar()
 {
  $fec_actual = date("Y-m-d H:i:s");

  $regist["habPAD"] = $_REQUEST[habPAD];
  $regist["despac"] = $_REQUEST[despac];
  $regist["contro"] = $_REQUEST[codpc];
  $regist["noveda"] = $_REQUEST[novedad];
  $regist["tieadi"] = $_REQUEST[tiem_duraci];
  $regist["observ"] = $_REQUEST[obs];
  $regist["fecnov"] = $_REQUEST[fecpronov];
  $regist["fecact"] = $fec_actual;
  $regist["ultrep"] = $_REQUEST[ultrep];
  $regist["usuari"] = $_REQUEST[usuario];

  if ( $_REQUEST[AsignMen] )
   $regist["AsignMen"] = $_REQUEST[AsignMen];
  if ( $_REQUEST[AsignAdit] )
   $regist["AsignAdit"] = $_REQUEST[AsignAdit];

  $consulta = new Consulta("SELECT NOW()", $this -> conexion,"BR");

  $transac_nov = new Despachos($_REQUEST[cod_servic],$_REQUEST[opcion],$this -> cod_aplica,$this -> conexion);
  $RESPON = $transac_nov -> InsertarNovedadPC(BASE_DATOS,$regist,0);

  if($RESPON[0]["indica"])
  {
   $consulta = new Consulta ("COMMIT", $this -> conexion);

   $mensaje = $RESPON[0]["mensaj"];
   for($i = 1; $i < sizeof($RESPON); $i++)
   {
    if($RESPON[$i]["indica"])
     $mensaje .= "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\">".$RESPON[$i]["mensaj"];
    else
     $mensaje .= "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/error.gif\">".$RESPON[$i]["mensaj"];
   }
    /*******
    * 
    *  Se reporta la novedad en Destino Seguro si la Transportadora tiene activa la interfaz
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
      $datosDespac['fecha'] =       date( "Y-m-d", strtotime( $_REQUEST[fecpronov] ) );
      $datosDespac['hora'] =        date( "H:i:s", strtotime( $_REQUEST[fecpronov] ) );
      $datosDespac['nittra'] =      $datos_ds[0][2];
      $datosDespac['manifiesto'] =  $despacho[0][0];
      $datosDespac['placa'] =       $despacho[0][1];
      $datosDespac['observacion'] = $_REQUEST[obs];
      
      //print_r($datosDespac);
      
      /* XMLRPC_prepare works on an array and converts it to XML-RPC parameters */
      list( $success, $response ) = XMLRPC_request
                                  ( SITEDS,
                                    LOCATIONDS,
                                    'wsds.InsertarSeguimiento',
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
        $mMessage .= "Observacion Novedad: ".$datosDespac['observacion']." \n";
        $mMessage .= "******** Detalle ******** \n";
        $mMessage .= "Codigo de error: ".$mReturn[1]." \n";
        $mMessage .= "Mesaje de error: ".$mReturn[2]." \n";
        mail( "soporte.ingenieros@intrared.net", "Web service Trafico-Destino seguro", $mMessage,'From: soporte.ingenieros@intrared.net' );
      }
      //print_r($response);
    }
    /*********
       * 
       *  Fin Interfaz Destino Seguro
       * 
       * ****/
      
	   //Verificar Check de Habilitacion en PAD
	   if($regist["habPAD"]==1)
	   {
	      $mQueryDespac = "SELECT a.cod_ciudes, b.num_placax, b.cod_transp, a.cod_manifi, a.num_despac 
                                       FROM ".BASE_DATOS.".tab_despac_despac a,  
                                            ".BASE_DATOS.".tab_despac_vehige b  
                                      WHERE a.num_despac = b.num_despac AND 
                                            a.num_despac = '" . $regist["despac"] . "'";

        $mQueryDespac = new Consulta($mQueryDespac, $this->conexion);
        $mDespac = $mQueryDespac->ret_matriz('a');

        include( "../".DIR_APLICA_CENTRAL."/lib/InterfPad.inc"); 
        $mInterfPad = new TraficoPad( $this->conexion );
        $mSetRecursosPAD = $mInterfPad -> SetDataRecursos( $mDespac[0] );
        $mensaje .= "<br>Interfaz PAD: ".$mSetRecursosPAD[1];

	   }
	      
   $mensaje .= "<br><b><a href=\"index.php?cod_servic=".$this -> servic."&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">Volver al Listado Principal</a></b>";
   $mens = new mensajes();
   $mens -> correcto("REGISTRO DE NOVEDADES",$mensaje);
  }
  else
  {
	   $mensaje = $RESPON[0]["mensaj"];
	   $mens = new mensajes();
	   $mens -> advert("REGISTRO DE NOVEDADES",$mensaje);
  }
 }

}//FIN CLASE PROC_DESPAC
   $proceso = new Proc_despac($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>
