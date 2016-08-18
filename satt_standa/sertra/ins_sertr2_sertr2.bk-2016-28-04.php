
<?php
/****************************************************************************
NOMBRE:   MODULO CONFIGURAR TIPO DE SERVICIO DE LA TRANSPORTADORA
FUNCION:  CONFIGURAR TIPO SERVICIO TRANSP
AUTOR: HUGO MALAGON
FECHA CREACION : 20 OCTUBRE 2010
****************************************************************************/
class Proc_alerta
{
 var $conexion,
     $usuario;//una conexion ya establecida a la base de datos
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
 //echo $GLOBALS[opcion];
  if(!isset($GLOBALS[opcion]))
     $this -> FormularioBusqueda();
  else
     {
      switch($GLOBALS[opcion])
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
     }// FIN ELSE GLOBALS OPCION
 }//FIN FUNCION PRINCIPAL
 function Resultado()
 {
    //Codigo del Tercero.
    $cod_tercer = explode( "-" , $_POST[busq_transp] );
    $cod_tercer = trim( $cod_tercer[0] );
    
    //Nombre del Tercero.
    $nom_tercer = explode( "-" , $_POST[busq_transp] );
    $nom_tercer = trim( $nom_tercer[1] );
    
    if( $nom_tercer == '' )
      $nom_tercer = $cod_tercer;
   
   if( trim( $_POST[busq_transp] ) == "" )
   {
     //Lista todas las transportadoras
     $query = "SELECT a.cod_tercer, UPPER( a.nom_tercer ) AS nom_tercer, UPPER(a.abr_tercer) AS abr_tercer,
                      IF(c.num_consec IS NULL, 'NO', 'SI') AS ind_config
                FROM ".BASE_DATOS.".tab_tercer_activi b,
                     ".BASE_DATOS.".tab_tercer_tercer a LEFT JOIN 
                     ".BASE_DATOS.".tab_transp_tipser c ON a.cod_tercer = c.cod_transp 
               WHERE a.cod_estado = '1' AND
                     a.cod_tercer = b.cod_tercer AND
                     b.cod_activi = ".COD_FILTRO_EMPTRA. " 
                     GROUP BY 1
                     ORDER BY 2 ASC ";
     $consec = new Consulta($query, $this -> conexion);
     $matriz = $consec -> ret_matriz();
   }
   else
   {
     //Lista las transportadoras que coincidan con el nit dado
     $query = "SELECT a.cod_tercer, UPPER( a.nom_tercer ) AS nom_tercer, UPPER(a.abr_tercer) AS abr_tercer,
                      IF(c.num_consec IS NULL, 'NO', 'SI') AS ind_config
                FROM ".BASE_DATOS.".tab_tercer_activi b,
                     ".BASE_DATOS.".tab_tercer_tercer a LEFT JOIN 
                     ".BASE_DATOS.".tab_transp_tipser c ON a.cod_tercer = c.cod_transp 
               WHERE a.cod_estado = '1' AND
                     a.cod_tercer = b.cod_tercer AND
                     a.cod_tercer LIKE '%".$cod_tercer."%' AND
                     b.cod_activi = ".COD_FILTRO_EMPTRA. " 
                     GROUP BY 1
                     ORDER BY 2 ASC ";
     $consec = new Consulta($query, $this -> conexion);
     $matriz = $consec -> ret_matriz();
     if(!$matriz)
     {
       //Lista las transportadoras que coincidan con el nombre
       $query = "SELECT a.cod_tercer, UPPER( a.nom_tercer ) AS nom_tercer, UPPER(a.abr_tercer) AS abr_tercer,
                      IF(c.num_consec IS NULL, 'NO', 'SI') AS ind_config
                FROM ".BASE_DATOS.".tab_tercer_activi b,
                     ".BASE_DATOS.".tab_tercer_tercer a LEFT JOIN 
                     ".BASE_DATOS.".tab_transp_tipser c ON a.cod_tercer = c.cod_transp 
               WHERE a.cod_estado = '1' AND
                     a.cod_tercer = b.cod_tercer AND
                     a.nom_tercer LIKE '%".$nom_tercer."%' AND
                     b.cod_activi = ".COD_FILTRO_EMPTRA. " 
                     GROUP BY 1
                     ORDER BY 2 ASC ";
       $consec = new Consulta($query, $this -> conexion);
       $matriz = $consec -> ret_matriz();
       if( !$matriz )
       {
         //Lista las transportadoras que coincidan con la abreviatura
          $query = "SELECT a.cod_tercer, UPPER( a.nom_tercer ) AS nom_tercer, UPPER(a.abr_tercer) AS abr_tercer,
                      IF(c.num_consec IS NULL, 'NO', 'SI') AS ind_config
                FROM ".BASE_DATOS.".tab_tercer_activi b,
                     ".BASE_DATOS.".tab_tercer_tercer a LEFT JOIN 
                     ".BASE_DATOS.".tab_transp_tipser c ON a.cod_tercer = c.cod_transp 
               WHERE a.cod_estado = '1' AND
                     a.cod_tercer = b.cod_tercer AND
                     a.abr_tercer LIKE '%".$nom_tercer."%' AND
                     b.cod_activi = ".COD_FILTRO_EMPTRA. " 
                     GROUP BY 1
                     ORDER BY 2 ASC ";
         $consec = new Consulta($query, $this -> conexion);
         $matriz = $consec -> ret_matriz();
       }
       
     }
   }
   
   if( sizeof( $matriz ) == 1 )
   {
     //Si retorna 1 solo resultado se redirecciona hacia la captura final
     $GLOBALS[cod_transp] = $matriz[0][0];
     $this -> Formulario();
   }
   else
   {
     $datos_usuario = $this -> usuario -> retornar();
     $usuario=$datos_usuario["cod_usuari"];
  
     $formulario = new Formulario ("index.php","post","Configurar Tipo de Servicio de Transportadoras","form_item");
     $formulario -> linea("Se Encontro un Total de ".sizeof($matriz)." Transportadoras(s) para la b&uacute;squeda "." \" ".$_POST[busq_transp]." \" ",0,"t2");
     $formulario -> nueva_tabla();
  
     if(sizeof($matriz) > 0)
     {
        $formulario -> linea("NIT",0,"t");
        
        $formulario -> linea("Nombre",0,"t");
        
        $formulario -> linea("Abreviatura",0,"t");
        
        $formulario -> linea("Configurada",1,"t");
        
        for($i=0;$i<sizeof($matriz);$i++)
        {
          $matriz[$i][0]= "<a href=\"index.php?cod_servic=$GLOBALS[cod_servic]&window=central&cod_transp=".$matriz[$i][0]."&opcion=1 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";
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
     $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
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
		
		//Definicion de Estilos.
		echo "<style>
				.celda_titulo2
				{
					border-right:1px solid #AAA;
					font-size:12px;
					width:20%;
				}
				
				.celda_info
				{
					width:20%;
					text-align:center;
				}
				
				.campo
				{
					border:1px solid #CCC;		
					text-transform:uppercase;
				}
				
				.info
				{
					border:0px;		
					text-align:center;					
				}			
				
				.ui-autocomplete-loading 
				{ 
					background: white url('../".DIR_APLICA_CENTRAL."/estilos/images/ui-anim_basic_16x16.gif') right center no-repeat; 
				}	
				
				.ui-corner-all
				{
					cursor:pointer;
				}
				
				/*.ui-autocomplete 
				{
					max-height: 200px;
					height: 200px;
					overflow-y: auto;
				}*/
			  </style>";
        
        $query = "SELECT a.cod_tercer, UPPER( a.abr_tercer ) as abr_tercer
				  FROM ".BASE_DATOS.".tab_tercer_tercer a,
					   ".BASE_DATOS.".tab_tercer_activi b
				  WHERE a.cod_tercer = b.cod_tercer AND
						b.cod_activi = ".COD_FILTRO_EMPTRA."
				  ORDER BY 2";
		
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
    
		$formulario = new Formulario ( "index.php", "post", "TIPO DE SERVICIO", "formulario" );
		echo "<td>";
		$formulario -> oculto( "window","central",0 );
		$formulario -> oculto( "cod_servic", $GLOBALS[cod_servic],0 );
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
		
		
		
		$cod_transp = $GLOBALS['cod_transp'];
		$datos_usuario = $this -> usuario -> retornar();
		$usuario=$datos_usuario["cod_usuari"];
		
		$inicio[0][0] = 0;
		$inicio[0][1] = "-";	
		
		//Servidores.
		$query = "SELECT cod_server, nom_server
				  FROM ".CENTRAL.".tab_genera_server
				   WHERE ind_estado = '1' ";
		
		if( $_POST[cod_server] )
			$query .= " AND cod_server = '$_POST[cod_server]' ";
		
		$query .= " ORDER BY 2 ";
		 
		$consulta = new Consulta($query, $this -> conexion);
		$server = $consulta -> ret_matriz();
		
		if( !$_POST[cod_server] )
			$server = array_merge( $inicio, $server );
		else
			$server = array_merge( $server, $inicio );
   
		//Trae el nombre de la transportadora
		$query = "SELECT UPPER(abr_tercer)
				  FROM ".BASE_DATOS.".tab_tercer_tercer
				   WHERE cod_tercer = '".$cod_transp."' ";
		 
		$consulta = new Consulta($query, $this -> conexion);
		$nom_transp = $consulta -> ret_matriz();
   
		//trae array con los tipos de servicio para el combobox
		$query = "SELECT cod_tipser, nom_tipser
				  FROM ".BASE_DATOS.".tab_genera_tipser
				   WHERE ind_estado = '1' ";
		 
		$consulta = new Consulta($query, $this -> conexion);
		$tipser = $consulta -> ret_matriz();
		$tipser = array_merge( $inicio, $tipser );
    
    $tip_servic = array(0 => array( 0 => "1" ,1 => "Por Despacho"),1 => array( 0 => "2" ,1 => "Por Registro"));
    $tip_servic = array_merge( $inicio,$tip_servic);

		//Trae el ultimo tipo de servicio configurado para una transportadora
		$query = "SELECT MAX(num_consec) AS num_consec
				 FROM ".BASE_DATOS.".tab_transp_tipser a
				 WHERE a.cod_transp = '".$cod_transp."'";

		$consult = new Consulta($query, $this -> conexion);
		$matriz_consec = $consult -> ret_matriz();
  
		$lastConsec = $matriz_consec ? $matriz_consec [0][0] : FALSE ;
		
		if( $lastConsec )
		{
			//trae los datos de la ultima configuracion
			$query = "SELECT a.cod_tipser, a.tie_contro, a.ind_estado, 
                       a.tie_conurb, a.ind_llegad, a.cod_server, 
                       a.ind_notage, a.tip_factur, a.tie_carurb,
                       a.tie_carnac, a.tie_carimp, a.tie_carexp,
                       a.tie_desurb, a.tie_desnac, a.tie_desimp, 
                       a.tie_desexp
				   FROM ".BASE_DATOS.".tab_transp_tipser a
				   WHERE a.cod_transp = '".$cod_transp."' AND 
						 a.num_consec = '".$lastConsec."'";

			$consult = new Consulta($query, $this -> conexion);
			$matriz = $consult -> ret_matriz();		
	   
			if( $matriz )
			{
				$query = "SELECT cod_tipser, nom_tipser
					  FROM ".BASE_DATOS.".tab_genera_tipser
					   WHERE ind_estado = '1' AND 
							 cod_tipser = '".$matriz[0][0]."' ";

				$consulta = new Consulta($query, $this -> conexion);
				$tipser_sel = $consulta -> ret_matriz();
				$tipser = array_merge($tipser_sel, $tipser);
        
        if( $matriz[0][5] )
        {
          $query = "SELECT cod_server, nom_server
                      FROM ".CENTRAL.".tab_genera_server
                     WHERE ind_estado = '1'
                      AND cod_server = '".$matriz[0][5]."'";
      
          $consulta = new Consulta($query, $this -> conexion);
          $act_server = $consulta -> ret_matriz();
          
            $server = array_merge( $act_server, $server );
        }
        
        if( $matriz[0][7] &&  $matriz[0][7] != '0' )
        {
            $act_servic[0][0] = $matriz[0][7];
            $act_servic[0][1] = $matriz[0][7] == '1'? 'Por Despacho': 'Por Registro';	
            
            $tip_servic = array_merge( $act_servic, $tip_servic );
        }
        
			}
		}
		else
		{
			//Configuracion Activa al insertar
			$matriz[0][2] = 1;
		}
		
		
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/sertra2.js\"></script>\n";
		
		$formulario = new Formulario ( "index.php", "post", "Configurar Tipo de Servicio de Transportadoras", "ins_sertra" );
		
		$formulario -> linea( "Datos Básicos", 1, "t2" );
		
		$formulario -> nueva_tabla();
		$formulario -> linea( "Nombre Transportadora", 0, "t", NULL, NULL, 'right' );
		$formulario -> linea($nom_transp[0][0],1,"i");
		$formulario -> lista("Tipo Servicio ", "cod_tipser\" onChange=\"onChangeTipServic()", $tipser, 1);
		$formulario -> lista("Servidor: ", "cod_server", $server, 1);
		
		$tie_visibility = $matriz[0][0] == '1' || $matriz[0][0] == '' ? ' style="display:none"' : '';
		
		$formulario -> texto("Tiempo Para Despachos Nacionales(Min)","text","tie_contro\"  $tie_visibility id=\"tie_controID\" onChange=\" BlurNumeric(this);",1,2,3,"",$matriz[0][1]);
		$formulario -> texto("Tiempo Para Despachos Urbanos(Min)","text","tie_conurb\"  $tie_visibility id=\"tie_conurbID\" onChange=\" BlurNumeric(this);",1,2,3,"",$matriz[0][3]);
		
		if($matriz[0][4] == '1') $formulario -> caja ("LLegada Automatica:","ind_llegad","1",1,1);
		else $formulario -> caja ("LLegada Automatica:","ind_llegad","1",0,1);
		
		if($matriz[0][2] == '1') $formulario -> caja ("Activa:","ind_estado","1",1,1);
		else $formulario -> caja ("Activa:","ind_estado","1",0,1);
    
    if($matriz[0][6] == '1') $formulario -> caja ("Notificar Agencias:","ind_notage","1",1,1);
		else $formulario -> caja ("Notificar a correos por Agencia:","ind_notage","1",0,1);
    
    /***********************************************************************************************************************************************************************/
    
    $formulario -> lista("Tipo de Servicio: ", "tip_factur", $tip_servic, 1);
    /***********************************************************************************************************************************************************************/ 
    
    /**    SECCION TIEMPOS PENDIENTES CARGUE ***********************************************/
    $formulario -> nueva_tabla();
    $formulario -> linea("Configuración Tiempos Control de Cargue",1,"h");
    //a.,a., a., a. 
    $formulario -> nueva_tabla();
    $formulario -> texto("Despachos Urbanos(Min)", "text", "tie_carurb\" id=\"tie_carurbID\" onChange=\" BlurNumeric(this);", 1, 2, 3, "", $matriz[0][8] );
    $formulario -> texto("Despachos Nacionales(Min)", "text", "tie_carnac\" id=\"tie_carnacID\" onChange=\" BlurNumeric(this);", 1, 2, 3, "", $matriz[0][9] );
    $formulario -> texto("Despachos Importación(Min)", "text", "tie_carimp\" id=\"tie_carimpID\" onChange=\" BlurNumeric(this);", 1, 2, 3, "", $matriz[0][10] );
    $formulario -> texto("Despachos Exportación(Min)", "text", "tie_carexp\" id=\"tie_carexpID\" onChange=\" BlurNumeric(this);", 1, 2, 3, "", $matriz[0][11] );
    
    /***************************************************************************************/
    
    /**    SECCION TIEMPOS PENDIENTES DESCARGUECARGUE ***********************************************/
    $formulario -> nueva_tabla();
    $formulario -> linea("Configuración Tiempos Control de Descargue",1,"h");
    //a.,a., a., a. 
    $formulario -> nueva_tabla();
    $formulario -> texto("Despachos Urbanos(Min)", "text", "tie_desurb\" id=\"tie_desurbID\" onChange=\" BlurNumeric(this);", 1, 2, 3, "", $matriz[0][12] );
    $formulario -> texto("Despachos Nacionales(Min)", "text", "tie_desnac\" id=\"tie_desnacID\" onChange=\" BlurNumeric(this);", 1, 2, 3, "", $matriz[0][13] );
    $formulario -> texto("Despachos Importación(Min)", "text", "tie_desimp\" id=\"tie_desimpID\" onChange=\" BlurNumeric(this);", 1, 2, 3, "", $matriz[0][14] );
    $formulario -> texto("Despachos Exportación(Min)", "text", "tie_desexp\" id=\"tie_desexpID\" onChange=\" BlurNumeric(this);", 1, 2, 3, "", $matriz[0][15] );
    
    /***************************************************************************************/
    
    
		$formulario -> nueva_tabla();
		$formulario -> oculto("usuario","$usuario",0);
		$formulario -> oculto("window","central",0);
		$formulario -> oculto("cod_transp",$cod_transp,0);
		$formulario -> oculto("opcion",3,0);
		$formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],1);
		$formulario -> botoni("Aceptar","aceptar_insert() ",0);
		$formulario -> botoni("Volver","javascript:history.go(-1)",0);
		$formulario -> cerrar();
	}
	
	function Insertar()
 {
  // echo "<pre>";
  // print_r( $_REQUEST );
  // echo "</pre>";
  // die();
  
   $cod_transp = $GLOBALS['cod_transp'];
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];
   //trae el consecutivo de la tabla
   $query = "SELECT MAX(num_consec) AS num_consec
             FROM ".BASE_DATOS.".tab_transp_tipser a
             WHERE a.cod_transp = '".$cod_transp."'";
   $consec = new Consulta($query, $this -> conexion);
   $ultimo = $consec -> ret_matriz();
   $ultimo_consec = $ultimo[0][0];
   $nuevo_consec = $ultimo_consec+1;
   $GLOBALS[ind_estado] = $GLOBALS[ind_estado] == '1' ? '1' : '0';
   $GLOBALS[ind_llegad] = $GLOBALS[ind_llegad] == '1' ? '1' : '0';
   $GLOBALS[ind_notage] = $GLOBALS[ind_notage] == '1' ? '1' : '0';
   
   //query de insercion
   $query = "INSERT INTO ".BASE_DATOS.".tab_transp_tipser
             (num_consec, cod_transp, cod_tipser,
              tie_contro, ind_estado, ind_llegad, 
              fec_creaci, usr_creaci, tie_conurb,
              cod_server, ind_notage, tip_factur,
              tie_carurb, tie_carnac, tie_carimp,
              tie_carexp, tie_desurb, tie_desnac, 
              tie_desimp, tie_desexp)
             VALUES ('$nuevo_consec','$cod_transp','$GLOBALS[cod_tipser]', 
                     '$GLOBALS[tie_contro]', '$GLOBALS[ind_estado]', '$GLOBALS[ind_llegad]', 
                     NOW(), '".$usuario."', '$GLOBALS[tie_conurb]', 
                     '$GLOBALS[cod_server]', '$GLOBALS[ind_notage]','$GLOBALS[tip_factur]', 
                     '".$_REQUEST['tie_carurb']."', '".$_REQUEST['tie_carnac']."', '".$_REQUEST['tie_carimp']."', '".$_REQUEST['tie_carexp']."',
                     '".$_REQUEST['tie_desurb']."', '".$_REQUEST['tie_desnac']."', '".$_REQUEST['tie_desimp']."', '".$_REQUEST['tie_desexp']."'
                     ) ";
   $consulta = new Consulta($query, $this -> conexion,"BR"); 

   if($insercion = new Consulta("COMMIT", $this -> conexion))
    {
     $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">Configurar el Tipo de Servicio de Otra Transportadora</a></b>";

     $mensaje =  "Se Inserto el Tipo de Servicio para la transportadora <b>".$GLOBALS[cod_transp]."</b> con Exito".$link_a;
     $mens = new mensajes();
     $mens -> correcto("INSERTAR TIPO SERVICIO",$mensaje);
    }
 }


}//FIN CLASE Proc_alerta
     $proceso = new Proc_alerta($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);
?>
