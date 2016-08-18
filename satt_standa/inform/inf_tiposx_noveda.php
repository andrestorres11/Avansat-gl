<?php
session_start();
class InfNoveda
{ 
    var $conexion;

    function __construct($conexion)
    {

        $this->conexion = $conexion;
        switch($_POST[opcion])
        {

            case "1":
            {
                $this->filtros();
            }
            break;
            case "2":
            {
                $this->Listar();
            }
            break;
            case "3":
				      $this ->infoNoveda();
            break;
            default:
            {
                $this->filtros();
            }
            break;
        }
    }

    function Listar()
    {
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/inf_noveda.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
        if(!$GLOBALS["horaini"])
          $GLOBALS["horaini"]='00:00:00';
        if(!$GLOBALS["horafin"])
          $GLOBALS["horafin"]='23:59:00';  
        $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "INFORME DE NOVEDADES", "formulario\" id=\"formularioID");
        $sql ="SELECT cod_usuari,nom_usuari 
               FROM ".BASE_DATOS.".tab_genera_usuari ";
        if($GLOBALS['cod_usuari'])
          $sql .= "WHERE cod_usuari ='".$GLOBALS['cod_usuari']."'";       
        $consulta = new Consulta($sql, $this -> conexion);
        $usuarios = $consulta -> ret_matriz();
        $formulario -> nueva_tabla();
        $formulario -> oculto("url_archiv\" id=\"url_archivID\"","inf_tiposx_noveda.php",0);
		    $formulario -> oculto("dir_aplica\" id=\"dir_aplicaID\"",DIR_APLICA_CENTRAL,0);
        $formulario -> oculto("based\" id=\"basedID\"",BASE_DATOS,0);//Jorge 2703-2012
        $formulario -> oculto("num_serie",0,0);
        $formulario -> oculto("window","central",0);
        $formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],0);
        $formulario -> oculto("opcion",2,0);
        $formulario -> nueva_tabla();
        $formulario -> linea("Informe de Novedades de ".$GLOBALS['fec_inicial']." ".$GLOBALS['horaini']." al ".$GLOBALS['fec_final']." ".$GLOBALS['horafin']  ,1,"t2"); 
        $grantotal=0;
        $totot=0;
        $totne=0;
        $totma=0;
        $totst=0;
        $totga=0;
        $h=0;//Jorge 23-03-2012
        foreach($usuarios AS $usuari){
          if($usuari[0]=='far0')
            $usuari[0]='faro';
          $total=0;
          $sql ="SELECT  b.usr_creaci 
                   FROM ".BASE_DATOS.".tab_genera_noveda a, ".BASE_DATOS.".tab_despac_contro b
                   WHERE a.cod_noveda = b.cod_noveda
                         AND b.fec_contro >= '".$GLOBALS['fec_inicial']." ".$GLOBALS['horaini']."'
                         AND b.fec_contro <= '".$GLOBALS['fec_final']." ".$GLOBALS['horafin']."' 
                         AND b.usr_creaci = '".$usuari[0]."' ";
          $consulta = new Consulta($sql, $this -> conexion);
          $tot = $consulta -> ret_matriz();
          $total += sizeof($tot);
          $sql ="SELECT b.usr_creaci 
                 FROM ".BASE_DATOS.".tab_genera_noveda a, ".BASE_DATOS.".tab_despac_noveda b
                 WHERE a.cod_noveda = b.cod_noveda
                       AND b.fec_noveda >= '".$GLOBALS['fec_inicial']." ".$GLOBALS['horaini']."'
                       AND b.fec_noveda <= '".$GLOBALS['fec_final']." ".$GLOBALS['horafin']."'  
                       AND b.usr_creaci = '".$usuari[0]."'";
          $consulta = new Consulta($sql, $this -> conexion);
          $tot = $consulta -> ret_matriz();
          $total += sizeof($tot);
          if($total=='')
            $total='0';
          $grantotal+= $total;
          $otros=0;
          $sql ="SELECT  b.usr_creaci 
                   FROM ".BASE_DATOS.".tab_genera_noveda a, ".BASE_DATOS.".tab_despac_contro b
                   WHERE a.cod_noveda = b.cod_noveda
                         AND a.nov_especi !='1'
                         AND a.ind_tiempo !='1'
                         AND a.ind_manala !='1'
                         AND a.ind_alarma !='S'
                         AND b.fec_contro >= '".$GLOBALS['fec_inicial']." ".$GLOBALS['horaini']."'
                         AND b.fec_contro <= '".$GLOBALS['fec_final']." ".$GLOBALS['horafin']."' 
                         AND b.usr_creaci = '".$usuari[0]."' ";
          $consulta = new Consulta($sql, $this -> conexion);
          $otr = $consulta -> ret_matriz();
          $otros += sizeof($otr);
          $sql ="SELECT b.usr_creaci 
                 FROM ".BASE_DATOS.".tab_genera_noveda a, ".BASE_DATOS.".tab_despac_noveda b
                 WHERE a.cod_noveda = b.cod_noveda
                       AND a.nov_especi !='1'
                       AND a.ind_tiempo !='1'
                       AND a.ind_manala !='1'
                       AND a.ind_alarma !='S'
                       AND b.fec_noveda >= '".$GLOBALS['fec_inicial']." ".$GLOBALS['horaini']."'
                       AND b.fec_noveda <= '".$GLOBALS['fec_final']." ".$GLOBALS['horafin']."'  
                       AND b.usr_creaci = '".$usuari[0]."'";
          $consulta = new Consulta($sql, $this -> conexion);
          $otr = $consulta -> ret_matriz();
          $otros += sizeof($otr);
          if($otros=='')
            $otros='0';
          $totot +=  $otros;
          $especi=0;
          $sql ="SELECT  b.usr_creaci 
                   FROM ".BASE_DATOS.".tab_genera_noveda a, ".BASE_DATOS.".tab_despac_contro b
                   WHERE a.cod_noveda = b.cod_noveda
                         AND a.nov_especi ='1'
                         AND b.fec_contro >= '".$GLOBALS['fec_inicial']." ".$GLOBALS['horaini']."'
                         AND b.fec_contro <= '".$GLOBALS['fec_final']." ".$GLOBALS['horafin']."' 
                         AND b.usr_creaci = '".$usuari[0]."' ";
          $consulta = new Consulta($sql, $this -> conexion);
          $nov_especi = $consulta -> ret_matriz();
          $especi += sizeof($nov_especi);
          $sql ="SELECT b.usr_creaci 
                 FROM ".BASE_DATOS.".tab_genera_noveda a, ".BASE_DATOS.".tab_despac_noveda b
                 WHERE a.cod_noveda = b.cod_noveda
                       AND a.nov_especi ='1'
                       AND b.fec_noveda >= '".$GLOBALS['fec_inicial']." ".$GLOBALS['horaini']."'
                       AND b.fec_noveda <= '".$GLOBALS['fec_final']." ".$GLOBALS['horafin']."'  
                       AND b.usr_creaci = '".$usuari[0]."'";
          $consulta = new Consulta($sql, $this -> conexion);
          $nov_especi = $consulta -> ret_matriz();
          $especi += sizeof($nov_especi);
          if($especi=='')
            $especi='0';
          $totne += $especi; 
          $tiempo=0;
          $sql ="SELECT b.usr_creaci
                   FROM ".BASE_DATOS.".tab_genera_noveda a, ".BASE_DATOS.".tab_despac_contro b
                   WHERE a.cod_noveda = b.cod_noveda
                         AND a.ind_tiempo ='1'
                         AND b.fec_contro >= '".$GLOBALS['fec_inicial']." ".$GLOBALS['horaini']."'
                         AND b.fec_contro <= '".$GLOBALS['fec_final']." ".$GLOBALS['horafin']."' 
                         AND b.usr_creaci = '".$usuari[0]."' ";
          $consulta = new Consulta($sql, $this -> conexion);
          $tiem = $consulta -> ret_matriz();
          $tiempo += sizeof($tiem);
          $sql ="SELECT b.usr_creaci
                 FROM ".BASE_DATOS.".tab_genera_noveda a, ".BASE_DATOS.".tab_despac_noveda b
                 WHERE a.cod_noveda = b.cod_noveda
                       AND a.ind_tiempo ='1'
                       AND b.fec_noveda >= '".$GLOBALS['fec_inicial']." ".$GLOBALS['horaini']."'
                       AND b.fec_noveda <= '".$GLOBALS['fec_final']." ".$GLOBALS['horafin']."'  
                       AND b.usr_creaci = '".$usuari[0]."' ";
          $consulta = new Consulta($sql, $this -> conexion);
          $tiem = $consulta -> ret_matriz();
          $tiempo += sizeof($tiem);
           if($tiempo=='')
            $tiempo='0';
          $totst += $tiempo;
          $mantiene=0;
          $sql ="SELECT b.usr_creaci
                   FROM ".BASE_DATOS.".tab_genera_noveda a, ".BASE_DATOS.".tab_despac_contro b
                   WHERE a.cod_noveda = b.cod_noveda
                         AND a.ind_manala ='1'
                         AND b.fec_contro >= '".$GLOBALS['fec_inicial']." ".$GLOBALS['horaini']."'
                         AND b.fec_contro <= '".$GLOBALS['fec_final']." ".$GLOBALS['horafin']."' 
                         AND b.usr_creaci = '".$usuari[0]."' ";
          $consulta = new Consulta($sql, $this -> conexion);
          $man = $consulta -> ret_matriz();
          $mantiene += sizeof($man);
          $sql ="SELECT b.usr_creaci
                 FROM ".BASE_DATOS.".tab_genera_noveda a, ".BASE_DATOS.".tab_despac_noveda b
                 WHERE a.cod_noveda = b.cod_noveda
                       AND a.ind_manala ='1'
                       AND b.fec_noveda >= '".$GLOBALS['fec_inicial']." ".$GLOBALS['horaini']."'
                       AND b.fec_noveda <= '".$GLOBALS['fec_final']." ".$GLOBALS['horafin']."'  
                       AND b.usr_creaci = '".$usuari[0]."' ";
          $consulta = new Consulta($sql, $this -> conexion);
          $man = $consulta -> ret_matriz();
          $mantiene += sizeof($man);
          if($mantiene=='')
            $mantiene='0';
          $totma += $mantiene;
          $genera=0;
          $sql ="SELECT b.usr_creaci
                   FROM ".BASE_DATOS.".tab_genera_noveda a, ".BASE_DATOS.".tab_despac_contro b
                   WHERE a.cod_noveda = b.cod_noveda
                         AND a.ind_alarma ='S'
                         AND b.fec_contro >= '".$GLOBALS['fec_inicial']." ".$GLOBALS['horaini']."'
                         AND b.fec_contro <= '".$GLOBALS['fec_final']." ".$GLOBALS['horafin']."' 
                         AND b.usr_creaci = '".$usuari[0]."' ";
          $consulta = new Consulta($sql, $this -> conexion);
          $alarma = $consulta -> ret_matriz();
          $genera += sizeof($alarma);
          $sql ="SELECT b.usr_creaci 
                 FROM ".BASE_DATOS.".tab_genera_noveda a, ".BASE_DATOS.".tab_despac_noveda b
                 WHERE a.cod_noveda = b.cod_noveda
                       AND a.ind_alarma ='S'
                       AND b.fec_noveda >= '".$GLOBALS['fec_inicial']." ".$GLOBALS['horaini']."'
                       AND b.fec_noveda <= '".$GLOBALS['fec_final']." ".$GLOBALS['horafin']."'  
                       AND b.usr_creaci = '".$usuari[0]."' ";
          $consulta = new Consulta($sql, $this -> conexion);
          $alarma = $consulta -> ret_matriz();
          $genera += sizeof($alarma);
          if($genera=='')
            $genera='0';
          $totga += $genera;  
          if($especi || $genera || $mantiene || $tiempo || $otros){
            $formulario -> nueva_tabla();
            $formulario -> linea("Usuario: ".$usuari[0]." Nombre: ".$usuari[1],1,"t2");
            $formulario -> nueva_tabla();
            $formulario -> linea("Total",0,"t");
            echo "<td align='left' class='celda_titulo' style='cursor:pointer;' onclick=\"infoNoveda('TO','".$usuari[0]."','".$GLOBALS['fec_inicial']."','".$GLOBALS['fec_final']."','".$GLOBALS['horaini']."', '".$GLOBALS['horafin']."');\">";
            echo "<b>$total ";
            echo "</td></tr>";
            $formulario -> linea("Otros",0,"t");
            echo "<td align='left' class='celda_titulo' style='cursor:pointer;' onclick=\"infoNoveda('OT','".$usuari[0]."','".$GLOBALS['fec_inicial']."','".$GLOBALS['fec_final']."','".$GLOBALS['horaini']."', '".$GLOBALS['horafin']."');\">";
            echo "<b>$otros ";
            echo "</td></tr>";
            $formulario -> linea("Novedad Especial",0,"t");
            echo "<td align='left' class='celda_titulo' style='cursor:pointer;' onclick=\"infoNoveda('NE','".$usuari[0]."','".$GLOBALS['fec_inicial']."','".$GLOBALS['fec_final']."','".$GLOBALS['horaini']."', '".$GLOBALS['horafin']."');\">";
            echo "<b>$especi ";
            echo "</td></tr>";
            $formulario -> linea("Solicita Tiempo",0,"t");
             echo "<td align='left' class='celda_titulo' style='cursor:pointer;' onclick=\"infoNoveda('ST','".$usuari[0]."','".$GLOBALS['fec_inicial']."','".$GLOBALS['fec_final']."','".$GLOBALS['horaini']."', '".$GLOBALS['horafin']."');\">";
            echo "<b>$tiempo ";
            echo "</td></tr>";
            $formulario -> linea("Mantiene Alerta",0,"t");
             echo "<td align='left' class='celda_titulo' style='cursor:pointer;' onclick=\"infoNoveda('MA','".$usuari[0]."','".$GLOBALS['fec_inicial']."','".$GLOBALS['fec_final']."','".$GLOBALS['horaini']."', '".$GLOBALS['horafin']."');\">";
            echo "<b>$mantiene ";
            echo "</td></tr>";
            $formulario -> linea("Genera Alarma",0,"t");
             echo "<td align='left' class='celda_titulo' style='cursor:pointer;' onclick=\"infoNoveda('GA','".$usuari[0]."','".$GLOBALS['fec_inicial']."','".$GLOBALS['fec_final']."','".$GLOBALS['horaini']."', '".$GLOBALS['horafin']."');\">";
            echo "<b>$genera ";
            echo "</td></tr>";
        
            //Jorge 27-03-2012
            $novedatotal[$h]['Usuario']=$usuari[0];
            $novedatotal[$h]['Nombre']=$usuari[1];
            $novedatotal[$h]['Total']=$total;
            $novedatotal[$h]['Otros']=$otros;
            $novedatotal[$h]['Novedad Especial']=$especi;
            $novedatotal[$h]['Solicita Tiempo']=$tiempo;
            $novedatotal[$h]['Mantiene Alerta']=$mantiene;
            $novedatotal[$h]['Genera Alarma']=$genera;
            $h++;//
          }
          
          
            
        }
        $formulario -> nueva_tabla();
        $formulario -> linea("Gran Total",1,"t2");
        $formulario -> nueva_tabla();
        $formulario -> linea("Total",0,"t");
        echo "<td align='left' class='celda_titulo' );\">";
        echo "<b>$grantotal ";
        echo "</td></tr>";
        $formulario -> linea("Otros",0,"t");
        echo "<td align='left' class='celda_titulo' );\">";
        echo "<b>$totot ";
        echo "</td></tr>";
        $formulario -> linea("Novedad Especial",0,"t");
        echo "<td align='left' class='celda_titulo' );\">";
        echo "<b>$totne ";
        echo "</td></tr>";
        $formulario -> linea("Solicita Tiempo",0,"t");
         echo "<td align='left' class='celda_titulo' );\">";
        echo "<b>$totst ";
        echo "</td></tr>";
        $formulario -> linea("Mantiene Alerta",0,"t");
         echo "<td align='left' class='celda_titulo' );\">";
        echo "<b>$totma ";
        echo "</td></tr>";
        $formulario -> linea("Genera Alarma",0,"t");
         echo "<td align='left' class='celda_titulo' );\">";
        echo "<b>$totga ";
        echo "</td></tr>";

        //Jorge 27-03-2012
        $novedatotal[$h]['Usuario']="Gran Total";
        $novedatotal[$h]['Nombre']=$h;
        $novedatotal[$h]['Total']=$grantotal;
        $novedatotal[$h]['Otros']=$totot;
        $novedatotal[$h]['Novedad Especial']=$totne;
        $novedatotal[$h]['Solicita Tiempo']=$totst;
        $novedatotal[$h]['Mantiene Alerta']=$totma;
        $novedatotal[$h]['Genera Alarma']=$totga;
        $_SESSION['LIST_TOTAL']=$novedatotal;
    
        $formulario -> nueva_tabla();
        $formulario -> botoni("Excel","exportarXls2()",0);
        $formulario -> nueva_tabla();//
        
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

  
  
  
  
  
  




    function filtros()
    {
      $inicio[0][0] = 0;
      $inicio[0][1] = '-';
      $sql ="SELECT cod_usuari,nom_usuari 
             FROM ".BASE_DATOS.".tab_genera_usuari ";
      $consulta = new Consulta($sql, $this -> conexion);
      $usuari = $consulta -> ret_matriz(); 
      $usuari= array_merge($inicio,$usuari); 
       echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/inf_noveda.js\"></script>\n";
		   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";
		   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
		   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
       echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
       echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/es.js\"></script>\n";
       echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/time.js\"></script>\n";
       echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";
       echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";
       echo '
        <script>
          jQuery(function($) { 
            
            $( "#fec_inicialID,#fec_finalID" ).datepicker();
            $( "#horainiID,#horafinID" ).timepicker({
              timeFormat:"hh:mm",
              showSecond: false
            });
                  
            $.mask.definitions["A"]="[12]";
            $.mask.definitions["M"]="[01]";
            $.mask.definitions["D"]="[0123]";
            $.mask.definitions["H"]="[012]";
            $.mask.definitions["N"]="[012345]";
            $.mask.definitions["n"]="[0123456789]";
            
            $( "#fec_inicialID,#fec_finalID" ).mask("Annn-Mn-Dn");
            $( "#horainiID,#horafinID" ).mask("Hn:Nn");
            
          });
       </script>';
        $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "Informe de Novedades", "formulario\" id=\"formularioID");
        
        $formulario -> nueva_tabla();
        $formulario -> texto ("Fecha Inicial","text","fec_inicial\" id=\"fec_inicialID",0,7,7,"","" );
        $formulario -> texto ("Hora Inicial","text","horaini\" id=\"horainiID",1,7,7,"","" );
        $formulario -> texto ("Fecha Final","text","fec_final\" id=\"fec_finalID",0,7,7,"","" );
        $formulario -> texto ("Hora Final","text","horafin\" id=\"horafinID",1,7,7,"","" );
        $formulario -> lista ("Usuario","cod_usuari\" id=\"ind_estadoID",$usuari,1 );
        $formulario -> nueva_tabla();
        $formulario -> botoni("Buscar","Listar()",0);
        $formulario -> nueva_tabla();
        echo "<BR><BR>";
        echo "<BR><BR>";
        echo "<BR><BR>";
        $formulario -> oculto("window","central",0);
        $formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],0);
        $formulario -> oculto("opcion\" id=\"opcionID",1,0);
        $formulario -> cerrar();


    }


  function moneyToDouble($valor=NULL){
   		 return  str_replace( ',', NULL, $valor );
  	}
	


    function infoNoveda()
	{
		ini_set('display_errors', true);
		error_reporting(E_ALL & ~E_NOTICE);
		global $HTTP_POST_FILES;
		session_start();
		$BASE = $_SESSION[BASE_DATOS];
		define ('DIR_APLICA_CENTRAL', $_SESSION['DIR_APLICA_CENTRAL']);
    define ('ESTILO', $_SESSION['ESTILO']);
    define ('BASE_DATOS', $_SESSION['BASE_DATOS']);
		include( "../lib/general/conexion_lib.inc" );
		include( "../lib/general/form_lib.inc" );
		include( "../lib/general/tabla_lib.inc" );
    $this -> conexion = new Conexion( "bd10.intrared.net:3306", $_SESSION[USUARIO], $_SESSION[CLAVE], $BASE  );
		if($_POST['tipo']=='TO')    
      $aux=" 1='1'";
    if($_POST['tipo']=='OT')    
      $aux=" a.nov_especi !='1'
             AND a.ind_tiempo !='1'
             AND a.ind_manala !='1'
             AND a.ind_alarma !='S'";
    if($_POST['tipo']=='NE')
      $aux= "a.nov_especi ='1'";
    if($_POST['tipo']=='ST')
      $aux= "a.ind_tiempo ='1'";
    if($_POST['tipo']=='MA')
      $aux= "a.ind_manala ='1'";
    if($_POST['tipo']=='GA')
      $aux= "a.ind_alarma ='S'";  
    $sql ="(SELECT b.num_despac as Despacho, a.nom_noveda as Novedad,b.fec_contro as Fecha,
                   b.obs_contro as Observacion, d.abr_tercer as Transportadora
           FROM ".BASE_DATOS.".tab_genera_noveda a,
                ".BASE_DATOS.".tab_despac_contro b,
                ".BASE_DATOS.".tab_despac_vehige c,
                ".BASE_DATOS.".tab_tercer_tercer d
           WHERE a.cod_noveda = b.cod_noveda
                 AND $aux
                 AND c.num_despac = b.num_despac
                 AND c.cod_transp = d.cod_tercer
                 AND b.fec_contro >= '".$_POST['fec_inicial']." ".$GLOBALS['horaini']."'
                 AND b.fec_contro <= '".$_POST['fec_final']." ".$GLOBALS['horafin']."' 
                 AND b.usr_creaci = '".$_POST['usuari']."') 
           UNION ALL
           (SELECT b.num_despac, a.nom_noveda, b.fec_noveda ,
                   b.des_noveda, d.abr_tercer
           FROM ".BASE_DATOS.".tab_genera_noveda a, 
                ".BASE_DATOS.".tab_despac_noveda b,
                ".BASE_DATOS.".tab_despac_vehige c,
                ".BASE_DATOS.".tab_tercer_tercer d
           WHERE a.cod_noveda = b.cod_noveda
                 AND $aux
                 AND c.num_despac = b.num_despac
                 AND c.cod_transp = d.cod_tercer
                 AND b.fec_noveda >= '".$_POST['fec_inicial']." ".$GLOBALS['horaini']."'
                 AND b.fec_noveda <= '".$_POST['fec_final']." ".$GLOBALS['horafin']."' 
                 AND b.usr_creaci = '".$_POST['usuari']."')
           ORDER BY 5,1,3";
    $consulta = new Consulta($sql, $this -> conexion);
    $despachos = $consulta -> ret_matriz();
    $_SESSION['LIST_ARRAY'] = $despachos;//Jorge 2703-2012 
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
		
		$formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "INFORMACION DE NOVEDADES", "formulario");
    $formulario -> nueva_tabla(); 
    $formulario -> botoni("Excel","exportarXls()",0);//Jorge 2703-2012 
    $formulario -> botoni("Cerrar","ClosePopup()",1);//validarCumplidos()
    $formulario -> oculto("dir_aplica\" id=\"dir_aplicaID\"",DIR_APLICA_CENTRAL,0);//Jorge 2703-2012
    $formulario -> oculto("based\" id=\"basedID\"",BASE_DATOS,0);//Jorge 2703-2012
    $formulario -> cerrar();
    $formulario -> nueva_tabla();
    $formulario -> linea("Total de Novedades: ".sizeof($despachos),0,"t2","15%");
    $formulario -> nueva_tabla();
    $formulario -> linea("Transportadora",0,"t2","15%");
    $formulario -> linea("Despacho",0,"t2","15%");
    
    $formulario -> linea("Novedad",0,"t2","15%");
    $formulario -> linea("Fecha",0,"t2","15%");
    $formulario -> linea("Observacion",1,"t2","15%");
    foreach($despachos AS $despacho){
      $formulario -> linea($despacho[4],0,"i","15%");
      $formulario -> linea($despacho[0],0,"i","15%");
      
      $formulario -> linea($despacho[1],0,"i","15%");
      $formulario -> linea($despacho[2],0,"i","15%");
      $formulario -> linea($despacho[3],1,"i","15%");
    }
    echo "<br>";
    /*$formulario -> nueva_tabla();
    $formulario -> linea("Detalle de Novedades: ",0,"t2","15%");
    $sql ="(SELECT a.nom_noveda ,COUNT(a.usr_creaci)
           FROM ".BASE_DATOS.".tab_genera_noveda a, ".BASE_DATOS.".tab_despac_contro b
           WHERE a.cod_noveda = b.cod_noveda
                 AND $aux
                 AND b.fec_contro >= '".$_POST['fec_inicial']." ".$GLOBALS['horaini']."'
                 AND b.fec_contro <= '".$_POST['fec_final']." ".$GLOBALS['horafin']."' 
                 AND b.usr_creaci = '".$_POST['usuari']."' 
           GROUP BY a.cod_noveda) 
           UNION ALL
           (SELECT  a.nom_noveda ,COUNT(a.usr_creaci)
           FROM ".BASE_DATOS.".tab_genera_noveda a, ".BASE_DATOS.".tab_despac_noveda b
           WHERE a.cod_noveda = b.cod_noveda
                 AND $aux
                 AND b.fec_noveda >= '".$_POST['fec_inicial']." ".$GLOBALS['horaini']."'
                 AND b.fec_noveda <= '".$_POST['fec_final']." ".$GLOBALS['horafin']."' 
                 AND b.usr_creaci = '".$_POST['usuari']."'
           GROUP BY a.cod_noveda)
           ORDER BY 1,2";
    $consulta = new Consulta($sql, $this -> conexion);
    $despachos = $consulta -> ret_matriz();*/
    $formulario -> nueva_tabla(); 
    $formulario -> botoni("Excel","exportarXls()",0);//Jorge 2703-2012 
    $formulario -> botoni("Cerrar","ClosePopup()",1);//validarCumplidos()
    $formulario -> cerrar();
	}

}
//$service = new InfNoveda($this->conexion);
$service = new InfNoveda($_SESSION['conexion']);
?> 
