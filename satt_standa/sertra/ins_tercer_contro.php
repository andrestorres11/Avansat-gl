<?php
/****************************************************************************
NOMBRE:   MODULO CONFIGURAR TIPO DE SERVICIO DE LA TRANSPORTADORA
FUNCION:  CONFIGURAR TIPO SERVICIO TRANSP
AUTOR: HUGO MALAGON
FECHA CREACION : 20 OCTUBRE 2010
****************************************************************************/
session_start();
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
  $this -> principal();
 }
//********METODOS
 function principal()
 {
 
 ini_set("memory_limit", "128M");
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
        case "addpc":
          $this -> AgregarPC();
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
     $query = "SELECT a.cod_tercer, UPPER( a.nom_tercer ) AS nom_tercer, UPPER(a.abr_tercer) AS abr_tercer
                FROM ".BASE_DATOS.".tab_tercer_activi b,
                     ".BASE_DATOS.".tab_tercer_tercer a
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
     $query = "SELECT a.cod_tercer, UPPER( a.nom_tercer ) AS nom_tercer, UPPER(a.abr_tercer) AS abr_tercer
                FROM ".BASE_DATOS.".tab_tercer_activi b,
                     ".BASE_DATOS.".tab_tercer_tercer a
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
       $query = "SELECT a.cod_tercer, UPPER( a.nom_tercer ) AS nom_tercer, UPPER(a.abr_tercer) AS abr_tercer
                FROM ".BASE_DATOS.".tab_tercer_activi b,
                     ".BASE_DATOS.".tab_tercer_tercer a
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
          $query = "SELECT a.cod_tercer, UPPER( a.nom_tercer ) AS nom_tercer, UPPER(a.abr_tercer) AS abr_tercer
                FROM ".BASE_DATOS.".tab_tercer_activi b,
                     ".BASE_DATOS.".tab_tercer_tercer a 
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
        
        $formulario -> linea("Abreviatura",1,"t");
        
        for($i=0;$i<sizeof($matriz);$i++)
        {
          $matriz[$i][0]= "<a href=\"index.php?cod_servic=$GLOBALS[cod_servic]&window=central&cod_transp=".$matriz[$i][0]."&opcion=1 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";
          $formulario -> linea($matriz[$i][0],0,"i");
          $formulario -> linea($matriz[$i][1],0,"i");
          $formulario -> linea($matriz[$i][2],1,"i");
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
			});

		});
		</script>';
    
		$formulario = new Formulario ( "index.php", "post", "Asignar Puestos de Control Transportadoras", "formulario" );
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
    ini_set("memory_limit", "128M");
   $cod_transp = $GLOBALS['cod_transp'];
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];
   
   $inicio[0][0] = 0;
   $inicio[0][1] = "-";
   
   //Trae el nombre de la transportadora
   $query = "SELECT UPPER(abr_tercer) AS nom_transp
              FROM ".BASE_DATOS.".tab_tercer_tercer
               WHERE cod_tercer = '".$cod_transp."' ";
   
   $consulta = new Consulta( $query, $this -> conexion );
   $nom_transp = $consulta -> ret_matriz();
   $nom_transp = $nom_transp[0]['nom_transp'];
   
   //Trae los puestos de control que tiene contratados la transportadora
   $puestosContratados = $this -> getControsContratados( $cod_transp );
   
   $numFilas = count( $puestosContratados ) == 0 ? 1 : count( $puestosContratados );
   
   //trae array con los puestos de control activos
   $contros = $this -> getContros();
    
   //trae array con los Operadores
   $operadores = $this -> getOperadores();
   
   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/sertra.js\"></script>\n";
   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";
   $formulario = new Formulario ("index.php","post","Asignar Puestos Control Transportadora","ins_tercer_contro");
   $formulario -> linea("Datos Básicos",1,"t2");
   $formulario -> nueva_tabla();
   $formulario -> linea("Nombre Transportadora",0,"t",NULL,NULL,'right');
   $formulario -> linea($nom_transp,1,"i");

   //salto de linea
   $html .= '<tr>';
   $html .= '<td colspan="5">';
   
   //div recargable
   $html .= '<div id="div_controsID">';
   $html .= '<table>';
   //Cabecera de la grilla
   $html .= $this -> getCabecera();
   for ($j = 1; $j <= $numFilas; $j++)
   {
     //Se muestran las filas
     $selectedArray['cod_contro'] = $puestosContratados[$j-1]['cod_contro'];
     $selectedArray['cod_operad'] = $puestosContratados[$j-1]['cod_operad'];
     $selectedArray['ind_estado'] = $puestosContratados[$j-1]['ind_estado'];
     $html .= $this ->getRow( $j, $selectedArray, $contros, $operadores );
   }

   $html .= '</table>';
   $html .= '</div>';
   $html .= '</td>';
   $html .= '</tr>';
   
   //Se imprime el boton Agregar
   $html .= '<tr>';
   $html .= '<td align="center" colspan="5">';
   $html .= '<input type="button" onclick="DrawGridPC(\'\',\'add\')" value="Otro" name="+ P/C" class="crmButton small save">';
   $html .= '</td>';
   $html .= '</tr>';
   
   echo $html;
   
   $formulario -> nueva_tabla();

   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_transp",$cod_transp,0);
   $formulario -> oculto("nom_transp",$nom_transp,0);
   $formulario -> oculto("dir_aplica_central",DIR_APLICA_CENTRAL,0);
   $formulario -> oculto("url_archiv",'ins_tercer_contro.php',0);
   $formulario -> oculto("num_filas",$numFilas,0);
   $formulario -> oculto("opcion",3,0);
   $formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],1);
   $formulario -> botoni("Aceptar","aceptar_tercer_contro()",0);
   $formulario -> botoni("Volver","javascript:history.go(-1)",0);
   $formulario -> cerrar();
 }//FIN FUNCTION CAPTURA

  function AgregarPC()
  {
    @session_start();
    include( "../lib/general/conexion_lib.inc" );
    include( "../lib/general/tabla_lib.inc" );
    include( "../lib/general/constantes.inc" );
    define('BASE_DATOS', $_SESSION['BASE_DATOS']);
    $this -> conexion = new Conexion( "bd10.intrared.net:3306", $_SESSION[USUARIO], $_SESSION[CLAVE], BASE_DATOS );
 
    $filas = $_POST["filas"];

    if ( $_POST["row"] )
    {
      $filas++;
    }
    $i = 1;

    $inicio[0][0] = 0;
    $inicio[0][1] = "-";
    
    //trae array con los puestos de control activos
    $contros = $this -> getContros();
    
    //trae array con los Operadores
    $operadores = $this -> getOperadores();
   
    $html .= '<table>';
    //Cabecera de la grilla
    $html .= $this -> getCabecera();
    for ($j = 1; $j <= $filas; $j++)
    {
      if ( $j != $_POST["row"] )
      {
        //Se muestran las filas
        $selectedArray['cod_contro'] = $_POST["cod_contro$i"];
        $selectedArray['cod_operad'] = $_POST["cod_operad$i"];
        $selectedArray['ind_estado'] = $_POST["ind_estado$i"];
        $html .= $this -> getRow( $i, $selectedArray, $contros, $operadores );
        $i++;
      }
    }
    $html .= '</table>';
    
    echo $html;
  }
 function Insertar()
 {
   $insercion = new Consulta("START TRANSACTION", $this -> conexion);
   
   $query = "DELETE FROM ".BASE_DATOS.".tab_tercer_contro 
                  WHERE cod_tercer = '".$_POST['cod_transp']."'";
 
   $consulta = new Consulta($query, $this -> conexion,"R");
   
   $query = "INSERT INTO ".BASE_DATOS.".tab_tercer_contro
              (
                cod_tercer, cod_contro, cod_operad, ind_estado
              )VALUES";
    for( $j = 1; $j <= $_POST["num_filas"]; $j++ )
    {
      //Se guardan solamente los amparos que estén seleccionados
      $_POST["ind_estado".$j] = $_POST["ind_estado".$j] == 'on' ? '1' : '0';
      $query .= "('".$_POST['cod_transp']."', '".$_POST["cod_contro$j"]."', '".$_POST["cod_operad$j"]."', '".$_POST["ind_estado$j"]."'),";
    }
    $query = substr( $query, 0, strlen( $query ) - 1 );
    
    $consulta = new Consulta($query, $this -> conexion,"R");
    
   if($insercion = new Consulta("COMMIT", $this -> conexion))
   {
     $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">Configurar Los puestos de control contratados de Otra Transportadora</a></b>";

     $mensaje =  "Se realiz&oacute; la asignaci&oacute;n de los puestos de control para la transportadora <b>".$GLOBALS[cod_transp]." - ".$GLOBALS[nom_transp]."</b> con &Eacute;xito".$link_a;
     $mens = new mensajes();
     $mens -> correcto("ASIGNAR PUESTOS DE CONTROL",$mensaje);
   }
 }
  function getCabecera()
  {
    $html .= '<tr>';
    $html .= '<td width="20%" align="left" class="celda_titulo"><b>&nbsp;</b></td>';
    $html .= '<td width="20%" align="left" class="celda_titulo"><b>&nbsp;</b></td>';
    $html .= '<td width="20%" align="left" class="celda_titulo"><b>Operador</b></td>';
    $html .= '<td width="20%" align="left" class="celda_titulo"><b>Estado</b></td>';
    $html .= '<td width="20%" align="left" class="celda_titulo"><b>&nbsp;</b></td>';
    $html .= '</tr>';
    return $html;
  }
 
 function getRow( $index, $selectedArray, $contros, $operadores )
 {
   $html .= '<tr>';
   //<------- Puesto de control ------->
   $html .= '<td width="20%" align="right" class="celda_titulo">Puesto de Control '.$index.'</td>';
   $html .= '<td width="20%" class="celda_info">';
   $html .= '<select onchange="onChangeCodContro(this)" name="cod_contro'.$index.'" id="cod_contro'.$index.'"  class="form_01">';
   $html .= '<option value="0">-</option>';
   foreach( $contros as $contro )
   {
     $selected = $selectedArray['cod_contro']==$contro['cod_contro'] ? ' selected="selected" ': '';
     $html .= '<option value="'.$contro[0].'" '.$selected.' >'.htmlentities($contro['nom_contro']).'</option>';
   }
   $html .= '</select>';
   $html .= '</td>';
   
   //<------- Operadores ------->
   $html .= '<td width="20%" class="celda_info">';
   $html .= '<select name="cod_operad'.$index.'" id="cod_operad'.$index.'"  class="form_01">';
   $html .= '<option value="0">-</option>';
   foreach( $operadores as $operador )
   {
     $selected = $selectedArray['cod_operad'] == $operador['cod_operad'] ? ' selected="selected" ' : '';
     $html .= '<option value="'.$operador['cod_operad'].'" '.$selected.' >'.$operador['nom_operad'].'</option>';
   }
   $html .= '</select>';
   $html .= '</td>';
   
   //<------- Estado ------->
   $checked = $selectedArray['ind_estado'] == '1' || $numFilas == 1 ? ' checked="checked" ' : '';
   $html .= '<td width="20%" class="celda"><input type="checkbox" name="ind_estado'.$index.'" id="ind_estado'.$index.'" '.$checked.'/></td>';
   
   //<------- Boton borrar ------->
   $html .= '<td width="20%" class="celda_info">';
   $html .= '<input type="button" onclick="DrawGridPC(\''.$index.'\',\'drop\')" value="Borrar" name="Borrar" class="crmButton small cancel">';
   $html .= '</td>';
   
   $html .= '</tr>';
   return $html;
 }
 
 function getContros()
 {
   //trae array con los puestos de control activos
   $query = "SELECT cod_contro, UPPER( CONCAT( IF( ind_virtua = '1', CONCAT( nom_contro, ' (Virtual)' ), CONCAT( nom_contro, ' (Fisico)' ) ), IF( ind_urbano = '".COD_ESTADO_ACTIVO."', ' - (Urbano)', '' ) ) ) AS nom_contro
                FROM ".BASE_DATOS.".tab_genera_contro
               WHERE cod_contro != ".CONS_CODIGO_PCLLEG." AND
                     ind_estado = '1' 
                     AND ind_virtua = '0'
               ORDER BY 2 ASC";
      
   $consulta = new Consulta( $query, $this -> conexion );
   $contros = $consulta -> ret_matriz();
   return $contros;
 }
 
 function getOperadores()
 {
   //trae array con operadores activos
   $query = "SELECT cod_operad, UPPER( nom_operad ) AS nom_operad
                FROM ".BASE_DATOS.".tab_operad_trafic
               WHERE ind_estado = '1' 
               ORDER BY 2 ASC";
      
   $consulta = new Consulta( $query, $this -> conexion );
   $operadores = $consulta -> ret_matriz();
   return $operadores;
 }
 
 function getControsContratados( $cod_transp )
 {
   //Trae los puestos de control que tiene contratados la transportadora
   $query = "SELECT a.cod_contro, a.cod_operad, a.ind_estado
               FROM ".BASE_DATOS.".tab_tercer_contro a,
                    ".BASE_DATOS.".tab_genera_contro b
              WHERE a.cod_contro = b.cod_contro
                AND a.cod_tercer = '".$cod_transp."'
				AND ( b.nom_contro LIKE '%EAL%' OR b.nom_contro LIKE '%ECL%' )
           ORDER BY b.nom_contro ASC";
   $consulta = new Consulta( $query, $this -> conexion );
   $puestosContratados = $consulta -> ret_matriz();
   return $puestosContratados;
 }


}//FIN CLASE Proc_alerta
     //$proceso = new Proc_alerta($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);
     $proceso = new Proc_alerta($_SESSION['conexion'], $_SESSION['usuario_aplicacion'], $_SESSION['codigo']);
?>