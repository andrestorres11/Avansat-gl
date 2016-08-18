<?php

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
          $this -> Formulario();
          break;
        case "2":
          $this -> registrar();
          break;
        default:
          $this -> Formulario();
          break;
      }
 }





 function Formulario()
 {
   $inicio[0][0] = 0;
   $inicio[0][1] = '-';
	 //codigo de ruta
   $query = "SELECT cod_usuari,TRIM( UPPER( nom_usuari ) ) AS nom_usuari FROM ".BASE_DATOS.".tab_genera_usuari ORDER BY 2 ASC ";
   $consulta = new Consulta( $query, $this -> conexion );
	 $usuar = $consulta -> ret_matriz();
   $usuari = array_merge($inicio, $usuar);
   if($GLOBALS[usuari]){
     $query = "SELECT cod_usuari,nom_usuari 
               FROM ".BASE_DATOS.".tab_genera_usuari 
               WHERE cod_usuari = '".$GLOBALS[usuari]."'";
     $consulta = new Consulta( $query, $this -> conexion );
  	 $usuar = $consulta -> ret_matriz();
     //$usuari = array_merge($usuar,usuari);
     $usuari = array_merge($usuar,$usuari);
   }  
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
   $formulario = new Formulario ("index.php","post","Horarios de Monitoreo","form_ins\" id=\"formularioID");
   $formulario -> nueva_tabla();
   $formulario -> lista("Usuario","usuari\" id=\"usuariID",$usuari,1); 
   $formulario -> texto ("Fecha Inicio","text","fecini\" id=\"feciniID",0,9,12,"","$GLOBALS[fecini]");
   $formulario -> texto ("Hora Inicio","text","horini\" id=\"horiniID",1,9,12,"","$GLOBALS[horini]");
   $formulario -> texto ("Fecha Final","text","fecfin\" id=\"fecfinID",0,9,12,"","$GLOBALS[fecfin]");
   $formulario -> texto ("Hora Final","text","horfin\" id=\"horfinID",1,9,12,"","$GLOBALS[horfin]");
   $formulario -> radio ("Limpios","limpio","0",1,9,12,"1","0");
   $formulario -> radio ("No Limpios","limpio","1",1,9,12,"0","0");
  $formulario -> nueva_tabla();
  $formulario -> oculto("window","central",0);
  $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
  $formulario -> oculto("opcion\" id=\"opcionID",1,0);

  $formulario -> nueva_tabla();
  if($GLOBALS['usuari'] && $GLOBALS['fecini'] && $GLOBALS['fecfin']){
      
              

    $usuario = $_SESSION["datos_usuario"]["cod_usuari"];
    $formulario -> nueva_tabla();
    $formulario -> botoni("Cancelar","aceptar_ins(2)",0);
    $query = "SELECT 1
   				FROM ".BASE_DATOS.".tab_monito_encabe a
   			   WHERE a.ind_estado = 1 AND
                 a.cod_usuari = '".$GLOBALS['usuari']."' AND 
                 (fec_inicia <= '".$GLOBALS['fecini']." ".$GLOBALS['horini']."' AND 
                  fec_finalx >= '".$GLOBALS['fecini']." ".$GLOBALS['horini']."') 
                 ";
//echo $query;
    $consulta = new Consulta($query, $this -> conexion);
    $fec = $consulta -> ret_matriz();
    $mensaje ='';
    if($fec){
      $mensaje =  "Cruce de Horarios con la Fecha Inicial".$link_a;
    }
    $query = "SELECT 1
   				FROM ".BASE_DATOS.".tab_monito_encabe a
   			   WHERE a.ind_estado = 1 AND
                 a.cod_usuari = '".$GLOBALS['usuari']."' AND 
                 (fec_inicia <= '".$GLOBALS['fecfin']." ".$GLOBALS['horfin']."' AND 
                  fec_finalx >= '".$GLOBALS['fecfin']." ".$GLOBALS['horfin']."') 
                 ";
//echo '<br />'.$query;
    $consulta = new Consulta($query, $this -> conexion);
    $fec = $consulta -> ret_matriz();
    if($fec){
      $mensaje .=  ".<br />Cruce de Horarios con la Fecha Final".$link_a;
    }
    if($mensaje!=''){
      $mens = new mensajes();
      $mens -> error("",$mensaje);
      die();
    }
    $query = "SELECT a.cod_tercer,a.abr_tercer
   				FROM ".BASE_DATOS.".tab_tercer_tercer a,
   			     	 ".BASE_DATOS.".tab_tercer_activi b
   			   WHERE a.cod_tercer = b.cod_tercer AND
   			         b.cod_activi = ".COD_FILTRO_EMPTRA."
   			         ORDER BY 2
   			 ";
    $consulta = new Consulta($query, $this -> conexion);
    $transpor = $consulta -> ret_matriz();
    $formulario -> nueva_tabla();
    echo '<td class="celda_titulo2"><b>Numero de Despachos Seleccionados</b>
          <input type="text" maxlength="2" readonly="true" value="0" size="2" style="background:none; border:none; color:#336600; font-weight:bold;" id="numID" >
          </td></tr>';
    $formulario -> nueva_tabla();
    $formulario -> oculto("transp\" id=\"transpID",sizeof($transpor),0);
    $formulario -> botoni("Aceptar","a  ceptar_ins(3)",0);
        
    echo '</tr></table><div style="height:300px; overflow:scroll"><table width="100%"  cellspacing="0" cellpadding="4" class="formulario" id="algoID">
    <tbody><tr>'; 
    echo '<td align="left" class="celda_titulo2"><b>Transportadora</b></td>
    <td align="left" class="celda_titulo2"><b>N.Despachos</b></td>
    <td align="left" class="celda_titulo2"><b>S/N</b></td>
    <td align="left" class="celda_titulo2"><b>Transportadora</b></td>
    <td align="left" class="celda_titulo2"><b>N.Despachos</b></td>
    <td align="left" class="celda_titulo2"><b>S/N</b></td>';

    for($i=0;$i<=sizeof($transpor);$i++){
       echo '<tr><td align="left" class="celda_titulo"><b>'.$transpor[$i][1].'</b></td>';
       $query = "SELECT COUNT(a.num_despac)
  					  FROM ".BASE_DATOS.".tab_despac_despac a,
                   ".BASE_DATOS.".tab_despac_vehige b 
  					  WHERE a.fec_salida Is Not Null AND 
  							    a.fec_salida <= NOW() AND 
  							    a.fec_llegad Is Null AND 
  							    a.ind_anulad = 'R' AND 
  							    a.ind_planru = 'S' AND
                    a.num_despac = b.num_despac AND 
  							    b.cod_transp = '".$transpor[$i][0]."'";
  		 $consulta = new Consulta($query, $this -> conexion);
       $despac = $consulta -> ret_matriz();
        if(!$despac)
         $despac = '0';
       else  
         $despac = $despac[0][0];
       echo '<td align="left" id="despacID'.$i.'" class="celda_titulo">'.$despac.'</td> ';
       echo "<td align='left' class='celda_titulo'>
             <input type='checkbox' name='tercer$i' onclick='sum(".$i.")' id='tercerID$i' value='".$transpor[$i][0]."'>
             </td>";
       $i++;
       echo '<td align="left" class="celda_titulo"><b>'.$transpor[$i][1].'</b></td>';
       $query = "SELECT COUNT(a.num_despac)
  					  FROM ".BASE_DATOS.".tab_despac_despac a,
                   ".BASE_DATOS.".tab_despac_vehige b 
  					  WHERE a.fec_salida Is Not Null AND 
  							    a.fec_salida <= NOW() AND 
  							    a.fec_llegad Is Null AND 
  							    a.ind_anulad = 'R' AND 
  							    a.ind_planru = 'S' AND
                    a.num_despac = b.num_despac AND 
  							    b.cod_transp = '".$transpor[$i][0]."'";
  		 $consulta = new Consulta($query, $this -> conexion);
       $despac = $consulta -> ret_matriz();
       if(!$despac)
         $despac = '0';
       else  
         $despac = $despac[0][0];
       echo '<td align="left" id="despacID'.$i.'" class="celda_titulo">'.$despac.'</td> '; 
       echo "<td align='left' class='celda_titulo'>
             <input type='checkbox' name='tercer$i' id='tercerID$i' value='".$transpor[$i][0]."'>
             </td></tr>";
    }
    
    echo "</table></div>";
    $formulario -> nueva_tabla();
    $formulario -> botoni("Aceptar","aceptar_ins(3)",0);
  }
  else
    $formulario -> botoni("Aceptar","aceptar_ins(1)",0);

  $formulario -> nueva_tabla();
  echo "<br><br><br><br><br><br><br><br>";
  $formulario -> cerrar();
  


 }

   function registrar()
    {		
        global $HTTP_POST_FILES;

        $usuario = $_SESSION["datos_usuario"]["cod_usuari"];
        $error = 0;
        $query= "SELECT MAX(cod_consec) 
                 FROM ".BASE_DATOS.".tab_monito_encabe";
        $max = new Consulta($query, $this -> conexion,"BR");
        $max = $max -> ret_matriz();
        $max = $max[0][0]+1;


        
        
        $encabe = "INSERT INTO ".BASE_DATOS.".tab_monito_encabe
										( 
											cod_consec, cod_usuari, fec_inicia, 
											fec_finalx, ind_limpio, usr_creaci, 
                                                                                        fec_creaci
										)
										VALUES
										( 
											'".$max."', '".$_POST["usuari"]."', '".$_POST["fecini"]." ".$_POST["horini"].":00', 
											'".$_POST["fecfin"]." ".$_POST["horfin"].":00','".$_POST["limpio"]."', '$usuario',NOW()
										)";
        
    
			  $insercion = new Consulta( $encabe, $this -> conexion, "BR" );
        for( $i = 1 ; $i <= $_POST[transp]; $i++ )
        {
          if( $_POST["tercer$i"] )
          {
				    $detall = "INSERT INTO ".BASE_DATOS.".tab_monito_detall
										( 
											cod_consec, cod_tercer, usr_creaci, 
                      fec_creaci 
										)
										VALUES
										( 
											'".$max."', '".$_POST["tercer$i"]."', '$usuario',
                      NOW()
										)";
					  $insercion = new Consulta( $detall, $this -> conexion, "R" );
            
          }
        }

 
        if( $insercion = new Consulta("COMMIT", $this -> conexion))
        {
            $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">Insertar Otro Horario</a></b>";

            if($msm)
                $mensaje = $msm;
            $mensaje .=  "El Horario se Inserto con Exito".$link_a;
            $mens = new mensajes();
            $mens -> correcto("INSERTAR Horario",$mensaje);
        }
    }

  
	
	
	
	
}//FIN CLASE PROC_DESPAC

   $proceso = new HoraMoni($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);



?>
