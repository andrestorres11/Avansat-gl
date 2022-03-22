
<?php
/*******************************************************************************************
NOMBRE:   MODULO HOMOLOGAR PUESTOS FISICOS DE LA APLICACION DEL CLIENTE CON LAS EAL DE SATT
FUNCION:  HOMOLOGAR EAL
AUTOR: HUGO MALAGON
FECHA CREACION : 11 OCTUBRE 2011
********************************************************************************************/
class Proc_alerta
{
 var $conexion,
     $usuario,
     $errorWs;
     //una conexion ya establecida a la base de datos
    //Metodos
 function __construct($co, $us, $ca)
 {
  $this -> conexion = $co;
  $this -> usuario = $us;
  $this -> cod_aplica = $ca;
  $datos_usuario = $this -> usuario -> retornar();
  $this -> principal();
 }
//********METODOS
 function principal()
 {
  if(!isset($_REQUEST["opcion"]))
     $this -> FormularioBusqueda();
  else
     {
      switch($_REQUEST["opcion"])
       {
        case "1":
          $this -> Formulario();
          break;
        case "2":
          $this -> Insertar();
          break;
        case "3":
          $this -> Resultado();
          break;
       }//FIN SWITCH
     }// FIN ELSE _REQUEST OPCION
 }//FIN FUNCION PRINCIPAL
 function Resultado()
 {
    //Codigo del Tercero.
    $cod_tercer = explode( "-" , $_POST["busq_transp"] );
    $cod_tercer = trim( $cod_tercer[0] );
    
    //Nombre del Tercero.
    $nom_tercer = explode( "-" , $_POST["busq_transp"] );
    $nom_tercer = trim( $nom_tercer[1] );
    
    if( $nom_tercer == '' )
      $nom_tercer = $cod_tercer;
   
   if( trim( $_POST["busq_transp"] ) == "" )
   {
     //Lista todas las transportadoras
     $query = "SELECT a.cod_tercer, UPPER( a.nom_tercer ) AS nom_tercer, 
                      UPPER( a.abr_tercer ) AS abr_tercer, IF( d.cod_tercer IS NULL, 'NO', 'SI' ) AS ind_config
                FROM ".BASE_DATOS.".tab_tercer_activi b,
                     ".BASE_DATOS.".tab_transp_tipser c,
                     ".BASE_DATOS.".tab_tercer_tercer a LEFT JOIN
                     ".BASE_DATOS.".tab_homolo_ealxxx d ON a.cod_tercer = d.cod_tercer
               WHERE a.cod_estado = '1'
                 AND a.cod_tercer = b.cod_tercer
                 AND b.cod_activi = ".COD_FILTRO_EMPTRA. " 
                 AND a.cod_tercer = c.cod_transp
                 AND c.num_consec = ( SELECT MAX( z.num_consec ) AS num_consec FROM ".BASE_DATOS.".tab_transp_tipser z WHERE z.cod_transp = a.cod_tercer )
                 AND c.cod_tipser IN ( '1', '3' )
            GROUP BY a.cod_tercer
            ORDER BY 2 ASC ";
     $consec = new Consulta($query, $this -> conexion);
     $matriz = $consec -> ret_matriz();
   }
   else
   {
     //Lista las transportadoras que coincidan con el nit dado
     $query = "SELECT a.cod_tercer, UPPER( a.nom_tercer ) AS nom_tercer, 
                      UPPER( a.abr_tercer ) AS abr_tercer, IF( d.cod_tercer IS NULL, 'NO', 'SI' ) AS ind_config
                FROM ".BASE_DATOS.".tab_tercer_activi b,
                     ".BASE_DATOS.".tab_transp_tipser c,
                     ".BASE_DATOS.".tab_tercer_tercer a LEFT JOIN
                     ".BASE_DATOS.".tab_homolo_ealxxx d ON a.cod_tercer = d.cod_tercer
               WHERE a.cod_estado = '1'
                 AND a.cod_tercer = b.cod_tercer
                 AND a.cod_tercer LIKE '%".$cod_tercer."%'
                 AND b.cod_activi = ".COD_FILTRO_EMPTRA. " 
                 AND a.cod_tercer = c.cod_transp
                 AND c.num_consec = ( SELECT MAX( z.num_consec ) AS num_consec FROM ".BASE_DATOS.".tab_transp_tipser z WHERE z.cod_transp = a.cod_tercer )
                 AND c.cod_tipser IN ( '1', '3' )
            GROUP BY a.cod_tercer
            ORDER BY 2 ASC ";
            
     $consec = new Consulta($query, $this -> conexion);
     $matriz = $consec -> ret_matriz();
     if(!$matriz)
     {
       //Lista las transportadoras que coincidan con el nombre
       $query = "SELECT a.cod_tercer, UPPER( a.nom_tercer ) AS nom_tercer, 
                      UPPER( a.abr_tercer ) AS abr_tercer, IF( d.cod_tercer IS NULL, 'NO', 'SI' ) AS ind_config
                FROM ".BASE_DATOS.".tab_tercer_activi b,
                     ".BASE_DATOS.".tab_transp_tipser c,
                     ".BASE_DATOS.".tab_tercer_tercer a LEFT JOIN
                     ".BASE_DATOS.".tab_homolo_ealxxx d ON a.cod_tercer = d.cod_tercer
               WHERE a.cod_estado = '1'
                 AND a.cod_tercer = b.cod_tercer
                 AND a.nom_tercer LIKE '%".$nom_tercer."%'
                 AND b.cod_activi = ".COD_FILTRO_EMPTRA. " 
                 AND a.cod_tercer = c.cod_transp
                 AND c.num_consec = ( SELECT MAX( z.num_consec ) AS num_consec FROM ".BASE_DATOS.".tab_transp_tipser z WHERE z.cod_transp = a.cod_tercer )
                 AND c.cod_tipser IN ( '1', '3' )
            GROUP BY a.cod_tercer
            ORDER BY 2 ASC ";
            
       $consec = new Consulta($query, $this -> conexion);
       $matriz = $consec -> ret_matriz();
       if( !$matriz )
       {
         //Lista las transportadoras que coincidan con la abreviatura
         $query = "SELECT a.cod_tercer, UPPER( a.nom_tercer ) AS nom_tercer, 
                      UPPER( a.abr_tercer ) AS abr_tercer, IF( d.cod_tercer IS NULL, 'NO', 'SI' ) AS ind_config
                FROM ".BASE_DATOS.".tab_tercer_activi b,
                     ".BASE_DATOS.".tab_transp_tipser c,
                     ".BASE_DATOS.".tab_tercer_tercer a LEFT JOIN
                     ".BASE_DATOS.".tab_homolo_ealxxx d ON a.cod_tercer = d.cod_tercer
               WHERE a.cod_estado = '1'
                 AND a.cod_tercer = b.cod_tercer
                 AND a.abr_tercer LIKE '%".$nom_tercer."%'
                 AND b.cod_activi = ".COD_FILTRO_EMPTRA. " 
                 AND a.cod_tercer = c.cod_transp
                 AND c.num_consec = ( SELECT MAX( z.num_consec ) AS num_consec FROM ".BASE_DATOS.".tab_transp_tipser z WHERE z.cod_transp = a.cod_tercer )
                 AND c.cod_tipser IN ( '1', '3' )
            GROUP BY a.cod_tercer
            ORDER BY 2 ASC ";
            
         $consec = new Consulta($query, $this -> conexion);
         $matriz = $consec -> ret_matriz();
       }
       
     }
   }
   
   if( sizeof( $matriz ) == 1 )
   {
     //Si retorna 1 solo resultado se redirecciona hacia la captura final
     $_REQUEST["cod_transp"] = $matriz[0][0];
     $this -> Formulario();
   }
   else
   {
     $datos_usuario = $this -> usuario -> retornar();
     $usuario=$datos_usuario["cod_usuari"];
  
     $formulario = new Formulario ("index.php","post","Configurar Tipo de Servicio de Transportadoras","form_item");
     $formulario -> linea("Se Encontro un Total de ".sizeof($matriz)." Transportadoras(s) para la b&uacute;squeda "." \" ".$_POST["busq_transp"]." \" ",0,"t2");
     $formulario -> nueva_tabla();
  
     if(sizeof($matriz) > 0)
     {
        $formulario -> linea("NIT",0,"t");
        
        $formulario -> linea("Nombre",0,"t");
        
        $formulario -> linea("Abreviatura",0,"t");
        
        $formulario -> linea("Homologada",1,"t");
        
        for($i=0;$i<sizeof($matriz);$i++)
        {
          $matriz[$i][0]= "<a href=\"index.php?cod_servic=$_REQUEST[cod_servic]&window=central&cod_transp=".$matriz[$i][0]."&opcion=1 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";
          $formulario -> linea($matriz[$i][0],0,"i");
          $formulario -> linea($matriz[$i][1],0,"i");
          $formulario -> linea($matriz[$i][2],0,"i");
          $formulario -> linea($matriz[$i][3],1,"i");
        }
  
     }
  
     $formulario -> nueva_tabla();
     $formulario -> oculto("usuario","$usuario",0);
     $formulario -> oculto("opcion",1,0);
     $formulario -> oculto("valor",$valor,0);
     $formulario -> oculto("window","central",0);
     $formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],0);
     $formulario -> botoni("Volver","javascript:history.go(-1)",0);
  
     $formulario -> cerrar();
   }
 }

  function FormularioBusqueda()
  {
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
    
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/es.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/time.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";
    
    
    
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/regnov.js\"></script>\n";
    echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";
    echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/homolo.css' type='text/css'>";

       
        $query = "SELECT a.cod_tercer, UPPER( a.abr_tercer ) AS abr_tercer
                FROM ".BASE_DATOS.".tab_tercer_activi b,
                     ".BASE_DATOS.".tab_transp_tipser c,
                     ".BASE_DATOS.".tab_tercer_tercer a
               WHERE a.cod_estado = '1'
                 AND a.cod_tercer = b.cod_tercer
                 AND b.cod_activi = ".COD_FILTRO_EMPTRA. " 
                 AND a.cod_tercer = c.cod_transp
                 AND c.num_consec = ( SELECT MAX( z.num_consec ) AS num_consec FROM ".BASE_DATOS.".tab_transp_tipser z WHERE z.cod_transp = a.cod_tercer )
                 AND c.cod_tipser IN( '1', '3' )
            GROUP BY a.cod_tercer
            ORDER BY 2 ASC ";

    $consulta = new Consulta( $query, $this -> conexion );
    $transpor = $consulta -> ret_matriz();
    
    echo '
    <script>
    $(function() {
      var tranportadoras = 
      [';

      if( $transpor )
      {
        echo "\"Ninguna\"";
        foreach( $transpor as $row )
        {
          echo ", \"$row[cod_tercer] - $row[abr_tercer]\"";
        }
      };

    echo ']
      $( "#busq_transp" ).autocomplete({
        source: tranportadoras,
        delay: 100
      }).bind( "autocompleteclose", function(event, ui){$("#form_insID").submit();} );
      
      $( "#busq_transp" ).bind( "autocompletechange", function(event, ui){$("#form_insID").submit();} ); 
      });

    </script>';
    
    $formulario = new Formulario ( "index.php", "post", "HOMOLOGAR EAL", "formulario" );
    echo "<td>";
    $formulario -> oculto( "window","central",0 );
    $formulario -> oculto( "cod_servic", $_REQUEST["cod_servic"],0 );
    $formulario -> oculto( "opcion",3,0 );
    echo "<td></tr>";
    echo "<tr>";
    echo "<table width='100%' border='0' class='tablaList' align='center' cellspacing='0' cellpadding='0'>";
    echo "<tr>";
    echo "<td class='celda_titulo2' style='padding:4px;' width='100%' colspan='4' >B&uacute;squeda de Transportadora</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td class='celda_titulo' style='padding:4px;' width='50%' colspan='2' align='right' >
          Nit / Nombre: </td>";
    echo "<td class='celda_titulo' style='padding:4px;' width='50%' colspan='2' >
          <input class='campo_texto' type='text'  
          size='25' name='busq_transp' id='busq_transp' onblur='formulario.submit()' /></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td  class='celda_etiqueta' style='padding:4px;' align='center' colspan='4' >
          <input class='crmButton small save' style='cursor:pointer;' type='button' value='Buscar' onclick='formulario.submit()'/></td>";
    echo "</tr>";
    echo "</table></td>";
    $formulario -> cerrar();
  }


  function Formulario()
 {

   $cod_transp = $_REQUEST['cod_transp'];
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];
   
   $inicio[0][0] = 0;
   $inicio[0][1] = "-";
   
   //Trae el nombre de la transportadora
   $query = "SELECT UPPER(abr_tercer)
              FROM ".BASE_DATOS.".tab_tercer_tercer
               WHERE cod_tercer = '".$cod_transp."' ";
     
   $consulta = new Consulta($query, $this -> conexion);
   $nom_transp = $consulta -> ret_matriz();
   
   $query = "SELECT b.nom_tipser
               FROM ".BASE_DATOS.".tab_transp_tipser a,
                    ".BASE_DATOS.".tab_genera_tipser b
              WHERE a.cod_tipser = b.cod_tipser
                AND a.num_consec = ( SELECT MAX( z.num_consec ) AS num_consec 
                                      FROM ".BASE_DATOS.".tab_transp_tipser z 
                                     WHERE z.cod_transp = '".$cod_transp."' ) ";
   $consec = new Consulta($query, $this -> conexion);
   $nom_tipser = $consec -> ret_matriz();
   $nom_tipser = $nom_tipser[0]['nom_tipser'];
     
   $pcxfars = $this -> getControsFaro();
   $puestosHomologados = $this -> getControsHomologados( $cod_transp );

   $pcxclis = $this -> getControsCliente( $cod_transp );
   
   if( $this -> errorWs && !$pcxclis )
   {
     $mens = new mensajes();
     $mens -> error( "HOMOLOGAR EAL", $this -> errorWs );
     die();
   }
   
   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/sertra.js\"></script>\n";
   echo "<style>
           select:focus{
             background:#DEFCE2
           }
         </style>\n";
   $formulario = new Formulario ("index.php","post","Homologar EAL ","form_item");
   $formulario -> linea("Datos Básicos",1,"t2");
   $formulario -> nueva_tabla();
   $formulario -> linea("Nombre Transportadora",0,"t",NULL,NULL,'right');
   $formulario -> linea($nom_transp[0][0],1,"i");
   $formulario -> linea("Tipo Servicio",0,"t",NULL,NULL,'right');
   $formulario -> linea($nom_tipser,1,"i");
   $formulario -> nueva_tabla();

   if( !isset( $html ))
    $html = '';

   //salto de linea
   $html .= '<tr>';
   $html .= '<td colspan="5">';
   
   //div recargable
   $html .= '<div id="div_controsID">';
   $html .= '<table>';
   //Cabecera de la grilla
   $html .= $this -> getCabecera();
   
   $numFilas = count( $pcxclis );
   
   for ($j = 1; $j <= $numFilas; $j++)
   {
     //Se muestran las filas
     $html .= $this -> getRow( $j, $puestosHomologados, $pcxclis, $pcxfars );
   }

   $html .= '</table>';
   $html .= '</div>';
   $html .= '</td>';
   $html .= '</tr>';
   
   echo $html;
   
   $formulario -> oculto("val_totalx\" id=\"val_totalxID","$numFilas",0);
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_transp",$cod_transp,0);
   $formulario -> oculto("nom_transp",$nom_transp[0][0],0);
   $formulario -> oculto("opcion",3,0);
   $formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],1);
   $formulario -> botoni("Aceptar","aceptar_insert_eal() ",0);
   $formulario -> botoni("Volver","javascript:history.go(-1)",0);
   $formulario -> cerrar();
 }//FIN FUNCTION CAPTURA


 function Insertar()
 {
   $insercion = new Consulta( "START TRANSACTION", $this -> conexion );
   
   $query = "DELETE FROM ".BASE_DATOS.".tab_homolo_ealxxx 
                  WHERE cod_tercer = '".$_POST['cod_transp']."'";
 
   $consulta = new Consulta( $query, $this -> conexion, "R" );
   
   $query = "INSERT INTO ".BASE_DATOS.".tab_homolo_ealxxx
              (
                cod_tercer, cod_pcxcli, cod_pcxfar, ind_estado
              )VALUES";
   for( $j = 1; $j <= $_POST["val_totalx"]; $j++ )
   {
     //Se guardan solamente los puestos seleccionados
     $_POST["ind_estado".$j] = $_POST["ind_estado".$j] == 'on' ? '1' : '0';
     if( $_POST['cod_pcxfar'.$j] != 0 )
       $query .= "('".$_POST['cod_transp']."', '".$_POST["cod_pcxcli$j"]."', '".$_POST["cod_pcxfar$j"]."', '".$_POST["ind_estado$j"]."'),";
   }
   $query = substr( $query, 0, strlen( $query ) - 1 );
    
   $consulta = new Consulta( $query, $this -> conexion, "R" );
    
   if($insercion = new Consulta( "COMMIT", $this -> conexion ) )
   {
     $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">Homologar los puestos de control fisicos de otra transportadora</a></b>";

     $mensaje =  "Se realiz&oacute; la homologaci&oacute;n de los puestos de control fisicos para la transportadora <b>".$_REQUEST[cod_transp]." - ".$_REQUEST[nom_transp]."</b> con &Eacute;xito".$link_a;
     $mens = new mensajes();
     $mens -> correcto("HOMOLOGAR EAL",$mensaje);
   }
 }
 
 function zeroFill( $valor )
 {
   if( $valor > 99 )
     $zeroLength = 3;
   else
     $zeroLength = 2;
   return str_pad( $valor, $zeroLength, "0", STR_PAD_LEFT );
 }
 
 function getPcFaro( $puestosHomologados, $cod_pcxcli )
 {
   $ret = FALSE;
   for( $i = 0, $total = count( $puestosHomologados ); $i< $total; $i++ )
   {
     if( $puestosHomologados[$i]['cod_pcxcli'] == $cod_pcxcli )
     {
       $ret['cod_pcxfar'] = $puestosHomologados[$i]['cod_pcxfar'];
       $ret['ind_estado'] = $puestosHomologados[$i]['ind_estado'];
     }
   }
   return $ret;
 }
 function getRow( $index, $puestosHomologados, $pcxclis, $pcxfars )
 {
   $html .= '<tr>';
   //<------- Puesto de control cliente ------->
   $html .= '<td width="30%" align="right" class="celda_titulo">Puesto de Control Cliente '.$this -> zeroFill( $index );
   $html .= '<input type="hidden" name="cod_pcxcli'.$index.'" id="cod_pcxcli'.$index.'" value="'.$pcxclis[$index - 1]['cod_contro'].'" />';
   $html .= '</td>';
   $html .= '<td width="30%" class="celda_info" id="nom_pcxcli'.$index.'" >';
   $html .= $pcxclis[$index - 1]['nom_contro'];
   $html .= '</td>';
   
   //<------- Puestos SATT ------->
   $html .= '<td width="30%" class="celda_info">';
   $html .= '<select name="cod_pcxfar'.$index.'" id="cod_pcxfar'.$index.'" onchange="checkStatusEAL('.$index.')" class="form_01">';
   $html .= '<option value="0">-</option>';
   $selectedPcFaro = $this -> getPcFaro( $puestosHomologados, $pcxclis[$index - 1]['cod_contro'] );
   foreach( $pcxfars as $pcxfar )
   {
     $selected = $selectedPcFaro['cod_pcxfar'] == $pcxfar['cod_contro'] ? ' selected="selected" ' : '';
     $html .= '<option value="'.$pcxfar['cod_contro'].'" '.$selected.' >'.$pcxfar['nom_contro'].'</option>';
   }
   $html .= '</select>';
   $html .= '</td>';
   
   //<------- Estado ------->
   $checked = $selectedPcFaro['ind_estado'] == '1' || $numFilas == 1 ? ' checked="checked" ' : '';
   $html .= '<td width="10%" class="celda"><input type="checkbox" name="ind_estado'.$index.'" id="ind_estado'.$index.'" '.$checked.'/></td>';
   
   $html .= '</tr>';
   return $html;
 }
 
 function getControsFaro()
 {
   //trae array con los puestos de control fisicos activos en satt
   $query = "SELECT cod_contro, UPPER( CONCAT( IF( ind_virtua = '1', CONCAT( nom_contro, ' (Virtual)' ), CONCAT( nom_contro, ' (Fisico)' ) ), IF( ind_urbano = '".COD_ESTADO_ACTIVO."', ' - (Urbano)', '' ) ) ) AS nom_contro
                FROM ".BASE_DATOS.".tab_genera_contro
               WHERE cod_contro != ".CONS_CODIGO_PCLLEG."
                 AND ind_estado = '1'
                 AND ind_virtua = '0'
               ORDER BY 2 ASC";
      
   $consulta = new Consulta( $query, $this -> conexion );
   $contros = $consulta -> ret_matriz();
   return $contros;
 }
 /********************************************************************************************
 * Funcion consulta los puestos fisicos de la aplicacion del cliente                         *
 * @fn getControscliente                                                                     *
 * @return $pcxclis: array de los puestos de control                                         *
 ********************************************************************************************/
 function getControsCliente( $cod_transp )
 {
   $ret = FALSE;
   //Se verifica si tiene configurada (activa o inactiva) la interfaz con FARO 
   $mSql = "SELECT nom_usuari, clv_usuari, nom_operad
              FROM ".BASE_DATOS.".tab_interf_parame 
             WHERE cod_operad = '50' 
               AND cod_transp = '".$cod_transp."'";
   $consulta = new Consulta( $mSql, $this -> conexion );
   $parameIntCli = $consulta -> ret_matriz( 'a' );
   
   $data['nom_proces'] = 'Webservice - Consultar Puestos Fisicos Cliente';
   $data['cod_tercer'] = $cod_transp;
   $data['nom_aplica'] = $parameIntCli[0]['nom_operad'];

   if( count( $parameIntCli ) > 0 )
   {
     $mExecute = TRUE;
     //Ruta Web Service.
  
     /*
     $oSoapClient = new soapclient( 'https://localhost:444/ap/interf/app/sat/wsdl/sat.wsdl', true );
     //$oSoapClient = new soapclient( 'https://dev.intrared.net/ap/interf/app/sat/wsdl/sat.wsdl', true );
     $oSoapClient -> soap_defencoding = 'ISO-8859-1';
     $mResult = $oSoapClient -> call( "aplicaExists", array( "nom_aplica" => $parameIntCli[0]['nom_operad'] ) );
  
     $mResult = explode( "; ", $mResult );
     $mCodResp = explode( ":", $mResult[0] );
     if( 1000 != $mCodResp[1] )
     {
       unset( $oSoapClient, $mResult, $mCodResp );
  
       $oSoapClient = new soapclient( 'https://server.intrared.net:444/ap/interf/app/sat/wsdl/sat.wsdl', true );
       $oSoapClient -> soap_defencoding = 'ISO-8859-1';
       $mResult = $oSoapClient -> call( "aplicaExists", array( "nom_aplica" => $parameIntCli[0]['nom_operad'] ) );
       $mResult = explode( "; ", $mResult );
       $mCodResp = explode( ":", $mResult[0] );
    
       if( 1000 != $mCodResp[1] )
       {
         unset( $oSoapClient, $mResult, $mCodResp );
         $oSoapClient = new soapclient( 'https://flired.intrared.net:444/ap/interf/app/sat/wsdl/sat.wsdl', true );
         $oSoapClient -> soap_defencoding = 'ISO-8859-1';
         $mResult = $oSoapClient -> call( "aplicaExists", array( "nom_aplica" => $parameIntCli[0]['nom_operad'] ) );
         $mResult = explode( "; ", $mResult );
         $mCodResp = explode( ":", $mResult[0] );
         $mMsgResp = explode( ":", $mResult[1] );
  
         if( 1000 != $mCodResp[1] )
           $mExecute = FALSE;
       }
       
     }
     */


      try
      {
        $query = "SELECT a.url_webser	
                    FROM ".BD_STANDA.".tab_genera_server a,
                       ".BASE_DATOS.".tab_transp_tipser b
                    WHERE a.cod_server = b.cod_server AND
                      b.cod_transp = '$cod_transp' 
                    ORDER BY b.fec_creaci DESC ";

        $consulta = new Consulta( $query, $this -> conexion );
        $url_webser = $consulta -> ret_matriz();
        $url_webser =  $url_webser[0][0];//URL DEL WSDL.
								
        $oSoapClient = new soapclient( $url_webser, array( 'encoding'=>'ISO-8859-1' ) );

        $mParams = array( "nom_aplica" => $parameIntCli[0]['nom_operad'] );

        $mResult = $oSoapClient -> __call( "aplicaExists", $mParams );

        //Procesa el resultado del WS
        $mResult = explode( "; ", $mResult );
        $mCodResp = explode( ":", $mResult[0] );

        if( 1000 != $mCodResp[1] )
        {
          $mExecute = FALSE;
        }
        else
        {
          $mExecute = TRUE;
        }
      }
      catch( SoapFault $e )
      {
          $mExecute = FALSE;
      }
         
      //--------------------------------------


//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@


  
     if( $mExecute ) 
     {
        /*
        $aParametros = array( "nom_usuari" => $parameIntCli[0]['nom_usuari'], 
                           "pwd_clavex" => $parameIntCli[0]['clv_usuari'],
                           "nom_aplica" => $parameIntCli[0]['nom_operad'] );

        $pcxclis = $oSoapClient -> call ( "getPuestosFisicos", $aParametros );
        */

        //--------------------------------------------------
        try
        {
          //----------------------------
          $mResult = NULL;

          $aParametros = array( "nom_usuari" => $parameIntCli[0]['nom_usuari'], 
                                 "pwd_clavex" => $parameIntCli[0]['clv_usuari'],
                                 "nom_aplica" => $parameIntCli[0]['nom_operad'] );

          $pcxclis = $oSoapClient -> __call( "getPuestosFisicos", $aParametros );
          //----------------------------

          if( !is_array( $pcxclis ) )
          {
            //Procesa el resultado del WS
            $mResult = explode( "; ", $pcxclis );
            $mCodResp = explode( ":", $mResult[0] );
            $mMsgResp = explode( ":", $mResult[1] );
            $data['cod_errorx'] = $mCodResp[1];

            if( "1000" != $mCodResp[1] )
            {
              //Notifica Errores retornados por el WS
              $data['error'] = $data['nom_proces'].': '.$mMsgResp[1];
              $this -> errorWs = $data['error'];
              $this -> sendError( $data );
            }
          }
          else
            $ret = $pcxclis;
        }
        catch( SoapFault $e )
        {
          //----------
          $error = $e -> faultstring;
          if ( $error ) 
          {
            // Notifica errores soap
            $data['error'] = $data['nom_proces'].': '.$error;
          }
          elseif ( $e -> fault )
          {
            //Notifica Fallos
            $data['error'] = $data['nom_proces'].': '.$e -> faultcode.':'.$e -> faultdetail.':'.$e -> faultstring;
          }
          //----------
          
          $this -> errorWs = $data['error'];
          $this -> sendError( $data );
        }
        //--------------------------------------------------
     }
     else
     {
       $data['error'] = "La aplicación ".$parameIntCli[0]['nom_operad']." no fue encontrada en ningun servidor";
       $this -> errorWs = $data['error'];
       $this -> sendError( $data );
     }
   }
   else
   {
     $data['error'] = "La transportadora no tiene configurada la Interfaz. Por favor realice la solicitud con el área de soporte.";
     $this -> errorWs = $data['error'];
     $this -> sendError( $data );
   }
   return $ret;
 }

 function sendError( $data )
 {
   $mMessage = "******** Encabezado ******** \n";
   $mMessage .= "Operacion: ".$data['nom_proces']." \n";
   $mMessage .= "Empresa de transporte: ".$data['cod_tercer']."\n";
   $mMessage .= "Aplicacion: ".$data['nom_aplica']." \n";
   $mMessage .= "Fecha: ".date('Y-m-d H:i:s')." \n";
   $mMessage .= "******** Detalle ******** \n";
   $mMessage .= "Codigo de error: ".$data['cod_errorx']." \n";
   $mMessage .= "Mensaje de error: ".$data['error']." \n";
   mail( 'faroavansat@eltransporte.com, soporte.ingenieros@intrared.net', $data['nom_proces'], $mMessage,'From: soporte.ingenieros@intrared.net' );
   //mail( 'hugo.malagon@intrared.net', $data['nom_proces'], $mMessage,'From: soporte.ingenieros@intrared.net' );
 }
 
 function getControsHomologados( $cod_transp )
 {
   //Trae los puestos de control que tiene contratados la transportadora
   $query = "SELECT a.cod_pcxcli, a.cod_pcxfar, a.ind_estado
               FROM ".BASE_DATOS.".tab_homolo_ealxxx a
              WHERE a.cod_tercer = '".$cod_transp."'";
   $consulta = new Consulta( $query, $this -> conexion );
   $puestosHomologados = $consulta -> ret_matriz();
   return $puestosHomologados;
 }
 
 function getCabecera()
 {
   $html .= '<tr>';
   $html .= '<td width="30%" align="left" class="celda_titulo"><b>&nbsp;</b></td>';
   $html .= '<td width="30%" align="left" class="celda_titulo"><b>&nbsp;</b></td>';
   $html .= '<td width="30%" align="left" class="celda_titulo"><b>Puesto En Sat Trafico</b></td>';
   $html .= '<td width="10%" align="left" class="celda_titulo"><b>Estado</b></td>';
   $html .= '</tr>';
   return $html;
 }

}//FIN CLASE Proc_alerta
     $proceso = new Proc_alerta($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);
?>
