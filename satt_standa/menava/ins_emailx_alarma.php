<?php
session_start();
class Proc_contro{
  var $conexion, $cod_aplica, $usuario;

  function __construct($co, $us, $ca){
    $this -> conexion = $co;
    $this -> usuario = $us;
    $this -> cod_aplica = $ca;
    $this -> principal();
  }
  function principal(){
    if(!isset($_REQUEST[opcion])){
      $this -> Formulario();
    }else{
      switch($_REQUEST[opcion]){
        case "1":
          $this -> Formulario();
        break;
        case "2":
          $this -> Insertar();
        break;
        case "addOpe":
          $this -> addOpe();
        break;
        case "addDisp":
          $this -> addDisp();
        break;
        case "getLista":
          $this -> getLista();
        break;
      }//FIN SWITCH
    }// FIN ELSE GLOBALS OPCION
  }//FIN FUNCION PRINCIPAL
  function Formulario(){
    $_SESSION['BD_STANDA'] = BD_STANDA;
    $datos_usuario = $this -> usuario -> retornar();
    $inicio[0][0] = 0;
    $inicio[0][1] = "-";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/emailx.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";
    
    
    $formulario = new Formulario ("index.php","post","ALARMAS","form_item","",0);
    //Se consultan las alarmas 
    $query = "SELECT a.cod_alarma FROM ".BASE_DATOS.".tab_genera_alarma a  ORDER BY a.cod_alarma ASC LIMIT 1 ";
    $consulta = new Consulta($query, $this -> conexion);
    $alarmin = $consulta -> ret_matriz();
    $query = "SELECT a.cod_alarma,CONCAT(a.nom_alarma,' (',a.cant_tiempo,')'),a.cod_colorx
              FROM ".BASE_DATOS.".tab_genera_alarma a
              WHERE a.cod_alarma > '".$alarmin[0][0]."'
              ORDER BY a.cant_tiempo";

    $consulta = new Consulta($query, $this -> conexion);
    $alarmas = $consulta -> ret_matriz();

    //Se consultan las transportadoras activas
    $query = "SELECT a.cod_transp,TRIM( UPPER( b.abr_tercer ) ) AS abr_tercer
              FROM ".BD_STANDA.".tab_mensaj_bdsata a,
                   ".BASE_DATOS.".tab_tercer_tercer b
              WHERE a.nom_bdsata = '".BASE_DATOS."' AND
                    a.cod_transp = b.cod_tercer AND
                    a.ind_estado = '1'";
                    
    if($datos_usuario["cod_perfil"] == ""){
      $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
      if($filtro -> listar($this -> conexion)){
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND a.cod_transp = '$datos_filtro[clv_filtro]' ";
        $indfilt = 1;
      }
    }else{
      $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
      if($filtro -> listar($this -> conexion)){
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND a.cod_transp = '$datos_filtro[clv_filtro]' ";
        $indfilt = 1;
      }
    }
    if(isset($_REQUEST[transp])){
        $query2 = $query.((int)$_REQUEST[transp]!=999?"  AND a.cod_transp = '".$_REQUEST[transp]."' ":'');
        echo "<input type='hidden' name='htransp' id='htranspID' value='".$_REQUEST[transp]."'>";
    }
    $query  .= " ORDER BY 2";
    $query2 .= " ORDER BY 2";
    $consulta  = new Consulta($query, $this -> conexion);
    $tranpsact = $consulta -> ret_matriz();
    
    if(isset($_REQUEST[transp])){
      $consulta  = new Consulta($query2, $this -> conexion);
      $activado = $consulta -> ret_matriz();
    }
    
    
    
    if($tranpsact){
     if(!$indfilt) $tranpsact = array_merge($inicio,$tranpsact);
      $formulario -> nueva_tabla();
      $formulario -> linea("Asignaci&oacute;n de Alerta a Operadores de Transportadoras",1,"t2");
      $formulario -> nueva_tabla();
      $formulario -> lista("Transportadora","transp",$tranpsact,1);
      echo "<script type='text/javascript'>
              if(document.getElementById('htranspID')){
                document.form_item.transp.value = document.getElementById('htranspID').value;
              }
            </script>";
      $formulario -> nueva_tabla();
      echo "<input type='hidden' id='opcionID' name='opcion' value=1 />";
      $formulario -> oculto("window","central",0);
      $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
      $formulario -> boton("Consultar","button\" onClick=\"consulope(form_item)",1);
      echo "<input type='hidden' name='dir_aplica_central' id='dir_aplica_centralID' value='".DIR_APLICA_CENTRAL."'>"; 
      
      $array_operadores = array();
      
      if(isset($_REQUEST[transp]) && $activado)
      for($l = 0; $l < sizeof($activado); $l++){
        $formulario -> oculto("listransp[$l]",$activado[$l][0],0);
        
        $query = "SELECT a.cod_operad,a.nom_operad,a.dns_operad,
                         IF(a.ind_emailx = '1','E-mail','SMS'),
                         IF(a.ind_estado = '1','Activo','Inactivo')
                  FROM ".BD_STANDA.".tab_mensaj_operad a,
                       ".BD_STANDA.".tab_mensaj_dispos b
                  WHERE a.cod_operad = b.cod_operad 
                    AND ((a.cod_transp = '".$activado[$l][0]."' 
                    AND a.nom_bdsata = '".BASE_DATOS."') 
                    OR (a.cod_transp IS NULL AND a.nom_bdsata IS NULL))
                  GROUP BY 1 ORDER BY 2";

        $consulta = new Consulta($query, $this -> conexion);
        $operador = $consulta -> ret_matriz();
        $formulario -> nueva_tabla();
        $formulario -> linea("Asignaci&oacute;n de Alerta a Operadores Transportadora :: ".$activado[$l][1]."",0,"t2");
        
        $formulario -> nueva_tabla();
        
        if(!sizeof($operador)){
          $array_operadores[]=0;
          $formulario -> linea("No Existen Dispositivos o E-mails Registrados a Esta Transportadora.",0,"e");
        }else{
          $formulario -> linea("",0,"t");  
          $array_operadores[]=1;
        }
        $formulario -> linea("",0,"t");
        $formulario -> linea("",0,"t");
        $formulario -> linea("Estado ",0,"t");
        $formulario -> linea("Genera Alerta Novedad",0,"t");
        
        
        for($j = 0; $j < sizeof($alarmas); $j++){
          //if($j == sizeof($alarmas)-1)
           //$formulario -> linea("<td style ='font-size: 8pt;color: #000000; text-align: center; background-color: ".$alarmas[$j][2]."'>".$alarmas[$j][1]."</td>",1,"t");
          //else
           $formulario -> linea("<td style ='font-size: 8pt;color: #000000; text-align: center; background-color: ".$alarmas[$j][2]."'>".$alarmas[$j][1]."</td>",0,"t");
        }
        $formulario -> linea("Borrar",1,"t");
      
    
      
      $a=0;
      for($i = 0; $i < sizeof($operador); $i++){
        
        $query = "SELECT a.dir_dispos,if(a.ind_estado = '1','Activo','Inactivo')
                  FROM ".BD_STANDA.".tab_mensaj_dispos a
                  WHERE a.cod_transp = '".$activado[$l][0]."' AND
                        a.nom_bdsata = '".BASE_DATOS."' AND
                        a.cod_operad = ".$operador[$i][0]."";
        $consulta = new Consulta($query, $this -> conexion);
        $disposit = $consulta -> ret_matriz();  

        //$formulario -> linea($operador[$i][1]." :: ".$operador[$i][2]." :: Envio Mensaje ".$operador[$i][3]." :: Estado ".$operador[$i][4],1,"h");
        $formulario -> linea("# Dispositivo",0,"t");  
        $formulario -> linea("Estado",0,"t");

        for($j = 0; $j < sizeof($alarmas); $j++){
          $formulario -> linea("",0,"t");
          if($j == sizeof($alarmas)-1) $formulario -> linea("",1,"t");
          else $formulario -> linea("",0,"t");
        }
        for($j = 0; $j < sizeof($disposit); $j++){
          $query = "SELECT a.ind_novala, ind_estado
                    FROM ".BD_STANDA.".tab_mensaj_dispos a
                    WHERE a.cod_transp = '".$activado[$l][0]."' AND
                          a.nom_bdsata = '".BASE_DATOS."' AND
                          a.dir_dispos = '".$disposit[$j][0]."' AND
                          a.cod_operad = ".$operador[$i][0]."";
          $consulta = new Consulta($query, $this -> conexion);
          $novastes = $consulta -> ret_matriz();
          $formulario -> linea($disposit[$j][0]."@".$operador[$i][2],0,"i");
          $formulario -> linea($disposit[$j][1],0,"i");
          if((int)$novastes[0][1]==1){
            echo "<td class='celda_etiqueta'>&nbsp;</td><td class='celda'><input type='checkbox' name='estado[$a]' value='".$disposit[$j][0]."|".$operador[$i][0]."|".$novastes[0][1]."' onclick='activate(this)' checked='' /></td>";
          }else{
            echo "<td class='celda_etiqueta'>&nbsp;</td><td class='celda'><input type='checkbox' name='estado[$a]' value='".$disposit[$j][0]."|".$operador[$i][0]."|0' onclick='activate(this)'/></td>";
          }
          
          if((int)$novastes[0][0]==1){
            echo "<td class='celda'><input type='hidden' name='novala[$a]' value='".$disposit[$j][0]."|".$operador[$i][0]."|".$activado[$l][0]."' />";
            echo "<input type='checkbox' name='novala2[$a]' value='".$disposit[$j][0]."|".$operador[$i][0]."|".$novastes[0][0]."' onclick='activate(this)' checked='' /></td>";
          }else{
            echo "<td class='celda'><input type='hidden' name='novala[$a]' value='".$disposit[$j][0]."|".$operador[$i][0]."|".$activado[$l][0]."' />";
            echo "<input type='checkbox' name='novala2[$a]' value='".$disposit[$j][0]."|".$operador[$i][0]."|0' onclick='activate(this)'/></td>";
          }
          $b = 0;
          for($k = 0; $k < sizeof($alarmas); $k++){
            $asigchek = 0;
            $query = "SELECT a.cod_alarma
                      FROM ".BD_STANDA.".tab_mensaj_alarma a
                      WHERE a.cod_transp = '".$activado[$l][0]."' AND
                            a.nom_bdsata = '".BASE_DATOS."' AND
                            a.dir_dispos = '".$disposit[$j][0]."' AND
                            a.cod_operad = ".$operador[$i][0]." AND
                            a.cod_alarma = ".$alarmas[$k][0]."";
          
            $consulta = new Consulta($query, $this -> conexion);
            $registes = $consulta -> ret_matriz();
            if($registes) $asigchek = 1;
             //if($k == sizeof($alarmas)-1)
              //$formulario -> caja("","codigo[$a][".$b."]",$disposit[$j][0]."|".$operador[$i][0]."|".$alarmas[$k][0]."|".$activado[$l][0],$asigchek,1);
             //else
              $formulario -> caja("","codigo[$a][".$b."]",$disposit[$j][0]."|".$operador[$i][0]."|".$alarmas[$k][0]."|".$activado[$l][0],$asigchek,0);
            $b++;
          }
          echo "<td class='celda'><input class='crmButton small save' type='button' name='borrar' id='borrarID' value='Borrar' onclick=\"borrarFila(this);\"></td></tr>";
          $a++;
        }
      }
     
      echo "<table width='100%' cellspacing='0' cellpadding='4' class='formulario' id='addOpeID'>
              <tbody>
                <tr>
                  <td align='center'>
                    <input type='button' value='Nuevo' name='Nuevo' onclick='openForm();' class='crmButton small save'>
                  </td>
                </tr>
              </tbody>
            </table>
            <div id='formDispositivosID' style='visibility: hidden;'>
              <table width='100%' cellspacing='0' cellpadding='4' class='formulario'>
                  <tbody>
                    <tr>
                      <td align='left' class='celda_titulo2' colspan='2'><b>Información Basica del Dispositvo</b></td>
                    </tr>
                    <tr>
                      <td align='right' class='celda_titulo' style='vertical-align: top;'>*Dispositivo</td>
                      <td class='celda_info'>
                        <input type='text' maxlength='30' size='30' name='numdis' id='numdisID' onblur=\"this.className='campo_texto'\" onfocus=\"this.className='campo_texto_on'\" class=\"campo_texto\">
                      </td>
                    </tr>
                    <tr>
                    <td align='right' class='celda_titulo' style='vertical-align: top;'>*Operardor</td>
                    <td class='celda_info'>
                        <div id='dnsIDDIV' style='padding:0; margin:0;'>";    
                        echo "<select class='form_01' id='operadorID' name='operador' onchange='setOperador()'>";
                        echo "<option value='0'>--</option>";
                        echo "<option value='999'>OTRO</option>";
                        foreach($this->getOperadores($activado[$l][0]) as $ope):
                          echo "<option value='".$ope[cod_operad]."'>".$ope[dns]."</option>";
                        endforeach;
                        echo "</select>";
                 echo  "</div>
                    </td>
                    </tr>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td align='center'><input type='button' value='Guardar' name='guardar' onclick='addOpe();' class='crmButton small save'></td>
                      <td align='center'><input type='button' value='Cancelar' name='cancelar' onclick='closeForm();' class='crmButton small save'></td>
                    </tr>
                  </tfoot>
              </table>
             </div>";
    }
    
      
    
      if(isset($_REQUEST[transp]) && $activado && in_array(1, $array_operadores)){
         
          $formulario -> oculto("cont",$a,0);
          $formulario -> oculto("totala",sizeof($alarmas),0);
          $formulario -> oculto("window","central",0);
          $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
          

          
          echo "<table width='100%' cellspacing='0' cellpadding='4' class='formulario'>
              <tbody>
                <tr>
                  <td align='center'>
                    <input type='button' value='Aceptar' name='Aceptar' onclick=\"if(confirm('Esta Seguro de Actualizar la Informacion de Alarmas?')){ document.getElementById('opcionID').value = 2; form_item.submit();}\" class='crmButton small save'>
                  </td>
                </tr>
              </tbody>
            </table>";
          
          
      }
      
      echo  "<table width='100%' cellspacing='0' cellpadding='4' class='formulario' id='formOperadoresID' style='display: none;'>
                  <tbody>
                    <tr>
                      <td align='left' class='celda_titulo2' colspan='2'><b>Información Basica del Operador</b></td>
                    </tr>
                    <tr style='display: none;'><td><input type='hidden' name='indmai' id='indamiID' value='1'></td></tr>
                    <tr>
                      <td align='right' class='celda_titulo'>*Nombre</td>
                      <td class='celda_info'>
                        <input type='text' maxlength='30' size='30' name='nombre' id='nombreID' onblur=\"this.className='campo_texto'\" onfocus=\"this.className='campo_texto_on'\" class=\"campo_texto\">
                      </td>
                    </tr>
                    <tr>
                      <td align='right' class='celda_titulo'>*DNS @</td>
                      <td class='celda_info'>
                        <input type='text' maxlength='40' size='40' name='dns' id='dnsID' onblur=\"this.className='campo_texto'\" onfocus=\"this.className='campo_texto_on'\" class=\"campo_texto\">
                      </td>
                    </tr>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td align='center'><input type='button' value='Guardar' name='guardar' onclick=\"Ope1('add');\" class='crmButton small save'></td>
                      <td align='center'><input type='button' value='Cancelar' name='cancelar' onclick=\"Ope1('can');\" class='crmButton small save'></td>
                    </tr>
                  </tfoot>
              </table>";
      
      $formulario -> cerrar();
      
    }else{
    	$formulario -> nueva_tabla();
    	$formulario -> linea("Para Realizar Operaciones en Esta Opci&oacute;n Debe Inicialmente Activar el Envio de Mensajes en la Opci&oacute;n :: Configuracion > Mensajes de Texto > Configuracion",1,"e");
    }
  }
  
  function addOpe(){
    
    @session_start();
    include_once( "../lib/general/conexion_lib.inc" );
    include_once( "../lib/general/tabla_lib.inc" );
    include_once( "../lib/general/constantes.inc" );
    include_once( "../lib/mensajes_lib.inc" );

    $BASE = $_SESSION[BASE_DATOS];
    $BD_STANDA = $_SESSION[BD_STANDA];
    $this -> conexion = new Conexion( $_SESSION['HOST'], $_SESSION[USUARIO], $_SESSION[CLAVE], $BASE  );//cod_transp
    $datos_usuario = $_SESSION[USUARIO];
    $usuario=$datos_usuario["cod_usuari"];
    $query = "SELECT MAX(cod_operad)
              FROM ".$BD_STANDA.".tab_mensaj_operad";
    $consulta = new Consulta($query, $this -> conexion);
    $consecut = $consulta -> ret_matriz();
    $consecut[0][0] += 1;
    
    
    $query = "INSERT INTO ".$BD_STANDA.".tab_mensaj_operad
                (cod_operad,cod_transp,nom_bdsata,nom_operad,dns_operad,ind_emailx,usr_creaci,fec_creaci)
              VALUES (".$consecut[0][0].",'".$_REQUEST[transp]."','".$BASE."','".$_REQUEST[nombre]."','".$_REQUEST[dns]."','".$_REQUEST[indmai]."','".$usuario."',NOW())";
    $consulta = new Consulta($query, $this -> conexion,"BR");
   
    if($consulta = new Consulta ("COMMIT", $this -> conexion)){
      $this->getLista($consecut[0][0]);
    }
  }
  function addDisp(){ ///ajustar esta funcion
     @session_start();
      include_once( "../lib/general/conexion_lib.inc" );
      include_once( "../lib/general/tabla_lib.inc" );
      include_once( "../lib/general/constantes.inc" );
      include_once( "../lib/mensajes_lib.inc" );
      $BASE = $_SESSION[BASE_DATOS];
      $BD_STANDA = $_SESSION[BD_STANDA];
      $this -> conexion = new Conexion( $_SESSION['HOST'], $_SESSION[USUARIO], $_SESSION[CLAVE], $BASE  );//cod_transp
      $datos_usuario = $_SESSION[USUARIO];
      $usuario=$datos_usuario["cod_usuari"];
      
      $query = "SELECT 1 
                FROM ".$BD_STANDA.".tab_mensaj_dispos 
                WHERE cod_transp='".$_REQUEST[transp]."' 
                AND nom_bdsata='".$BASE."' 
                AND dir_dispos='".$_REQUEST[numdis]."' 
                AND cod_operad='".$_REQUEST[operador]."'";
      $consulta = new Consulta($query, $this -> conexion); 
      $dispo = $consulta -> ret_matriz();
      if(count($dispo)==0){
        $query = "INSERT INTO ".$BD_STANDA.".tab_mensaj_dispos
                     (cod_transp,nom_bdsata,dir_dispos,cod_operad,ind_estado)
                  VALUES ('".$_REQUEST[transp]."','".$BASE."','".$_REQUEST[numdis]."',".$_REQUEST[operador].",'1')";
        $consulta = new Consulta($query, $this -> conexion,"BR");  
        if($consulta = new Consulta ("COMMIT", $this -> conexion)){
          $mensaje =  "Se realizo la asignacion del dispositvo para la transportadora <b>".$_REQUEST[transp]."</b> con Exito";
          $mens = new mensajes();
          $mens -> correcto("ASIGNAR DISPOSITIVO",$mensaje);
        }
      }else{
          $mensaje =  "El dispositvo ya Existe para la transportadora <b>".$_REQUEST[transp]."</b>";
          $mens = new mensajes();
          $mens -> correcto("ASIGNAR DISPOSITIVO",$mensaje);
      }
  }
  function getOperadores($tranp=''){
    $query = "SELECT a.cod_operad,CONCAT(a.nom_operad,' (@',a.dns_operad,')') AS dns
    		      FROM   ".BD_STANDA.".tab_mensaj_operad a
    		      WHERE ((a.cod_transp = '".$tranp."' AND
    		   		       a.nom_bdsata = '".BASE_DATOS."') OR
    		   		      (a.cod_transp IS NULL AND a.nom_bdsata IS NULL))
              ORDER BY 2";
       $consulta = new Consulta($query, $this -> conexion);
       $operador_a = $consulta -> ret_matriz();
       return $operador_a;
  }
  function getLista($selected=''){
     @session_start();
     include_once( "../lib/general/conexion_lib.inc" );
     include_once( "../lib/general/tabla_lib.inc" );
     include_once( "../lib/general/constantes.inc" );
     include_once( "../lib/mensajes_lib.inc" );
     $BASE = $_SESSION[BASE_DATOS];
     $BD_STANDA = $_SESSION[BD_STANDA];
     $this -> conexion = new Conexion( $_SESSION['HOST'], $_SESSION[USUARIO], $_SESSION[CLAVE], $BASE  );//cod_transp
     $query = "SELECT a.cod_operad,CONCAT(a.nom_operad,' (@',a.dns_operad,')') AS dns
    		      FROM   ".$BD_STANDA.".tab_mensaj_operad a
    		      WHERE ((a.cod_transp = '".$_REQUEST[transp]."' AND
    		   		       a.nom_bdsata = '".$BASE."') OR
    		   		      (a.cod_transp IS NULL AND a.nom_bdsata IS NULL))
              ORDER BY 2";
      $consulta = new Consulta($query, $this -> conexion);
      $operador_a = $consulta -> ret_matriz();
    
      echo "<select class='form_01' id='operadorID' name='operador' onchange='setOperador()'>";
      echo "<option value='0'>--</option>";
      echo "<option value='999'>OTRO</option>";
      foreach($operador_a as $ope):
        echo "<option value='".$ope[cod_operad]."' ".($selected==$ope[cod_operad]?"selected":'').">".$ope[dns]."</option>";
      endforeach;
      echo "</select>";
  }
  function Eliminar(){
    $novala = $_REQUEST[novala];
    $codigo = $_REQUEST[codigo];
    $listransp = $_REQUEST[listransp];
    
    $query = "SELECT * 
              FROM ".BD_STANDA.".tab_mensaj_dispos 
              WHERE cod_transp = '".$listransp[0]."' 
              AND  nom_bdsata = '".BASE_DATOS."' 
              GROUP BY 1,2,3,4";
    $consulta = new Consulta($query, $this -> conexion);
    $alardisp  = $consulta -> ret_matriz();
    $eliminar = array();
    $m=0;
    for($j = 0; $j < sizeof($alardisp); $j++){
      $control = false;
      for($i = 0; $i < $_REQUEST[cont]; $i++){
        if($novala[$i]){
          $novala_m = explode("|",$novala[$i]);
          if( strcmp($alardisp[$j][dir_dispos], $novala_m[0])==0  && (int)$alardisp[$j][cod_operad] == (int)$novala_m[1]){
            $control = true;
          }else{
            continue;
          }
        }
      }
      if(!$control){
        $eliminar[$m][dir_dispos] = $alardisp[$j][dir_dispos];
        $eliminar[$m][cod_operad] = $alardisp[$j][cod_operad];
        $m++;
      }
    }
    for($j = 0; $j < count($eliminar); $j++){
      $query = "DELETE FROM ".BD_STANDA.".tab_mensaj_alarma
                WHERE cod_transp = '".$listransp[0]."' 
                      AND nom_bdsata = '".BASE_DATOS."'
                      AND dir_dispos = '".$eliminar[$j][dir_dispos]."'
                      AND cod_operad = '".$eliminar[$j][cod_operad]."'";
      $consulta = new Consulta($query, $this -> conexion);
      $query = "DELETE FROM ".BD_STANDA.".tab_mensaj_dispos
                WHERE cod_transp = '".$listransp[0]."' 
                      AND nom_bdsata = '".BASE_DATOS."'
                      AND dir_dispos = '".$eliminar[$j][dir_dispos]."'
                      AND cod_operad = '".$eliminar[$j][cod_operad]."'";
      $consulta = new Consulta($query, $this -> conexion);
    }
  }
  
  
  function Insertar(){
    $datos_usuario = $this -> usuario -> retornar();
    $usuario = $datos_usuario["cod_usuari"];
    $novala  = $_REQUEST[novala];
    $novala2 = $_REQUEST[novala2];
    $estado  = $_REQUEST[estado]; 
    $codigo  = $_REQUEST[codigo];
    $listransp = $_REQUEST[listransp];
    
    /***** Funcion Para Eliminar Dispositivos ***********/
     $this->Eliminar($GLOBALS);
    /***************/ 
   
    for($i = 0; $i < $_REQUEST[cont]; $i++){
      if($novala[$i]){
        $novala_m = explode("|",$novala[$i]);
        $novala_x = explode("|",$novala2[$i]);
        $estado_m = explode("|",$estado[$i]);
        $query = "UPDATE ".BD_STANDA.".tab_mensaj_dispos
                  SET ind_novala = '".$novala_x[2]."',
                      ind_emailx = 1,
                      ind_estado = '".$estado_m[2]."'
                  WHERE cod_transp = '".$novala_m[2]."' AND
                        nom_bdsata = '".BASE_DATOS."' AND
                        dir_dispos = '".$novala_m[0]."' AND
                        cod_operad = ".$novala_m[1]."";

        $consulta = new Consulta($query, $this -> conexion,"R");
      }
      for($j = 0; $j < $_REQUEST[totala]; $j++){
        if($codigo[$i][$j]){
          $codigo_m = explode("|",$codigo[$i][$j]);
          $query = "SELECT 1 
                    FROM ".BD_STANDA.".tab_mensaj_alarma 
                    WHERE cod_transp = '".$codigo_m[3]."' 
                    AND nom_bdsata='".BASE_DATOS."' 
                    AND dir_dispos='".$codigo_m[0]."' 
                    AND cod_operad='".$codigo_m[1]."' 
                    AND cod_alarma='".$codigo_m[2]."'";
          $consulta = new Consulta($query, $this -> conexion);
          $dispo    = $consulta -> ret_matriz();
          if(count($dispo)==0){
            $query = "INSERT INTO ".BD_STANDA.".tab_mensaj_alarma
                          (cod_transp,nom_bdsata,dir_dispos,cod_operad,cod_alarma)
                      VALUES ('".$codigo_m[3]."','".BASE_DATOS."','".$codigo_m[0]."',".$codigo_m[1].",".$codigo_m[2].")";
            $consulta = new Consulta($query, $this -> conexion,"R");
          }
        }
      }
    }
    if($consulta = new Consulta ("COMMIT", $this -> conexion)){
      $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">Configurar Alarmas Otra Transportadora</a></b>";
      $mensaje =  "Se Actualizo la Informaci&oacute;n de las Alarmas Exitosamente. ".$link_a;
      $mens = new mensajes();
      $mens -> correcto("ALARMAS",$mensaje);
    }
  }
}//FIN CLASE PROC_CONTRO

//$proceso = new Proc_contro($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
$proceso = new Proc_contro($_SESSION['conexion'], $_SESSION['usuario_aplicacion'], $_SESSION['codigo']);
