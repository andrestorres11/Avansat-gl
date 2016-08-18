<?php
session_start();
class HoraMoni
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
          $this -> Filtro();
          break;
        case "2":
          $this -> Listar();
          break;
        case "3":
          $this -> infoHorari();
          break;
        default:
          $this -> Filtro();
          break;
      }
 }





 function Filtro()
 {
   $inicio[0][0] = 0;
   $inicio[0][1] = '-';
	 //codigo de ruta
   $query = "SELECT cod_usuari,nom_usuari FROM ".BASE_DATOS.".tab_genera_usuari ";
   $consulta = new Consulta( $query, $this -> conexion );
	 $usuar = $consulta -> ret_matriz();
   $usuari = array_merge($inicio, $usuar);
   $query = "SELECT a.cod_tercer,a.abr_tercer
        			FROM ".BASE_DATOS.".tab_tercer_tercer a,
   		           	 ".BASE_DATOS.".tab_tercer_activi b
   		        WHERE a.cod_tercer = b.cod_tercer AND
   		              b.cod_activi = ".COD_FILTRO_EMPTRA."
   		        ORDER BY 2 ";
   $consulta = new Consulta($query, $this -> conexion);
   $transpor = $consulta -> ret_matriz();
   $transpor = array_merge($inicio, $transpor); 
   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";
   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/monit.js\"></script>\n";
   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/es.js\"></script>\n";
   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/time.js\"></script>\n";
   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";
   echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";
   echo '
    <script>
      jQuery(function($) { 
        
        $( "#feciniID,#fecfinID" ).datepicker();      
        $( "#horiniID,#horfinID" ).timepicker({
          timeFormat:"hh:mm",
          showSecond: false
        });
        
        $.mask.definitions["A"]="[12]";
        $.mask.definitions["M"]="[01]";
        $.mask.definitions["D"]="[0123]";
        
        $.mask.definitions["H"]="[012]";
        $.mask.definitions["N"]="[012345]";
        $.mask.definitions["n"]="[0123456789]";
        
        $( "#feciniID,#fecfinID" ).mask("Annn-Mn-Dn");
        $( "#horiniID,#horfinID" ).mask("Hn:Nn");
     })
     </script>';
   $formulario = new Formulario ("index.php","post","Listar Horarios de Monitoreo","form_ins\" id=\"formularioID");
   $formulario -> nueva_tabla();
   $formulario -> lista("Usuario","usuari\" id=\"usuariID",$usuari,1);
   $formulario -> lista("Transportadora","transp\" id=\"transpID",$transpor,1);  
   $formulario -> texto ("Fecha Inicio","text","fecini\" id=\"feciniID",0,9,12,"","$GLOBALS[fecini]");
   $formulario -> texto ("Fecha Final","text","fecfin\" id=\"fecfinID",0,9,12,"","$GLOBALS[fecfin]");
   $formulario -> nueva_tabla();
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
   $formulario -> oculto("opcion\" id=\"opcionID",1,0);

   $formulario -> botoni("Buscar","listar()",0);

   $formulario -> nueva_tabla();
   echo "<br><br><br><br><br><br><br><br>";
   $formulario -> cerrar();
  


 }
 
 
function Listar(){
   include( "../".DIR_APLICA_CENTRAL."/lib/general/dinamic_list.inc" );

        echo "<link rel=\"stylesheet\" href=\"../".DIR_APLICA_CENTRAL."/estilos/dinamic_list.css\" type=\"text/css\">";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/dinamic_list.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/monit.js\"></script>\n";

        $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "Listar Horarios de Monitoreo", "formulario\" id=\"formularioID");
        $sql = "SELECT a.cod_consec, b.cod_usuari, b.nom_usuari,
                       a.fec_inicia, a.fec_finalx,
                       if(a.ind_estado ='1','Estado','Anulado') ,
                       if(a.ind_limpio = '1','Limpio','No limpio') AS ind_limpio
   				        FROM ".BASE_DATOS.".tab_monito_encabe a,
                       ".BASE_DATOS.".tab_genera_usuari b,
                       ".BASE_DATOS.".tab_monito_detall c
   			          WHERE a.cod_usuari = b.cod_usuari AND
                        c.cod_consec = a.cod_consec ";
        if($GLOBALS['usuari'])
          $sql .= " AND a.cod_usuari = '".$GLOBALS['usuari']."' ";
        if($GLOBALS['transp'])
          $sql .= " AND c.cod_tercer = '".$GLOBALS['transp']."' ";
        if($GLOBALS['fecini'] && $GLOBALS['fecfin'])
          $sql .= " AND ((a.fec_inicia >= '".$GLOBALS['fecini']." 00:00:00'  AND a.fec_inicia <= '".$GLOBALS['fecini']." 23:59:00') OR (a.fec_finalx >= '".$GLOBALS['fecfin']." 00:00:00' AND a.fec_finalx <= '".$GLOBALS['fecfin']." 23:59:00'))";
	      $sql .=" GROUP BY 1";
        $_SESSION["queryXLS"] = $sql;
        $list = new DinamicList($this->conexion, $sql, 1 );
        $list->SetClose('no');
        $list->SetHeader("Consecutivo", "field:a.cod_consec; type:link; onclick:infoHorari()");
        $list->SetHeader("Usuario", "field:a.cod_usuari; type:link; onclick:infoHorari()");
        $list->SetHeader("Nombre", "field:b.nom_usuari");
        $list->SetHeader("Fecha Inicial", "field:a.fec_inicia" );
        $list->SetHeader("Fecha Final", "field:a.fec_finalx");
        $list->SetHeader("Estado","field:a.ind_estado");
        $list->SetHeader("Limpio","field:ind_limpio");

        $list->Display($this->conexion);

        $_SESSION["DINAMIC_LIST"] = $list;
        echo "<td>";
        echo $list->GetHtml();
        echo "</td>";

        $formulario -> nueva_tabla();
        $formulario -> oculto("url_archiv\" id=\"url_archivID\"","lis_horari_monito.php",0);
		    $formulario -> oculto("dir_aplica\" id=\"dir_aplicaID\"",DIR_APLICA_CENTRAL,0);
        $formulario -> oculto("num_serie",0,0);
        $formulario -> oculto("window","central",0);
        $formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],0);
        $formulario -> oculto("opcion",2,0);

        $formulario -> cerrar();
        
        echo '<tr><td><div id="AplicationEndDIV"></div>
              <div id="RyuCalendarDIV" style="position: absolute; background: white; left: 0px; top: 0px; z-index: 3; display: none; border: 1px solid black; "></div>
              <div id="popupDIV" style="position: absolute; left: 0px; top: 0px; width: 300px; height: 300px; z-index: 3; visibility: hidden; overflow: auto; border: 5px solid #333333; background: white; ">

    		  <div id="filtros" >
    		  </div>

    		  <div id="result" >


    		  </div>
     		  </div><div id="alg"> <table></table></div></td></tr>';
			echo"";
    }

  function infoHorari()
	{
		ini_set('display_errors', true);
		@session_start();
    error_reporting(E_ALL & ~E_NOTICE);
		global $HTTP_POST_FILES;
		$BASE = $_SESSION[BASE_DATOS];
		define ('DIR_APLICA_CENTRAL', $_SESSION['DIR_APLICA_CENTRAL']);
    define ('ESTILO', $_SESSION['ESTILO']);
    define ('BASE_DATOS', $_SESSION['BASE_DATOS']);
		include( "../lib/general/conexion_lib.inc" );
		include( "../lib/general/form_lib.inc" );
		include( "../lib/general/tabla_lib.inc" );
    $this -> conexion = new Conexion( "bd10.intrared.net:3306", $_SESSION[USUARIO], $_SESSION[CLAVE], $BASE  );
		$sql = "SELECT b.cod_usuari, b.nom_usuari,
                   a.fec_inicia, a.fec_finalx,
                   a.obs_anulad, if(a.ind_estado ='1','Activo','Anulado'),
                   c.cod_tercer, d.abr_tercer, c.obs_anulad,
                   if(c.ind_estado ='1','Activo','Anulado') 
		        FROM ".BASE_DATOS.".tab_monito_encabe a,
                 ".BASE_DATOS.".tab_genera_usuari b,
                 ".BASE_DATOS.".tab_monito_detall c,
                 ".BASE_DATOS.".tab_tercer_tercer d
	          WHERE a.cod_usuari = b.cod_usuari AND
                  c.cod_consec = a.cod_consec AND
                  c.cod_tercer = d.cod_tercer AND
                  a.cod_consec = '".$_POST['cod_consec']."'";
    $consulta = new Consulta($sql, $this -> conexion);
  	$horaris = $consulta -> ret_matriz();
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mmpp.js\"></script>\n";
		
		$formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "INFORMACION DEL HORARIO", "formulario");
		$formulario -> nueva_tabla();
    $formulario -> linea("Datos Basicos. ",1,"t2");
    $formulario -> nueva_tabla();
    $formulario -> linea("Codigo Usuario:",0,"t","15%");
    $formulario -> linea($horaris[0][0],0,"i","15%");
    $formulario -> linea("Nombre:",0,"t","15%");
    $formulario -> linea($horaris[0][1],1,"i","15%"); 
    $formulario -> linea("Fecha Inicial:",0,"t","15%");
    $formulario -> linea($horaris[0][2],0,"i","15%");
    $formulario -> linea("Fecha Final:",0,"t","15%");
    $formulario -> linea($horaris[0][3],1,"i","15%");
    $formulario -> linea("Observacion Anulado:",0,"t","15%");
    $formulario -> linea($horaris[0][4],1,"i","15%");  
    $formulario -> linea("Estado:",0,"t","15%");
    $formulario -> linea($horaris[0][5],0,"i","15%"); 
    
    $formulario -> nueva_tabla(); 
    $formulario -> botoni("Cerrar","ClosePopup()",1);
    $formulario -> nueva_tabla();
    $formulario -> linea("Transportadora:",0,"t","15%");
    $formulario -> linea("Observacion de Anulado",0,"t","15%");
    $formulario -> linea("Estado",1,"t","15%");
    for($i=0;$i<=sizeof($transpor);$i++){
      $formulario -> linea($horaris[$i][7],0,"t","15%");
      $formulario -> linea($horaris[$i][8],0,"t","15%");
      $formulario -> linea($horaris[$i][9],1,"t","15%");
    }
    $formulario -> nueva_tabla(); 
    $formulario -> botoni("Cerrar","ClosePopup()",1);
    $formulario -> cerrar();
	}
  
	
	
	
	
}//FIN CLASE PROC_DESPAC

  //$proceso = new HoraMoni($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
  $proceso = new HoraMoni($_SESSION['conexion'], $_SESSION['usuario_aplicacion'], $_SESSION['codigo']);


?>
