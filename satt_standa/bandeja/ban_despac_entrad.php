<?php

class Bandeja
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

      switch($GLOBALS[opcion])
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
   $GLOBALS_ADD[0]["valor"] = $GLOBALS[alacla];
   $GLOBALS_ADD[1]["campo"] = "totregif";
   $GLOBALS_ADD[1]["valor"] = $GLOBALS[totregif];
   $GLOBALS_ADD[atras]=$_GET[atras];
   $datos_usuario[bandeja] = TRUE;

   $listado_prin = new Despachos($GLOBALS[cod_servic],1,$this -> cod_aplica,$this -> conexion);
   $listado_prin -> ListadoPrincipal($datos_usuario,0,"",0,NULL,$GLOBALS_ADD);
 }


 function Datos()
 {
   $datos_usuario = $this -> usuario -> retornar();

   $formulario = new Formulario ("index.php","post","Informacion del Despacho","form_ins");

   $listado_prin = new Despachos($GLOBALS[cod_servic],2,$this -> cod_aplica,$this -> conexion);
   $listado_prin  -> Encabezado($GLOBALS[despac],$formulario,$datos_usuario);
   $listado_prin  -> PlanDeRuta($GLOBALS[despac],$formulario,1,0,0,$datos_usuario);

   $formulario -> nueva_tabla();
   $formulario -> oculto("opcion",$GLOBALS[opcion],0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);


	$formulario -> botoni("Atras","javascript:history.go(-1)",0);

   $formulario -> cerrar();

  //Para la carga del Popup
  echo '<div id="AplicationEndDIV"></div>
        <div id="popupDIV" style="position: absolute; left: 0px; top: 0px; width: 300px; height: 300px; z-index: 3; visibility: hidden; overflow: auto; border: 5px solid #333333; background: white; ">
          <div id="result" ></div>
        </div>
        ';

 }

 function Formulario1()
 {

   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

   //trae la fecha actual
   $fec_actual = date("d-m-Y");
   $hor_actual = date("H:i:s");

   //presenta por defecta la fecha actual
   if(!isset($GLOBALS[fecnov]))
      $GLOBALS[fecnov]=$fec_actual;
   if(!isset($GLOBALS[hornov]))
      $GLOBALS[hornov]=$hor_actual;

   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/noveda.js\"></script>\n";
   $formulario = new Formulario ("index.php","post","Informacion del Despacho","form_ins");

   $listado_prin = new Despachos($GLOBALS[cod_servic],3,$this -> cod_aplica,$this -> conexion,2);
   $listado_prin  -> Encabezado($GLOBALS[despac],$formulario,$datos_usuario);

   $inicio[0][0] = 0;
   $inicio[0][1] = '-';

   //lista las novedades
   $query = "SELECT cod_noveda,if(ind_alarma = 'S',CONCAT(nom_noveda,' (Genera Alarma)'),nom_noveda), ind_tiempo
               FROM ".BASE_DATOS.".tab_genera_noveda ";
   $consulta = new Consulta($query, $this -> conexion);
   $novedades = $consulta -> ret_matriz();

   $query = "SELECT cod_noveda,if(ind_alarma = 'S',CONCAT(nom_noveda,' (Genera Alarma)'),nom_noveda),obs_preted,ind_alarma
               FROM ".BASE_DATOS.".tab_genera_noveda
               WHERE cod_noveda = '".$GLOBALS[novedad]."'";
   $consulta = new Consulta($query, $this -> conexion);
   $novedades_a = $consulta -> ret_matriz();

   if($GLOBALS[novedad])
     $novedades = array_merge($novedades_a,$inicio,$novedades);
   else
     $novedades = array_merge($inicio,$novedades);

      //trae el indicador de solicitud tiempo en novedad
   $query = "SELECT ind_tiempo
               FROM ".BASE_DATOS.".tab_genera_noveda
               WHERE cod_noveda = '".$GLOBALS[novedad]."'";
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

                   c.num_despac = '$GLOBALS[despac]' ";



   $consulta = new Consulta($query, $this -> conexion);
   $ultrep = $consulta -> ret_matriz();

   $formulario -> nueva_tabla();
   $formulario -> linea("Fecha y Hora Novedad",1,"t2");

   $formulario -> nueva_tabla();
   if(!$GLOBALS[fecpronov])
    $feactual = date("Y-m-d H:i");
   else
    $feactual = $GLOBALS[fecpronov];

   $feactual = str_replace("/","-",$feactual);
   $formulario -> fecha_calendar("Fecha/Hora","fecpronov","form_ins",$feactual,"yyyy-mm-dd hh:ii",1);

   $formulario -> nueva_tabla();
   $formulario -> linea("Asignaci&oacute;n del Puesto de Control y Novedad",1,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("Puesto de Control",0,"t");
   $formulario -> linea($GLOBALS[pc],0,"i");

  $formulario -> lista("NOVEDAD","novedad\" onChange=\"form_ins.submit()\"", $novedades,0);
  if($ind_tiempo[0]){
  $formulario -> texto("TIEMPO DURACION ","text","tiem_duraci\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '0'){alert('La Cantidad No es Valida');this.value='';this.focus()}}else{this.value=''}\" id=\"duracion",1,4,4,"","");
  }

  if ( $novedades_a[0][3] == "S" )
  {
  	$mQuery = "SELECT a.cod_transp
                 FROM ".BASE_DATOS.".tab_despac_vehige a
                WHERE a.num_despac = ".$GLOBALS[despac]."
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

  					    "despac" => $GLOBALS[despac],
  					    "transp" => $mTranspor[0][0],
  					    "bdsata" => BASE_DATOS,
  					    "perfil" => $datos_usuario["cod_perfil"]
  				    );

      $listado_prin  -> getMailAssignedNov( $formulario, $mArrayValueMen );
    }
  }

  $formulario -> nueva_tabla();
  $formulario -> texto("Observaciones","textarea","obs",1,50,5,"",$novedades_a[0][2]);
  $formulario -> caja ("Habilitar Disponibilidad PAD:","habPAD",1,0,0);
  $formulario -> nueva_tabla();
  $formulario -> linea ("Fecha de la novedad (dd-mm-yyyy)",0,"t");
  $formulario -> linea ("".$GLOBALS[fecnov]."",1,"i");
  $formulario -> linea ("Hora de la novedad (HH:mm)",0,"t");
  $formulario -> linea ("<b>".$GLOBALS[hornov]."</b>",1,"i");
 //$formulario -> linea ("".$GLOBALS[fecnov]."",1,"i");


  $formulario -> nueva_tabla();
  $formulario -> oculto("usuario","$usuario",0);
  $formulario -> oculto("tercero","$tercero",0);

/*
  $formulario -> oculto("fecnov\" id=\"fecnovID","$fec_actual",0);
  $formulario -> oculto("hornov\" id=\"hornovID","$hor_actual",0);
  $formulario -> oculto("sitio\" id=\"sitioID","$GLOBALS[pc]",0);
  $formulario -> oculto("sit\" id=\"sitID","S",0);
*/
  $formulario -> oculto("fecnov","$fec_actual",0);
  $formulario -> oculto("hornov","$hor_actual",0);

  $formulario -> oculto("window","central",0);
  $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
  $formulario -> oculto("opcion",2,0);

  $formulario -> oculto("pc",$GLOBALS[pc],0);
  $formulario -> oculto("codpc",$GLOBALS[codpc],0);

  $formulario -> oculto("ultrep",$ultrep[0][0],0);
  if($ind_tiempo[0])
  $formulario -> oculto("tiem",1,0);
  else
  $formulario -> oculto("tiem",0,0);

  $formulario -> oculto("despac","$GLOBALS[despac]",0);

  $formulario -> nueva_tabla();

  $formulario -> botoni("Aceptar","aceptar_ins()",0);

  $formulario -> botoni("Borrar","form_ins.reset()",1);

  $formulario -> cerrar();
 }

 function Insertar()
 {
  $fec_actual = date("Y-m-d H:i:s");

  $regist["habPAD"] = $GLOBALS[habPAD];
  $regist["despac"] = $GLOBALS[despac];
  $regist["contro"] = $GLOBALS[codpc];
  $regist["noveda"] = $GLOBALS[novedad];
  $regist["tieadi"] = $GLOBALS[tiem_duraci];
  $regist["observ"] = $GLOBALS[obs];
  $regist["fecnov"] = $GLOBALS[fecpronov];
  $regist["fecact"] = $fec_actual;
  $regist["ultrep"] = $GLOBALS[ultrep];
  $regist["usuari"] = $GLOBALS[usuario];

  if ( $GLOBALS[AsignMen] )
   $regist["AsignMen"] = $GLOBALS[AsignMen];
  if ( $GLOBALS[AsignAdit] )
   $regist["AsignAdit"] = $GLOBALS[AsignAdit];

  $consulta = new Consulta("SELECT NOW()", $this -> conexion,"BR");

  $transac_nov = new Despachos($GLOBALS[cod_servic],$GLOBALS[opcion],$this -> cod_aplica,$this -> conexion);
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

	   //Verificar Check de Habilitación en PAD
	   if($regist["habPAD"]==1)
	   {
	  	//Consultar Ciudad y Placa para Consumo del WebService.	
	  	$mQueryDespac = "SELECT a.cod_ciudes, b.num_placax " .
	  					"FROM ".BASE_DATOS.".tab_despac_despac a, " .
	  							"".BASE_DATOS.".tab_despac_vehige b " .
	  					"WHERE a.num_despac = b.num_despac " .
	  						"AND a.num_despac = '".$regist["despac"]."'";
	  						
	   $mQueryDespac = new Consulta( $mQueryDespac, $this -> conexion );
	   $mDespac = $mQueryDespac -> ret_matriz();
	   
		//Ruta Web Service.
		$oSoapClient = new soapclient('http://dev.intrared.net/ap/dev/si/ws/server.wsdl', true);
		
		//Parametros Web Service.
		$parametros = array("inputs" => "num_placax:".$mDespac[0][1]."; cod_ciuori:".$mDespac[0][0], "aplica" => "pad", 
						"module" => "dispo", "action" => "insert", "cod_usuari" => "yecid.gomez", "clv_passwd" => "Gomez4022");
		
		//Consumo Web Service.
		$respuesta = $oSoapClient -> call("Processing",$parametros);
		$valor = explode(":",$respuesta);

	   //Mensaje de Respuesta.
	   $mensaje .= "<br><b>".$valor[2]."</b>";

	   }
	      
   $mensaje .= "<br><b><a href=\"index.php?cod_servic=".$this -> servic."&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">Volver al Listado Principal</a></b>";
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

}//FIN CLASE Bandeja



   $proceso = new Bandeja($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);



?>