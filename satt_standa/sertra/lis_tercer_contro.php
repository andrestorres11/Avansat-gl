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
 var $contador;   
 function __construct($co, $us, $ca)
 {
  $this -> conexion = $co;
  $this -> usuario = $us;
  $this -> cod_aplica = $ca;
  $this -> contador = 1;
  $this -> principal();
 }
//********METODOS
 function principal()
 {
 
 ini_set("memory_limit", "128M");
  if(!isset($_REQUEST[opcion]))
     $this -> FormularioBusqueda();
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
          $this -> Resultado();
          break;
        case "addpc":
          $this -> AgregarPC();
          break;
       }//FIN SWITCH
     }// FIN ELSE _REQUEST OPCION
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
     $_REQUEST[cod_transp] = $matriz[0][0];
     $this -> Formulario();
   }
   else
   {
     $datos_usuario = $this -> usuario -> retornar();
     $usuario=$datos_usuario["cod_usuari"];
  
     $formulario = new Formulario ("index.php","post","Listar Tipo de Servicio de Transportadoras","form_item");
     $formulario -> linea("Se Encontro un Total de ".sizeof($matriz)." Transportadoras(s) para la b&uacute;squeda "." \" ".$_POST[busq_transp]." \" ",0,"t2");
     $formulario -> nueva_tabla();
  
     if(sizeof($matriz) > 0)
     {
        $formulario -> linea("NIT",0,"t");
        
        $formulario -> linea("Nombre",0,"t");
        
        $formulario -> linea("Abreviatura",1,"t");
        
        for($i=0;$i<sizeof($matriz);$i++)
        {
          $matriz[$i][0]= "<a href=\"index.php?cod_servic=$_REQUEST[cod_servic]&window=central&cod_transp=".$matriz[$i][0]."&opcion=1 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";
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
     $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
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
    
		$formulario = new Formulario ( "index.php", "post", "Listar Puestos de Control Transportadoras", "formulario" );
		echo "<td>";
		$formulario -> oculto( "window","central",0 );
		$formulario -> oculto( "cod_servic", $_REQUEST[cod_servic],0 );
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
   $cod_transp = $_REQUEST['cod_transp'];
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
   $formulario = new Formulario ("index.php","post","Listar Puestos Control Transportadora","ins_tercer_contro");
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
   
   if( count( $puestosContratados ) > 0 )
   {
     $html .= $this -> getCabecera();
     for ($j = 1, $nro = 1;  $j <= $numFilas; $j++)
     {
       //Se muestran las filas
       $selectedArray['cod_contro'] = $puestosContratados[$j-1]['cod_contro'];
       $selectedArray['cod_operad'] = $puestosContratados[$j-1]['cod_operad'];
       $selectedArray['ind_estado'] = $puestosContratados[$j-1]['ind_estado'];
       //if ( $selectedArray[$j]['nom_contro'] )
       //{
         //echo "<br>".$contros[$j]['nom_contro'];
         $html .= $this ->getRow( $nro, $selectedArray, $contros, $operadores );
         $nro++;
       //}
     }
   }
   else
   {
    
     $html .= '<tr><td colspan="3">No hay Puestos de Control Contratados</td></tr>';
   }

   $html .= '</table>';
   $html .= '</div>';
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
   $formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],1);
   $formulario -> botoni("Volver","javascript:history.go(-1)",0);
   $formulario -> cerrar();
 }//FIN FUNCTION CAPTURA


  function getCabecera()
  {
    $html .= '<tr>';
    $html .= '<td width="20%" align="center" class="celda_titulo" colspan="2" ><b> Puesto De Control </b></td>';
    $html .= '<td width="20%" align="left" class="celda_titulo"><b>Operador</b></td>';
    $html .= '<td width="20%" align="left" class="celda_titulo"><b>Estado</b></td>';
    $html .= '</tr>';
    return $html;
  }
 
 function getRow( $index, $selectedArray, $contros, $operadores )
 {
   $html .= '<tr>';
   //<------- Puesto de control ------->
   $html .= '<td width="2%" align="right" class="celda_titulo">'.$this -> contador.'</td>';
   
   $html .= '<td width="20%" class="celda_info">';
   foreach( $contros as $contro )
   {
     if( $selectedArray['cod_contro']==$contro['cod_contro'] )
     {
        $nom_contro = $contro['nom_contro'];
     }
   }
   if( $nom_contro )
   {
     $this -> contador++;
     $html .= !$nom_contro ? '---' : $nom_contro . '</td>';
     
     //<------- Operadores ------->
     $html .= '<td width="20%" class="celda_info">';
     foreach( $operadores as $operador )
     {
       if( $selectedArray['cod_operad'] == $operador['cod_operad'] )
       {
         $nom_operad = $operador['nom_operad'];
       }
     }
     $html .= !$nom_operad ? '---': $nom_operad. '</td>';
     
     //<------- Estado ------->
     $estado = $selectedArray['ind_estado'] == '1' || $numFilas == 1 ? 'Activo' : 'Inactivo';
     $html .= '<td width="20%" align="center" class="celda_info"> '. $estado .' </td>';

     $html .= '</tr>';
   }
   else
   {
     $html = '';
   }
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
   $sql = "SELECT a.cod_ealxxx AS cod_contro, 
                  '1' AS cod_operad,
                  '1' AS ind_estado,
                  a.val_ealxxx, a.fec_inieal, 
                  a.fec_fineal 
             FROM 
                  " . BASE_DATOS . ".tab_ealxxx_transp a 
       INNER JOIN " . BASE_DATOS . ".tab_genera_contro b ON a.cod_ealxxx = b.cod_contro
            WHERE a.cod_transp = '$cod_transp' ";
        
   $consulta = new Consulta( $sql, $this -> conexion );
   $puestosContratados = $consulta -> ret_matriz();
   return $puestosContratados;
 }


}//FIN CLASE Proc_alerta
     //$proceso = new Proc_alerta($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);
     $proceso = new Proc_alerta($_SESSION['conexion'], $_SESSION['usuario_aplicacion'], $_SESSION['codigo']);
?>