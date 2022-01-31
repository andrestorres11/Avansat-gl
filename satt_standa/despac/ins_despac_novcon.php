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
          $this -> Formulario();
          break;
        case "2":
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

   $listado_prin = new Despachos($_REQUEST[cod_servic],1,$this -> cod_aplica,$this -> conexion);
   $listado_prin -> ListadoPrincipal($datos_usuario,0,"",0,NULL,NULL,0,0,1);
 }

  function Formulario()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

   //trae la fecha actual
   $fec_actual = date("d-m-Y");
   $hor_actual = date("H:i");
   $inicio[0][0] = 0;
   $inicio[0][1] = '-';

   //presenta por defecta la fecha actual
   if(!isset($_REQUEST[fecnov]))
      $_REQUEST[fecnov]=$fec_actual;
   if(!isset($_REQUEST[hornov]))
      $_REQUEST[hornov]=$hor_actual;


   //lista las novedades
   $query = "SELECT a.cod_noveda,if(a.ind_alarma = 'S',CONCAT(a.nom_noveda,' (Genera Alarma)'),a.nom_noveda),a.ind_tiempo
               FROM ".BASE_DATOS.".tab_genera_noveda a
              WHERE a.cod_noveda = '".$_REQUEST[novedad]."'
                       ORDER BY 2";

   $consulta = new Consulta($query, $this -> conexion);
   $novedades_a = $consulta -> ret_matriz();

    //lista las novedades
    $query = "SELECT cod_noveda,if(ind_alarma = 'S',CONCAT(nom_noveda,' (Genera Alarma)'),nom_noveda)
                FROM ".BASE_DATOS.".tab_genera_noveda
                        ORDER BY 2";

    $consulta = new Consulta($query, $this -> conexion);
    $novedades = $consulta -> ret_matriz();

   $query = "SELECT a.cod_contro,if(a.ind_virtua = '1',CONCAT(a.nom_contro,' (Virtual)'),a.nom_contro)
               FROM ".BASE_DATOS.".tab_genera_contro a
              WHERE a.cod_contro = '$_REQUEST[puesto]'";

   $consulta = new Consulta($query, $this -> conexion);
   $puestos_a = $consulta -> ret_matriz();

   //trae la ultima fecha de la novedad
   $query="SELECT b.fec_planea,a.fec_noveda,a.cod_contro
                        FROM ".BASE_DATOS.".tab_despac_noveda a,
                       ".BASE_DATOS.".tab_despac_seguim b,
                       ".BASE_DATOS.".tab_despac_vehige d
                      WHERE d.num_despac = '".$_REQUEST[despac]."' AND
                       d.num_despac = b.num_despac AND
                       a.num_despac = b.num_despac AND
                       a.cod_contro = b.cod_contro AND
                       a.fec_noveda = (SELECT MAX(c.fec_noveda)
                                         FROM ".BASE_DATOS.".tab_despac_noveda c
                                        WHERE c.num_despac = a.num_despac
                                      )
                ";

   $consulta = new Consulta($query, $this -> conexion);
   $maximo = $consulta -> ret_matriz();

   //trae la ultima fecha de la nota de controlador
   $query="SELECT b.fec_planea,a.fec_contro,a.cod_contro
                        FROM ".BASE_DATOS.".tab_despac_contro a,
                       ".BASE_DATOS.".tab_despac_seguim b,
                       ".BASE_DATOS.".tab_despac_vehige d
                      WHERE d.num_despac = '".$_REQUEST[despac]."' AND
                       d.num_despac = b.num_despac AND
                       a.num_despac = b.num_despac AND
                       a.cod_contro = b.cod_contro AND
                       a.fec_contro = (SELECT MAX(c.fec_contro)
                                         FROM ".BASE_DATOS.".tab_despac_contro c
                                        WHERE c.num_despac = a.num_despac
                                      )
                ";

   $consulta = new Consulta($query, $this -> conexion);
   $maximo_c = $consulta -> ret_matriz();

   $query = "SELECT if('".$maximo[0][1]."' > '".$maximo_c[0][1]."','1','0')
            ";

   $consulta = new Consulta($query, $this -> conexion);
   $compfecs = $consulta -> ret_matriz();

   if($compfecs[0][0])
   {
     $query="SELECT  b.cod_contro,if(b.ind_virtua = '1',CONCAT(b.nom_contro,' (Virtual)'),b.nom_contro)
              FROM ".BASE_DATOS.".tab_genera_contro b,
                   ".BASE_DATOS.".tab_despac_seguim d,
                   ".BASE_DATOS.".tab_despac_vehige e
             WHERE b.cod_contro = d.cod_contro AND
                   e.num_despac = d.num_despac AND
                   d.fec_planea > '".$maximo[0][0]."' AND
                   e.num_despac = '$_REQUEST[despac]' AND
                   d.cod_contro != ".$maximo[0][2]."
                   ";

      $query = $query." ORDER BY d.fec_planea ";
   }
   else
   {
     if($maximo_c[0][2])
     {
      $query = "SELECT a.cod_contro
     			  FROM ".BASE_DATOS.".tab_despac_noveda a
     			 WHERE a.num_despac = ".$_REQUEST[despac]." AND
     				   a.cod_contro = ".$maximo_c[0][2]."
     		   ";

      $consulta = new Consulta($query, $this -> conexion);
      $noinclui = $consulta -> ret_matriz();
     }
     else
      $noinclui = NULL;

     $query="SELECT b.cod_contro,if(b.ind_virtua = '1',CONCAT(b.nom_contro,' (Virtual)'),b.nom_contro)
              FROM ".BASE_DATOS.".tab_genera_contro b,
                   ".BASE_DATOS.".tab_despac_seguim d,
                   ".BASE_DATOS.".tab_despac_vehige e
             WHERE b.cod_contro = d.cod_contro AND
                   e.num_despac = d.num_despac AND
                   d.fec_planea >= '".$maximo_c[0][0]."' AND
                   e.num_despac = '$_REQUEST[despac]'
               ";

      if($noinclui)
       $query .= " AND d.cod_contro != ".$noinclui[0][0];

      $query = $query." ORDER BY d.fec_planea ";
   }

   $consulta = new Consulta($query, $this -> conexion);
   $matrizlink = $consulta -> ret_matriz();

   $titupc = "ANTES DE";

   if(!$matrizlink)
   {
    $query = "SELECT a.cod_contro
    		    FROM ".BASE_DATOS.".tab_despac_seguim a
    		   WHERE a.num_despac = ".$_REQUEST[despac]." AND
    		   		 a.fec_planea = (SELECT MAX(b.fec_planea)
    		   		 				   FROM ".BASE_DATOS.".tab_despac_seguim b
    		   		 				  WHERE a.num_despac = b.num_despac
    		   		 			    )
    		 ";

    $consulta = new Consulta($query, $this -> conexion);
    $ultcontr = $consulta -> ret_matriz();

    $query = "SELECT a.cod_contro,if(a.ind_virtua = '1',CONCAT(a.nom_contro,' (Virtual)'),a.nom_contro)
		        FROM ".BASE_DATOS.".tab_genera_contro a
	           WHERE a.cod_contro = ".$ultcontr[0][0]."
	     ";

    $consulta = new Consulta($query, $this -> conexion);
    $matrizlink = $consulta -> ret_matriz();

    $titupc = "POR LLEGADA";
   }

   $matrizlink = array_merge($puestos_a,$inicio,$matrizlink);
   $novedades = array_merge($novedades_a,$inicio,$novedades);

   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/desvirtual.js\"></script>\n";

   $formulario = new Formulario ("index.php","post","INFORMACION DEL DESPACHO","form_ins");

    if($novedades_a[0][2] == "1")
      $formulario -> oculto("soltie",1,0);
    else
      $formulario -> oculto("soltie",0,0);

    $formulario -> oculto("usa","$usuario",0);
    $formulario -> oculto("usuario","$usuario",0);
    $formulario -> oculto("tercero","$tercero",0);
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
    $formulario -> oculto("opcion",1,0);
    $formulario -> oculto("despac","$_REQUEST[despac]",0);

   $listado_prin = new Despachos($_REQUEST[cod_servic],2,$this -> cod_aplica,$this -> conexion);
   $listado_prin  -> Encabezado($_REQUEST[despac],$datos_usuario);

   $formulario -> nueva_tabla();
   $formulario -> linea("Informaci&oacute;n B&aacute;sica del Reporte",1,"h");

  $formulario -> nueva_tabla();
  $formulario -> lista($titupc,"puesto", $matrizlink,1);
  $formulario -> lista("NOVEDAD","novedad\" onChange=\"form_ins.submit()", $novedades,0);

  if($novedades_a[0][2] == "1")
   $formulario -> texto("Tiempo (min)","text","tiemp_adicis\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",1,4,4,"","");
  $formulario -> nueva_tabla();
  if($_REQUEST[novedad] == CONS_NOVEDA_PCLLEG)
  {
   echo "<div align = \"center\"><small><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/advertencia.gif\"><b>El Tiempo de Recalculo Para el Plan de Ruta con la Novedad Seleccionada, se Tomara Desde la Fecha de Entrada de Registro, m&aacute;s la Cantidad de Minutos.</small></div>";
  }
  $formulario -> texto("OBSERVACIONES","textarea","obs",1,50,5,"","");

  $formulario -> nueva_tabla();
  $formulario -> linea("Fecha/Hora de la Noveda",0,"t");
  $formulario -> linea(date("Y-m-d H:i"),1,"i");

  $formulario -> nueva_tabla();
  $formulario -> boton("Aceptar","button\" onClick=\"aceptar_ins()",0);
  $formulario -> boton("Borrar","reset",1);

  $formulario -> cerrar();
  
	//Para la carga del Popup
    echo '<tr><td><div id="AplicationEndDIV"></div>
          <div id="RyuCalendarDIV" style="position: absolute; background: white; left: 0px; top: 0px; z-index: 3; display: none; border: 1px solid black; "></div>
          <div id="popupDIV" style="position: absolute; left: 0px; top: 0px; width: 300px; height: 300px; z-index: 3; visibility: hidden; overflow: auto; border: 5px solid #333333; background: white; ">
            <div id="filtros" ></div>
            <div id="result" ></div>
      </div><div id="alg"><table></table></div></td></tr>';


 }//FIN FUNCION ACTUALIZAR

 function Insertar()
 {
  $fec_actual = date("Y-m-d H:i:s");

  $query = "SELECT a.cod_transp, a.cod_rutasx
    		  FROM ".BASE_DATOS.".tab_despac_vehige a
    		 WHERE a.num_despac = ".$_REQUEST[despac]."
    		 ";

  $consulta = new Consulta($query, $this -> conexion);
  $nitransp = $consulta -> ret_matriz();

  $regist["despac"] = $_REQUEST[despac];
  $regist["contro"] = $_REQUEST[puesto];
  $regist["noveda"] = $_REQUEST[novedad];
  $regist["tieadi"] = $_REQUEST[tiemp_adicis];
  $regist["observ"] = $_REQUEST[obs];
  $regist["fecact"] = $fec_actual;
  $regist["usuari"] = $_REQUEST[usuario];
  $regist["nittra"] = $nitransp[0][0];
  $regist["rutax"] = $nitransp[0][1];

  $consulta = new Consulta("SELECT NOW()", $this -> conexion,"BR");

  $transac_nov = new Despachos($_REQUEST[cod_servic],$_REQUEST[opcion],$this -> cod_aplica,$this -> conexion);
  $RESPON = $transac_nov -> InsertarNovedadNC(BASE_DATOS,$regist,0);

  $formulario = new Formulario ("index.php","post","INFORMACION DEL DESPACHO","form_ins");

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
    *  Se reporta la nota de controlador en Destino Seguro si la Transportadora tiene activa la interfaz
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
      $datosDespac['fecha'] =       date( "Y-m-d", strtotime( $fec_actual ) );
      $datosDespac['hora'] =        date( "H:i:s", strtotime( $fec_actual ) );
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
        $mMessage .= "Observacion Nota Controlador: ".$datosDespac['observacion']." \n";
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

   $formulario -> cerrar();

 }





}//FIN CLASE PROC_DESPAC



   $proceso = new Proc_despac($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);



?>