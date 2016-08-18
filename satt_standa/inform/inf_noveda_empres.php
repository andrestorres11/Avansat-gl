<?php
/****************************************************************************
NOMBRE:   MODULO INFORME DE NOVEDADES DE LA TRANSPORTADORA
FUNCION:  INFORME DE NOVEDADES DE LA TRANSP
AUTOR: JORGE PRECIADO
FECHA CREACION : 17 FEBRERO 2012
****************************************************************************/
session_start();
class InfNovEmp
{
  var  $conexion;
  
  function __construct($conexion)
  {
    $this->conexion=$conexion;
    ini_set("memory_limit", "128M");
    switch ($_POST[opcion])
    {
      case  "1":
      {
        echo  1;
        $this->filtro();
      }
       break;
       case  2:
       {
         $this->MostrarResul();
       }
       break;
       case  "3":
       {
         $this->infoNovEmp();
       }
       case  "4":
       {
         //$this->executeXls();
       }       
       break;
       default:
       {
         $this->filtro();
       }
       break;
    }
  }
  
  function filtro()
  {
    
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/inf_nov_emp.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/es.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/time.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/regnov.js\"></script>\n";
    echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";
    //Scrip del calendario
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
      //SQL Para el autocompletar
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
              $( "#busq_transpID" ).autocomplete({
                source: tranportadoras,
                delay: 100
              }).bind( "autocompleteclose", function(event, ui){$("#form_insID").submit();} );
              $( "#busq_transpID" ).bind( "autocompletechange", function(event, ui){$("#form_insID").submit();} ); 
	        });
        </script>';
    
    /*$inicio[0][0] = 0;
    $inicio[0][1] = '-';
   
    $query = "SELECT a.cod_tercer, UPPER( a.abr_tercer ) as abr_tercer
                FROM ".BASE_DATOS.".tab_tercer_tercer a,
                     ".BASE_DATOS.".tab_tercer_activi b
               WHERE a.cod_tercer = b.cod_tercer AND
                     b.cod_activi = ".COD_FILTRO_EMPTRA."
               ORDER BY 2";

             
    $consulta = new Consulta($query, $this -> conexion);
    $busq_transp = $consulta -> ret_matriz(); 
    $busq_transp= array_merge($inicio,$busq_transp);*/
    
    $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "Informe de Novedades de la Transportadora", "formulario\" id=\"formularioID");
    
    $formulario -> nueva_tabla();
    $formulario -> texto ("Fecha Inicial","text","fec_inicial\" id=\"fec_inicialID",0,7,7,"","" );
    $formulario -> texto ("Hora Inicial","text","horaini\" id=\"horainiID",1,7,7,"","" );
    $formulario -> texto ("Fecha Final","text","fec_final\" id=\"fec_finalID",0,7,7,"","" );
    $formulario -> texto ("Hora Final","text","horafin\" id=\"horafinID",1,7,7,"","" );
    $formulario -> texto ("Nit / Nombre","text","busq_transp\" id=\"busq_transpID",1,30,30,"","" );
    
    $formulario -> nueva_tabla();
    $formulario -> botoni("Buscar","MostrarResul()",0);
    $formulario -> nueva_tabla();
    echo "<BR><BR>";
    echo "<BR><BR>";
    echo "<BR><BR>";
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],0);
    $formulario -> oculto("opcion\" id=\"opcionID",1,0);
    $formulario -> cerrar();
    
  }
  

  function MostrarResul()
  {
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/inf_nov_emp.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
    if(!$GLOBALS["horaini"])
      $GLOBALS["horaini"]='00:00:00';
    if(!$GLOBALS["horafin"])
      $GLOBALS["horafin"]='23:59:00';
    $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "INFORME DE NOVEDADES DE LA TRANSPORTADORA", "formulario\" id=\"formularioID");
    
    $query ="SELECT a.cod_tercer, UPPER( a.abr_tercer ) as abr_tercer
               FROM ".BASE_DATOS.".tab_tercer_tercer a,
                    ".BASE_DATOS.".tab_tercer_activi b
              WHERE a.cod_tercer = b.cod_tercer AND
                    b.cod_activi = ".COD_FILTRO_EMPTRA."";
                    
    if($GLOBALS['busq_transp'])
    {
      $v = split(" - ", $GLOBALS['busq_transp']);
      $query .= " AND a.cod_tercer ='".$v[0]."'";   
    }
    $consulta = new Consulta($query, $this -> conexion);
    $transporta = $consulta -> ret_matriz();

    $formulario -> nueva_tabla();
    $formulario -> oculto("url_archiv\" id=\"url_archivID\"","inf_noveda_empres.php",0);
    $formulario -> oculto("dir_aplica\" id=\"dir_aplicaID\"",DIR_APLICA_CENTRAL,0);
    $formulario -> oculto("based\" id=\"basedID\"",BASE_DATOS,0);
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
    $h=0;
    foreach($transporta AS $transpor)
    {
      $total=0;
      $sql ="SELECT  b.usr_creaci 
               FROM ".BASE_DATOS.".tab_genera_noveda a,
                    ".BASE_DATOS.".tab_despac_contro b,
                    ".BASE_DATOS.".tab_despac_vehige c,
                    ".BASE_DATOS.".tab_tercer_tercer d
              WHERE a.cod_noveda = b.cod_noveda
                    AND c.num_despac = b.num_despac
                    AND c.cod_transp = d.cod_tercer
                    AND b.fec_contro >= '".$GLOBALS['fec_inicial']." ".$GLOBALS['horaini']."'
                    AND b.fec_contro <= '".$GLOBALS['fec_final']." ".$GLOBALS['horafin']."' 
                    AND d.abr_tercer = '".$transpor[1]."' ";
      $consulta = new Consulta($sql, $this -> conexion);
      $tot = $consulta -> ret_matriz();
      $total += sizeof($tot);
      
      $sql ="SELECT b.usr_creaci 
               FROM ".BASE_DATOS.".tab_genera_noveda a,
                    ".BASE_DATOS.".tab_despac_noveda b,
                    ".BASE_DATOS.".tab_despac_vehige c,
                    ".BASE_DATOS.".tab_tercer_tercer d
              WHERE a.cod_noveda = b.cod_noveda
                    AND c.num_despac = b.num_despac
                    AND c.cod_transp = d.cod_tercer
                    AND b.fec_noveda >= '".$GLOBALS['fec_inicial']." ".$GLOBALS['horaini']."'
                    AND b.fec_noveda <= '".$GLOBALS['fec_final']." ".$GLOBALS['horafin']."'  
                    AND d.abr_tercer = '".$transpor[1]."' ";
      $consulta = new Consulta($sql, $this -> conexion);
      $tot = $consulta -> ret_matriz();
      $total += sizeof($tot);
      
      if($total=='')
        $total='0';
      $grantotal+= $total;
      
      $otros=0;
      $sql ="SELECT  b.usr_creaci 
               FROM ".BASE_DATOS.".tab_genera_noveda a,
                    ".BASE_DATOS.".tab_despac_contro b,
                    ".BASE_DATOS.".tab_despac_vehige c,
                    ".BASE_DATOS.".tab_tercer_tercer d
              WHERE a.cod_noveda = b.cod_noveda
					AND a.nov_especi !='1'
					AND a.ind_tiempo !='1'
					AND a.ind_manala !='1'
					AND a.ind_alarma !='S'			  
                    AND c.num_despac = b.num_despac
                    AND c.cod_transp = d.cod_tercer
                    AND b.fec_contro >= '".$GLOBALS['fec_inicial']." ".$GLOBALS['horaini']."'
                    AND b.fec_contro <= '".$GLOBALS['fec_final']." ".$GLOBALS['horafin']."' 
                    AND d.abr_tercer = '".$transpor[1]."'";
	  $consulta = new Consulta($sql, $this -> conexion);
	  $otr = $consulta -> ret_matriz();
	  $otros += sizeof($otr);
	  $sql ="SELECT b.usr_creaci 
               FROM ".BASE_DATOS.".tab_genera_noveda a,
                    ".BASE_DATOS.".tab_despac_noveda b,
                    ".BASE_DATOS.".tab_despac_vehige c,
                    ".BASE_DATOS.".tab_tercer_tercer d
              WHERE a.cod_noveda = b.cod_noveda
			        AND a.nov_especi !='1'
				    AND a.ind_tiempo !='1'
				    AND a.ind_manala !='1'
				    AND a.ind_alarma !='S'
                    AND c.num_despac = b.num_despac
                    AND c.cod_transp = d.cod_tercer
                    AND b.fec_noveda >= '".$GLOBALS['fec_inicial']." ".$GLOBALS['horaini']."'
                    AND b.fec_noveda <= '".$GLOBALS['fec_final']." ".$GLOBALS['horafin']."'  
                    AND d.abr_tercer = '".$transpor[1]."'";
	  $consulta = new Consulta($sql, $this -> conexion);
	  $otr = $consulta -> ret_matriz();
	  $otros += sizeof($otr);
	  if($otros=='')
		$otros='0';
	  $totot +=  $otros;	  
      
	  $especi=0;
	  $sql ="SELECT  b.usr_creaci 
               FROM ".BASE_DATOS.".tab_genera_noveda a,
                    ".BASE_DATOS.".tab_despac_contro b,
                    ".BASE_DATOS.".tab_despac_vehige c,
                    ".BASE_DATOS.".tab_tercer_tercer d
              WHERE a.cod_noveda = b.cod_noveda
					AND a.nov_especi ='1'
					AND c.num_despac = b.num_despac
                    AND c.cod_transp = d.cod_tercer
                    AND b.fec_contro >= '".$GLOBALS['fec_inicial']." ".$GLOBALS['horaini']."'
                    AND b.fec_contro <= '".$GLOBALS['fec_final']." ".$GLOBALS['horafin']."' 
                    AND d.abr_tercer = '".$transpor[1]."'";
	  $consulta = new Consulta($sql, $this -> conexion);
	  $nov_especi = $consulta -> ret_matriz();
	  $especi += sizeof($nov_especi);
	  $sql ="SELECT b.usr_creaci 
               FROM ".BASE_DATOS.".tab_genera_noveda a,
                    ".BASE_DATOS.".tab_despac_noveda b,
                    ".BASE_DATOS.".tab_despac_vehige c,
                    ".BASE_DATOS.".tab_tercer_tercer d
              WHERE a.cod_noveda = b.cod_noveda
			        AND a.nov_especi ='1'
				    AND c.num_despac = b.num_despac
                    AND c.cod_transp = d.cod_tercer
                    AND b.fec_noveda >= '".$GLOBALS['fec_inicial']." ".$GLOBALS['horaini']."'
                    AND b.fec_noveda <= '".$GLOBALS['fec_final']." ".$GLOBALS['horafin']."'  
                    AND d.abr_tercer = '".$transpor[1]."'";
	  $consulta = new Consulta($sql, $this -> conexion);
	  $nov_especi = $consulta -> ret_matriz();
	  $especi += sizeof($nov_especi);
	  if($especi=='')
		$especi='0';
	  $totne += $especi; 
      
	  $tiempo=0;
	  $sql ="SELECT  b.usr_creaci 
               FROM ".BASE_DATOS.".tab_genera_noveda a,
                    ".BASE_DATOS.".tab_despac_contro b,
                    ".BASE_DATOS.".tab_despac_vehige c,
                    ".BASE_DATOS.".tab_tercer_tercer d
              WHERE a.cod_noveda = b.cod_noveda
					AND a.ind_tiempo ='1'
					AND c.num_despac = b.num_despac
                    AND c.cod_transp = d.cod_tercer
                    AND b.fec_contro >= '".$GLOBALS['fec_inicial']." ".$GLOBALS['horaini']."'
                    AND b.fec_contro <= '".$GLOBALS['fec_final']." ".$GLOBALS['horafin']."' 
                    AND d.abr_tercer = '".$transpor[1]."'";
	  $consulta = new Consulta($sql, $this -> conexion);
	  $tiem = $consulta -> ret_matriz();
	  $tiempo += sizeof($tiem);
	  $sql ="SELECT b.usr_creaci 
               FROM ".BASE_DATOS.".tab_genera_noveda a,
                    ".BASE_DATOS.".tab_despac_noveda b,
                    ".BASE_DATOS.".tab_despac_vehige c,
                    ".BASE_DATOS.".tab_tercer_tercer d
              WHERE a.cod_noveda = b.cod_noveda
			        AND a.ind_tiempo ='1'
				    AND c.num_despac = b.num_despac
                    AND c.cod_transp = d.cod_tercer
                    AND b.fec_noveda >= '".$GLOBALS['fec_inicial']." ".$GLOBALS['horaini']."'
                    AND b.fec_noveda <= '".$GLOBALS['fec_final']." ".$GLOBALS['horafin']."'  
                    AND d.abr_tercer = '".$transpor[1]."'";
	  $consulta = new Consulta($sql, $this -> conexion);
	  $tiem = $consulta -> ret_matriz();
	  $tiempo += sizeof($tiem);
	   if($tiempo=='')
		$tiempo='0';
	  $totst += $tiempo;
	  
	  $mantiene=0;
	  $sql ="SELECT  b.usr_creaci 
               FROM ".BASE_DATOS.".tab_genera_noveda a,
                    ".BASE_DATOS.".tab_despac_contro b,
                    ".BASE_DATOS.".tab_despac_vehige c,
                    ".BASE_DATOS.".tab_tercer_tercer d
              WHERE a.cod_noveda = b.cod_noveda
					AND a.ind_manala ='1'
					AND c.num_despac = b.num_despac
                    AND c.cod_transp = d.cod_tercer
                    AND b.fec_contro >= '".$GLOBALS['fec_inicial']." ".$GLOBALS['horaini']."'
                    AND b.fec_contro <= '".$GLOBALS['fec_final']." ".$GLOBALS['horafin']."' 
                    AND d.abr_tercer = '".$transpor[1]."'";
	  $consulta = new Consulta($sql, $this -> conexion);
	  $man = $consulta -> ret_matriz();
	  $mantiene += sizeof($man);
	  $sql ="SELECT b.usr_creaci 
               FROM ".BASE_DATOS.".tab_genera_noveda a,
                    ".BASE_DATOS.".tab_despac_noveda b,
                    ".BASE_DATOS.".tab_despac_vehige c,
                    ".BASE_DATOS.".tab_tercer_tercer d
              WHERE a.cod_noveda = b.cod_noveda
			        AND a.ind_manala ='1'
				    AND c.num_despac = b.num_despac
                    AND c.cod_transp = d.cod_tercer
                    AND b.fec_noveda >= '".$GLOBALS['fec_inicial']." ".$GLOBALS['horaini']."'
                    AND b.fec_noveda <= '".$GLOBALS['fec_final']." ".$GLOBALS['horafin']."'  
                    AND d.abr_tercer = '".$transpor[1]."'";
	  $consulta = new Consulta($sql, $this -> conexion);
	  $man = $consulta -> ret_matriz();
	  $mantiene += sizeof($man);
	  if($mantiene=='')
		$mantiene='0';
	  $totma += $mantiene;
	  
	  $genera=0;
	  $sql ="SELECT  b.usr_creaci 
               FROM ".BASE_DATOS.".tab_genera_noveda a,
                    ".BASE_DATOS.".tab_despac_contro b,
                    ".BASE_DATOS.".tab_despac_vehige c,
                    ".BASE_DATOS.".tab_tercer_tercer d
              WHERE a.cod_noveda = b.cod_noveda
					AND a.ind_alarma ='S'
					AND c.num_despac = b.num_despac
                    AND c.cod_transp = d.cod_tercer
                    AND b.fec_contro >= '".$GLOBALS['fec_inicial']." ".$GLOBALS['horaini']."'
                    AND b.fec_contro <= '".$GLOBALS['fec_final']." ".$GLOBALS['horafin']."' 
                    AND d.abr_tercer = '".$transpor[1]."'";
	  $consulta = new Consulta($sql, $this -> conexion);
	  $alarma = $consulta -> ret_matriz();
	  $genera += sizeof($alarma);
	  $sql ="SELECT b.usr_creaci 
               FROM ".BASE_DATOS.".tab_genera_noveda a,
                    ".BASE_DATOS.".tab_despac_noveda b,
                    ".BASE_DATOS.".tab_despac_vehige c,
                    ".BASE_DATOS.".tab_tercer_tercer d
              WHERE a.cod_noveda = b.cod_noveda
			        AND a.ind_alarma ='S'
				    AND c.num_despac = b.num_despac
                    AND c.cod_transp = d.cod_tercer
                    AND b.fec_noveda >= '".$GLOBALS['fec_inicial']." ".$GLOBALS['horaini']."'
                    AND b.fec_noveda <= '".$GLOBALS['fec_final']." ".$GLOBALS['horafin']."'  
                    AND d.abr_tercer = '".$transpor[1]."'";
	  $consulta = new Consulta($sql, $this -> conexion);
	  $alarma = $consulta -> ret_matriz();
	  $genera += sizeof($alarma);
	  if($genera=='')
		$genera='0';
	  $totga += $genera;  
	  
      /// Pinta el resto
      if($especi || $genera || $mantiene || $tiempo || $otros){
            $formulario -> nueva_tabla();
            $formulario -> linea("NIT: ".$transpor[0]." - Trasnportadora: ".$transpor[1],1,"t2");
            $formulario -> nueva_tabla();
            $formulario -> linea("Total",0,"t");
            echo "<td align='left' class='celda_titulo' style='cursor:pointer;' onclick=\"infoNovEmp('TO','".$transpor[1]."','".$GLOBALS['fec_inicial']."','".$GLOBALS['fec_final']."','".$GLOBALS['horaini']."', '".$GLOBALS['horafin']."');\">";
      echo "<b>$total ";
            echo "</td></tr>";
            $formulario -> linea("Otros",0,"t");
            echo "<td align='left' class='celda_titulo' style='cursor:pointer;' onclick=\"infoNovEmp('OT','".$transpor[1]."','".$GLOBALS['fec_inicial']."','".$GLOBALS['fec_final']."','".$GLOBALS['horaini']."', '".$GLOBALS['horafin']."');\">";
      echo "<b>$otros ";
            echo "</td></tr>";
            $formulario -> linea("Novedad Especial",0,"t");
            echo "<td align='left' class='celda_titulo' style='cursor:pointer;' onclick=\"infoNovEmp('NE','".$transpor[1]."','".$GLOBALS['fec_inicial']."','".$GLOBALS['fec_final']."','".$GLOBALS['horaini']."', '".$GLOBALS['horafin']."');\">";
      echo "<b>$especi ";
            echo "</td></tr>";
            $formulario -> linea("Solicita Tiempo",0,"t");
            echo "<td align='left' class='celda_titulo' style='cursor:pointer;' onclick=\"infoNovEmp('ST','".$transpor[1]."','".$GLOBALS['fec_inicial']."','".$GLOBALS['fec_final']."','".$GLOBALS['horaini']."', '".$GLOBALS['horafin']."');\">";
      echo "<b>$tiempo ";
            echo "</td></tr>";
            $formulario -> linea("Mantiene Alerta",0,"t");
            echo "<td align='left' class='celda_titulo' style='cursor:pointer;' onclick=\"infoNovEmp('MA','".$transpor[1]."','".$GLOBALS['fec_inicial']."','".$GLOBALS['fec_final']."','".$GLOBALS['horaini']."', '".$GLOBALS['horafin']."');\">";
      echo "<b>$mantiene ";
            echo "</td></tr>";
            $formulario -> linea("Genera Alarma",0,"t");
            echo "<td align='left' class='celda_titulo' style='cursor:pointer;' onclick=\"infoNovEmp('GA','".$transpor[1]."','".$GLOBALS['fec_inicial']."','".$GLOBALS['fec_final']."','".$GLOBALS['horaini']."', '".$GLOBALS['horafin']."');\">";
      echo "<b>$genera ";
            echo "</td></tr>";
        $novedatotal[$h]['NIT']=$transpor[0];
        $novedatotal[$h]['Transportadora']=$transpor[1];
        $novedatotal[$h]['Total']=$total;
        $novedatotal[$h]['Otros']=$otros;
        $novedatotal[$h]['Novedad Especial']=$especi;
        $novedatotal[$h]['Solicita Tiempo']=$tiempo;
        $novedatotal[$h]['Mantiene Alerta']=$mantiene;
        $novedatotal[$h]['Genera Alarma']=$genera;
        $h++;
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
        $novedatotal[$h]['NIT']="Gran Total";
        $novedatotal[$h]['Transportadora']=$h;
        $novedatotal[$h]['Total']=$grantotal;
        $novedatotal[$h]['Otros']=$totot;
        $novedatotal[$h]['Novedad Especial']=$totne;
        $novedatotal[$h]['Solicita Tiempo']=$totst;
        $novedatotal[$h]['Mantiene Alerta']=$totma;
        $novedatotal[$h]['Genera Alarma']=$totga;
    $_SESSION['LIST_TOTAL']=$novedatotal;
    
    $formulario -> nueva_tabla();
    $formulario -> botoni("Excel","exportarXls2()",0);
    //$formulario -> oculto("Central",CENTRAL,0);
    $formulario -> oculto("Aplicacion",BASE_DATOS,0);
    $formulario -> nueva_tabla();

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
  
  function infoNovEmp()
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
                   b.obs_contro as Observacion, b.usr_creaci as Usuario
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
                 AND d.abr_tercer = '".$_POST['empres']."') 
           UNION ALL
           (SELECT b.num_despac, a.nom_noveda, b.fec_noveda ,
                   b.des_noveda, b.usr_creaci
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
                 AND d.abr_tercer = '".$_POST['empres']."')
           ORDER BY 3,5";
    
    $consulta = new Consulta($sql, $this -> conexion);
    $despachos = $consulta -> ret_matriz('a');
    $_SESSION['LIST_ARRAY'] = $despachos;
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
		
		$formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "INFORMACION DE NOVEDADES", "formulario");
    $formulario -> nueva_tabla(); 
    $formulario -> botoni("Excel","exportarXls()",0);
    $formulario -> botoni("Cerrar","ClosePopup()",1);
    $formulario -> oculto("dir_aplica\" id=\"dir_aplicaID\"",DIR_APLICA_CENTRAL,0);
    $formulario -> oculto("based\" id=\"basedID\"",BASE_DATOS,0);

    $formulario -> cerrar();
    $formulario -> nueva_tabla();
    $formulario -> linea("Total de Novedades: ".sizeof($despachos),0,"t2","15%");
    
    $formulario -> nueva_tabla();
    $formulario -> linea("Usuario",0,"t2","15%");
    $formulario -> linea("Despacho",0,"t2","15%");
    $formulario -> linea("Novedad",0,"t2","15%");
    $formulario -> linea("Fecha",0,"t2","15%");
    $formulario -> linea("Observacion",1,"t2","15%");
    
    foreach($despachos AS $despacho){
      $formulario -> linea($despacho['Usuario'],0,"i","15%");
      $formulario -> linea($despacho['Despacho'],0,"i","15%");
      $formulario -> linea($despacho['Novedad'],0,"i","15%");
      $formulario -> linea($despacho['Fecha'],0,"i","15%");
      $formulario -> linea($despacho['Observacion'],1,"i","15%");
    }
    echo "<br>";
    $formulario -> nueva_tabla(); 
    $formulario -> botoni("Excel","exportarXls()",0);
    $formulario -> botoni("Cerrar","ClosePopup()",1);
    $formulario -> cerrar();
  }

}
//$service= new  InfNovEmp($this->conexion);
$service= new  InfNovEmp($_SESSION['conexion']);
?>