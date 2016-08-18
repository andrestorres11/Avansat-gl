<?php
    
    session_start();
    
    ini_set('display_errors', true);
    error_reporting(E_ALL & ~E_NOTICE);

    
    class InfNovedadesUsuario{
        
        var $conexion;
        var $cNull = array(array('cod_usuari'=>0, 'nom_usuari'=>'Seleccione una opción'));
        
        function __construct($conexion){
            echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";
            $this -> conexion = $conexion;
            if(empty($_REQUEST[option]) && !isset($_REQUEST[option])){
                $_REQUEST[option] = 'showFilters';
            }
            
            $this -> $_REQUEST[option]( $_REQUEST );
        }

        function expInformExcel(){
            
            $archivo = "Informe_Novedades_por_Usuario.xls";
            header('Content-Type: application/octetstream');
            header('Expires: 0');
            header('Content-Disposition: attachment; filename="'.$archivo.'"');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            echo $_REQUEST['datosPintar'];
            
        }
        
        function showFilters()
        {
            header('charset: ISO-8859-1');
            
            $_USUARIOS =  $this->getUsuarios();
           
            $_PERFILES =  $this->getPerfiles();
            $session = (object) $_SESSION['datos_usuario'];
            $disabled = "";
            if($session->cod_perfil == '7' || $session->cod_perfil == '713'){
                $disabled = "disabled";
            }

            echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/inf_noveda2.js\"></script>\n";
            echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
            echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.blockUI.js\"></script>\n";
            echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/time.js\"></script>\n";
            echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";
            echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
            echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/multiselect/jquery.multiselect.filter.min.js\"></script>\n";
            echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/multiselect/jquery.multiselect.min.js\"></script>\n";
            echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>\n";
            echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/bootstrap.css' type='text/css'>\n";
            echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/multiselect/jquery.multiselect.css' type='text/css'>\n";
            echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/multiselect/jquery.multiselect.filter.css' type='text/css'>\n";
            echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery.table2excel.js\"></script>\n";
            

            $hoy = date("Y-m-d H:i:s");
            $inicial = strtotime('-7 day', strtotime($hoy));
            $inicial = date('Y-m-d', $inicial);
            $hinicial = date('H:00', strtotime($hoy));
            $hoy = date('Y-m-d', strtotime($hoy));
            echo "</table>";
            ?>
        <form id="formulario" style="display: none" method="post" name='formulario' target="_blank" action="../<?= DIR_APLICA_CENTRAL ?>/inform/inf_numero_alarma.php">
            <input type="hidden" name='datosPintar' id='datosPintarID'>
            <input type="hidden" name='option' id='optionID' value='expInformExcel'>
        </form>
        <div id="acordeonID" class="col-md-12 ancho">
          <h1>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>Informe de Novedades Por Usuario</b></h1>
          <div id="contenido">
            <div  class="Style2DIV">
              <table width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <th class="CellHead" colspan="6" style="text-align:center"><b>Ingrese los  Par&aacute;metros de consulta</b></th>
                </tr>
                <tr >
                  <td class="CellHead contenido" colspan="6" style="text-align:center">
                    <div class="col-md-3">Fecha Inicial<font style="color:red">*</font>: </div>
                    <div class="col-md-3"><input type="text" maxlength="10" value="<?= $inicial ?>" size="10" id="fec_iniciaID" readonly="" name="fec_inicia" ></div>
                    <div class="col-md-3">Hora Inicial<font style="color:red">*</font>: </div>
                    <div class="col-md-3"><input type="text" maxlength="10" value="<?= $hinicial ?>" size="10" id="hor_iniciaID" readonly="" name="hor_inicia" size="7" maxlenght="7" ></div>
                    <div class="col-md-12">&nbsp;</div>
                    <div class="col-md-3">Fecha Final<font style="color:red">*</font>: </div>
                    <div class="col-md-3"><input type="text" maxlength="10" value="<?= $hoy ?>" size="10" id="fec_finaliID" readonly="" name="fec_finali" ></div>
                    <div class="col-md-3">Hora Final<font style="color:red">*</font>: </div>
                    <div class="col-md-3"><input type="text" maxlength="10" value="<?= $hinicial ?>" size="10" id="hor_finaliID" readonly="" name="hor_finali" size="7" maxlenght="7" ></div>
                    <div class="col-md-12">&nbsp;</div>
                    <div class="col-md-6">
                        <div class="col-md-6">Perfiles: </div>
                        <div class="col-md-6">
                            <select id="perfilID" name="perfil"  <?= $disabled ?>  style="width:100%" >
                            <?php
                                foreach ($_PERFILES as $key => $value) {
                                    ?>
                                    <option value="<?= $value['cod_perfil'] ?>"><?= $value['nom_perfil'] ?></option>
                                    <?php
                                 } 
                            ?>
                            </select>
                        </div>
                    </div> 
                    <div class="col-md-6">
                        <div class="col-md-6">Tipo de Informe<font style="color:red">*</font>: </div>
                        <div class="col-md-6">
                            <select id="tipoID" name="tipo" style="width:100%">
                                <option value="0">Seleccione una opci&oacute;n</option>
                                <option value="1">Diario</option>
                                <option value="2">Semanal</option>
                                <option value="3">Mensual</option>
                            </select>
                        </div>
                    </div>
                  </td>
                </tr>
                <tr>
                    <td  class="CellHead contenido" colspan="6" style="text-align:center">
                        <div class="col-md-6">
                            <div class="col-md-6">Usuario: </div>
                            <div class="col-md-6">
                                <select id="usuarioID" <?= $disabled ?> name="usuario" style="width:100%">
                                <?php
                                    foreach ($_USUARIOS as $key => $value) {
                                        ?>
                                        <option value="<?= $value['cod_usuari'] ?>"><?= $value['nom_usuari'] ?></option>
                                        <?php
                                     } 
                                ?>
                                </select>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                  <td class="CellHead contenido" colspan="6" style="text-align:center">
                    <div class="col-md-12">&nbsp;</div>
                    <div id="ocultos" style="display:none" class="col-md-12">
                      <input type="hidden" name="standa" id="standaID" value="<?= DIR_APLICA_CENTRAL ?>"> 
                      <input type="hidden" name="window" id="windowID" value="central"> 
                      <input type="hidden" name="cod_servic" id="cod_servicID" value="<?= $_REQUEST['cod_servic'] ?>"> 
                    </div>
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </div>
          <div class="col-md-12 tabs ancho" id="tabs">
             <ul>
               <li><a id="liGenera" href="#generaID" style="cursor:pointer">REPORTE</a></li>
             </ul>
             <div class="col-md-12" id="generaID" ></div>
          </div>
          <div class="col-md-12" id="hidden" style="display:none"></div>
         
            <?php
        }
        
        
        function getInforme(){
            
            echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/inf_noveda2.js\"></script>\n";
            echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";
            echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
            $this -> Style();
            
            if(empty($_REQUEST[hor_corte]))
                $_REQUEST[hor_corte] = date('H:i:00');
            else
                $_REQUEST[hor_corte] .= ':00';
            
            $_USUARIOS = $this->getUsuarios( $_REQUEST );
            /*
            echo "<pre>";
            print_r($_USUARIOS);
            echo "</pre>";*/
            
            $_REQUEST[fecha_corte] = $_REQUEST[fec_corte].' '.$_REQUEST[hor_corte];
            
            
            $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "INFORME DE NOVEDADES", "formulario\" id=\"formularioID");
            $formulario -> oculto("url_archiv\" id=\"url_archivID\"","inf_numero_alarma.php",0);
            $formulario -> oculto("dir_aplica\" id=\"dir_aplicaID\"",DIR_APLICA_CENTRAL,0);
            $formulario -> oculto("based\" id=\"basedID\"",BASE_DATOS,0);
            $formulario -> oculto("num_serie",0,0);
            $formulario -> oculto("window","central",0);
            $formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],0);
            $formulario -> oculto("opcion",2,0);
            $formulario -> nueva_tabla();
            $formulario -> linea("Cantidad de Novedades por Usuario al ".$_REQUEST[fecha_corte]  ,1,"t2"); 

            $_NOVEDADES = $this -> getNovedades( $_REQUEST ); 
            
            $mHtml  = "<table width='100%'>";
            
            $mHtml .= "<tr>";
            $mHtml .= "<th class=cellHead rowspan='2'>Usuario</th>";
            foreach($_NOVEDADES AS $_FECHA_ => $_FECHA_VAL){
                $mHtml .= "<th class=cellHead colspan='".count($_FECHA_VAL)."' >{$_FECHA_}</th>";
            }
            $mHtml .= "<th class=cellHead rowspan='2'>TOTAL</th>";
            $mHtml .= "</tr>";
            $mHtml .= "<tr>";
            foreach($_NOVEDADES AS $_FECHA_VAL){
                foreach ( $_FECHA_VAL as $_HORA => $HORA_VAL){
                    $mHtml .= "<th class=cellHead >{$_HORA}</th>";
                }
            }
            $mHtml .= "</tr>";
            
            $_TOTAL_ = array();
            
           
            
            foreach($_USUARIOS AS $_USUARIO_){
                
                $_TOTAL = 0;
                $mHtmlr = '';
                
                $mHtmlr .= "<tr class='row'>";
                    $mHtmlr .= "<td class='cellInfo' >{$_USUARIO_[nom_usuari]}</td>";
                    foreach($_NOVEDADES AS $_FECHA => $_FECHA_VAL){
             
                        foreach ( $_FECHA_VAL as $_HORA => $HORA_VAL){
                            $mHtmlr .= "<td class='cellInfo' align='right' style='cursor: pointer;' ".( ((int)$HORA_VAL[ $_USUARIO_[0] ] ) > 0 ? "  onclick=\"infoNoveda2('IN', '".$_USUARIO_[0]."','".$_REQUEST[fecha_corte]."','".$_HORA."', '".$_REQUEST[ran_corte]."');\" " : null )." >".((int)$HORA_VAL[ $_USUARIO_[0] ])."</td>";
                            $_TOTAL += (int)$HORA_VAL[ $_USUARIO_[0] ];
                            $_TOTAL_[$_FECHA][$_HORA] += (int)$HORA_VAL[ $_USUARIO_[0] ];
                        }
                    }
                    $mHtmlr .= "<td class='cellInfo' align='right' style='cursor: pointer;' ".( ((int)$_TOTAL ) > 0 ? "  onclick=\"infoNoveda2('TO', '".$_USUARIO_[0]."','".$_REQUEST[fecha_corte]."','".end( array_keys( $_TOTAL_ ) )."', '".$_REQUEST[ran_corte]."');\" " : null ).">{$_TOTAL}</td>";
                $mHtmlr .= "</tr>";
                    
                    
                if($_TOTAL > 0){
                    $mHtml .= $mHtmlr;
                }
            }
            
            $_TOTAL__ = array();
            
            $mHtml .= "<tr>";
            $mHtml .= "<th class='cellHead' style='text-align: right;' >TOTAL:</th>";
            foreach ($_TOTAL_ as $_FECHAS ){
                foreach ($_FECHAS as $row ){
                    $mHtml .= "<th class='cellHead' style='text-align: right;' >{$row}</th>";
                    $_TOTAL__[] = $row;
                }
            }
            $mHtml .= "<th class='cellHead' style='text-align: right;'>".array_sum($_TOTAL__)."</th>";
            $mHtml .= "</tr>";
            
            
            $mHtml .= "</table>";
            
            echo $mHtml;
            
            $_SESSION['LIST_TOTAL']=$mHtml;
    
            $formulario -> nueva_tabla();
            $formulario -> botoni("Excel","exportarXls2()",0);
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
        
        function getDetalles( $__REQUEST ){
         
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
            $_USUARIO_ = $this -> getUsuarios( array('cod_usuari' => $__REQUEST[cod_usuar]) );
            
            
            $_horaCorte = explode(':', $__REQUEST[hora_corte]);
            $_horaCorte[1] = trim(str_replace('00 ', '', $_horaCorte[1]));
            
            if(strcmp($_horaCorte[1], 'PM') == 0){
                $_horaCorte[0] = (int)$_horaCorte[0] + 12;
            }else{
                $_horaCorte[0] = (int)$_horaCorte[0];
            }
            
            if($__REQUEST[tipo]=='IN'){
                $condition1 = " AND HOUR(b.fec_contro) = {$_horaCorte[0]} ";
                $condition2 = " AND HOUR(b.fec_noveda) = {$_horaCorte[0]} ";
                $condition3 = " AND HOUR(b.fec_repnov) = {$_horaCorte[0]} ";
            }else{
                $condition1 = '';
                $condition2 = '';
                $condition3 = '';
            }
            
         $sql = "SELECT b.usr_creaci, b.fec_contro, b.num_despac, a.nom_noveda, b.obs_contro
                      FROM ".BASE_DATOS.".tab_genera_noveda a, 
                           ".BASE_DATOS.".tab_despac_contro b
                     WHERE a.cod_noveda  = b.cod_noveda
                       AND b.fec_contro <= '{$__REQUEST[fecha_corte]}'
                       AND b.fec_contro >= DATE_SUB( '{$__REQUEST[fecha_corte]}', INTERVAL {$__REQUEST[ran_corte]} HOUR)
                       {$condition1}
                    UNION ALL
                    SELECT b.usr_creaci, b.fec_noveda, b.num_despac, a.nom_noveda, b.des_noveda as obs_contro
                      FROM ".BASE_DATOS.".tab_genera_noveda a, 
                           ".BASE_DATOS.".tab_despac_noveda b
                     WHERE a.cod_noveda  = b.cod_noveda
                       AND b.fec_noveda >= DATE_SUB( '{$__REQUEST[fecha_corte]}', INTERVAL {$__REQUEST[ran_corte]} HOUR)
                       AND b.fec_noveda <= '{$__REQUEST[fecha_corte]}'
                       {$condition2}
                       ";
            
            
            $sql = "SELECT a.*, d.abr_tercer
                      FROM ( ".$sql." ) AS a, 
                           ".BASE_DATOS.".tab_genera_usuari b,
                           ".BASE_DATOS.".tab_despac_vehige c,
                           ".BASE_DATOS.".tab_tercer_tercer d
                     WHERE LOWER(a.usr_creaci) = LOWER(b.cod_usuari)
                       AND b.ind_estado = 1 
                       AND c.num_despac = a.num_despac
                       AND c.cod_transp = d.cod_tercer
                       AND LOWER(a.usr_creaci) = '".$__REQUEST[cod_usuar]."'
                       AND ( b.cod_perfil IN(1,7,8,73,70,77,669,713) )
                     UNION ALL
                    SELECT b.usr_creaci, b.fec_repnov, b.num_repnov, a.nom_noveda, b.obs_repnov as obs_contro, d.abr_tercer
                      FROM ".BASE_DATOS.".tab_genera_noveda a, 
                           ".BASE_DATOS.".tab_report_noveda b,
                           ".BASE_DATOS.".tab_genera_usuari c,
                           ".BASE_DATOS.".tab_tercer_tercer d
                     WHERE a.cod_noveda  = b.cod_noveda
                       AND b.fec_repnov >= DATE_SUB( '{$__REQUEST[fecha_corte]}', INTERVAL {$__REQUEST[ran_corte]} HOUR)
                       AND b.fec_repnov <= '{$__REQUEST[fecha_corte]}'
                       {$condition3}
                       AND LOWER(b.usr_creaci) = LOWER(c.cod_usuari)
                       AND c.ind_estado = 1 
                       AND b.cod_tercer = d.cod_tercer
                       AND LOWER(b.usr_creaci) = '".$__REQUEST[cod_usuar]."'
                       AND ( c.cod_perfil IN(1,7,8,73,70,77,669,713) )
                    ORDER BY 2 DESC,4,1
                   ";
           
       
            
            $consulta  = new Consulta($sql, $this -> conexion);
            $despachos = $consulta -> ret_matriz();

                
            $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "INFORMACION DE NOVEDADES", "formulario");
            $formulario -> nueva_tabla(); 
            $formulario -> botoni("Excel","exportarXls()",0);//Jorge 2703-2012 
            $formulario -> botoni("Cerrar","ClosePopup()",1);//validarCumplidos()
            $formulario -> oculto("dir_aplica\" id=\"dir_aplicaID\"",DIR_APLICA_CENTRAL,0);//Jorge 2703-2012
            $formulario -> oculto("based\" id=\"basedID\"",BASE_DATOS,0);//Jorge 2703-2012
            $formulario -> cerrar();
            $formulario -> nueva_tabla();
            $formulario -> linea("Total de Novedades: ".sizeof($despachos)." - ".$_USUARIO_[0][1],0,"t2","15%");
            
            $mHtml  = "<table width='100%'>";
            $mHtml .= "<tr>";
                $mHtml .= "<th class=cellHead >Transportadora</th>";
                $mHtml .= "<th class=cellHead >Despacho</th>";
                $mHtml .= "<th class=cellHead >Novedad</th>";
                $mHtml .= "<th class=cellHead >Fecha</th>";
                $mHtml .= "<th class=cellHead >Observacion</th>";
            $mHtml .= "</tr>";
            
            foreach($despachos AS $row){
                 $mHtml .= "<tr>";
                    $mHtml .= "<td class='cellInfo' >{$row[5]}</td>";
                    $mHtml .= "<td class='cellInfo' >{$row[2]}</td>";
                    $mHtml .= "<td class='cellInfo' >{$row[3]}</td>";
                    $mHtml .= "<td class='cellInfo' >{$row[1]}</td>";
                    $mHtml .= "<td class='cellInfo' >{$row[4]}</td>";
                 $mHtml .= "</tr>";
            }
            
            $mHtml .= "</table>";
            
            echo $mHtml;
            
            $_SESSION['LIST_TOTAL']=$mHtml;
            
            $formulario -> nueva_tabla(); 
            $formulario -> botoni("Excel","exportarXls()",0);
            $formulario -> botoni("Cerrar","ClosePopup()",1);
            $formulario -> cerrar();
        }
        function getNovedades( $__REQUEST ){
            
            $sql = "SELECT b.usr_creaci, b.fec_contro AS fec_noveda 
                      FROM ".BASE_DATOS.".tab_genera_noveda a, 
                           ".BASE_DATOS.".tab_despac_contro b
                     WHERE a.cod_noveda  = b.cod_noveda
                       AND b.fec_contro <= '{$__REQUEST[fecha_corte]}'
                       AND b.fec_contro >= DATE_SUB( '{$__REQUEST[fecha_corte]}', INTERVAL {$__REQUEST[ran_corte]} HOUR)
                    UNION ALL
                    SELECT b.usr_creaci, b.fec_noveda
                      FROM ".BASE_DATOS.".tab_genera_noveda a, 
                           ".BASE_DATOS.".tab_despac_noveda b
                     WHERE a.cod_noveda  = b.cod_noveda
                       AND b.fec_noveda >= DATE_SUB( '{$__REQUEST[fecha_corte]}', INTERVAL {$__REQUEST[ran_corte]} HOUR)
                       AND b.fec_noveda <= '{$__REQUEST[fecha_corte]}' 
                    UNION ALL
                    SELECT b.usr_creaci, b.fec_repnov AS fec_noveda 
                      FROM ".BASE_DATOS.".tab_genera_noveda a,
                           ".BASE_DATOS.".tab_report_noveda b
                     WHERE a.cod_noveda  = b.cod_noveda
                       AND b.fec_repnov >= DATE_SUB( '{$__REQUEST[fecha_corte]}', INTERVAL {$__REQUEST[ran_corte]} HOUR)
                       AND b.fec_repnov <= '{$__REQUEST[fecha_corte]}' ";
            
            
            $sql = "SELECT COUNT(*), 
                           TRIM(LOWER(a.usr_creaci)) AS usr_creaci,
                           HOUR(a.fec_noveda), 
                           CONCAT( SUBSTRING( DATE_FORMAT(a.fec_noveda, '%r'), 1, 2), ':00', ' ', SUBSTRING( DATE_FORMAT(a.fec_noveda, '%r'), -2 )  ), 
                           DATE(a.fec_noveda)
                      FROM ( ".$sql." ) AS a, 
                           ".BASE_DATOS.".tab_genera_usuari b
                     WHERE LOWER(a.usr_creaci) = LOWER(b.cod_usuari)
                       AND b.ind_estado = 1 
                       AND ( b.cod_perfil IN(1,7,8,73,70,77,669,713) OR cod_usuari LIKE '%eal%' OR nom_usuari LIKE '%eal%' OR cod_usuari LIKE '%ecl%' OR nom_usuari LIKE '%ecl%' )
                   GROUP BY 2, 3, 5
                   ORDER BY 5, 3";
           
            $consul = new Consulta($sql, $this -> conexion);
            $result = $consul -> ret_matriz();
           
            $MATRIZ = array();
            
            foreach ( $result  as $row){
                $MATRIZ[ $row[4] ][ $row[3] ][ $row[1] ] = $row[0]; 
            }      
            
            return $MATRIZ;
            
        }
        function getUsuarios( $__REQUEST = null  ){
            $session = (object) $_SESSION['datos_usuario'];
            $and = "";
            if($session->cod_perfil == '7' || $session->cod_perfil == '713'){
                $and = "AND cod_usuari = '$session->cod_usuari'";
            }
            $sql =" SELECT LOWER(cod_usuari) cod_usuari, CONCAT( UPPER(nom_usuari ), ' - ', LOWER(cod_usuari) ) AS nom_usuari
                      FROM ".BASE_DATOS.".tab_genera_usuari
                     WHERE ind_estado = 1 
                       AND ( cod_perfil IN(1,7,8,73,70,77,669,713) OR cod_usuari LIKE '%eal%' OR nom_usuari LIKE '%eal%' OR cod_usuari LIKE '%ecl%' OR nom_usuari LIKE '%ecl%'  ) $and 
                       ".(empty($__REQUEST[cod_usuari]) ? null : " AND cod_usuari =  '".$__REQUEST[cod_usuari]."' ")."
                    ORDER BY 2";
            $consulta = new Consulta($sql, $this -> conexion);
            return $consulta->ret_matrix("a");
        } 
        function getPerfiles( $__REQUEST = null  ){
            $session = (object) $_SESSION['datos_usuario'];
            if($session->cod_perfil == '7' || $session->cod_perfil == '713'){
                $where = "WHERE cod_perfil = '$session->cod_perfil' ";
            }else{
                $where = "WHERE cod_perfil IN(7,8,73,70,77,669,713)";
            }
            $sql =" SELECT cod_perfil, LOWER(nom_perfil) AS nom_perfil
                      FROM ".BASE_DATOS.".tab_genera_perfil
                     $where
                    ORDER BY 2";
            $consulta = new Consulta($sql, $this -> conexion);
            return $consulta->ret_matrix("a");
        }
        
        
    }
    $service = new InfNovedadesUsuario($_SESSION['conexion']);


?> 
