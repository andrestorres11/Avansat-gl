<?php 
/*****************************************************************/
/* CLASE PARA EL INFORME DE PROYECCION DE DESPACHOS POR USUARIO  */
/* @AUTHOR: FELIPE MALAVER - felipe.malaver@intrared.net         */
/* @DATE: 23-08-2013                                             */
/*****************************************************************/
session_start();
ini_set( 'memory_limit', '1024M' );

class Inf_Proyec_Despac
{
  var $conexion;
  var $cNull = array( array('', '- Todos -') ); 
  var $cDays = array( array('', '- Todos -'), array('1', 'Domingo'), array('2', 'Lunes'), array('3', 'Martes'), 
                      array('4', 'Miércoles'), array('5', 'Jueves'), array('6', 'Viernes'), array('7', 'Sábado'),  ); 

  function __construct($conexion)
  {
    $this -> conexion = $conexion;
    $this -> Principal( $_REQUEST );
  }
    
  function Principal( $mData )
  {
    if( !isset( $mData['option'] ) )
    {
      $this -> SetFilters( $mData );
    }
    else
    {
      switch( $mData['option'] )
      {
        case 'result';
          $this -> SetResult( $mData );
        break;
        
        case 'details';
          $this -> SetDetails();
        break;

        case 'xls';
          $this -> expInformExcel();
        break;

        default:
        $this -> SetFilters( $mData );
        break;
      }
    }
  }
  
  function expInformExcel()
  {       
    $archivo = "Informe_Proyeccion_Despachos_".date( "Y_m_d" ).".xls";
    header('Content-Type: application/octetstream');
    header('Expires: 0');
    header('Content-Disposition: attachment; filename="'.$archivo.'"');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    echo $_SESSION['LIST_TOTAL'];
  }
  
  function SetDetails()
  {
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
    
    $_USUARIO = $this -> getUsers( $_REQUEST );
    
    $condition1 = " AND HOUR(b.fec_contro) = ".$_REQUEST['hora']." ";
    $condition2 = " AND HOUR(b.fec_noveda) = ".$_REQUEST['hora']." ";
    $condition3 = " AND HOUR(b.fec_repnov) = ".$_REQUEST['hora']." ";
      
    $sql = "SELECT b.usr_creaci, b.fec_contro, b.num_despac, a.nom_noveda, b.obs_contro
              FROM ".BASE_DATOS.".tab_genera_noveda a, 
                   ".BASE_DATOS.".tab_despac_contro b
             WHERE a.cod_noveda  = b.cod_noveda
               AND b.fec_contro BETWEEN '".$_REQUEST['fecha']." 00:00:00' AND '".$_REQUEST['fecha']." 23:59:59'
               ".$condition1."
            UNION ALL
            SELECT b.usr_creaci, b.fec_noveda, b.num_despac, a.nom_noveda, b.des_noveda as obs_contro
              FROM ".BASE_DATOS.".tab_genera_noveda a, 
                   ".BASE_DATOS.".tab_despac_noveda b
             WHERE a.cod_noveda  = b.cod_noveda
               AND b.fec_noveda BETWEEN '".$_REQUEST['fecha']." 00:00:00' AND '".$_REQUEST['fecha']." 23:59:59'
               ".$condition2." ";

    $sql = "SELECT a.*, d.abr_tercer
              FROM ( ".$sql." ) AS a, 
                   ".BASE_DATOS.".tab_genera_usuari b,
                   ".BASE_DATOS.".tab_despac_vehige c,
                   ".BASE_DATOS.".tab_tercer_tercer d
             WHERE LOWER(a.usr_creaci) = LOWER(b.cod_usuari)
               AND b.ind_estado = 1 
               AND c.num_despac = a.num_despac
               AND c.cod_transp = d.cod_tercer
               AND LOWER(a.usr_creaci) = '".$_REQUEST['cod_usuari']."'
               AND ( b.cod_perfil IN(1,7,8,73,70,77,669,713) )
             UNION ALL
            SELECT b.usr_creaci, b.fec_repnov, b.num_repnov, a.nom_noveda, b.obs_repnov as obs_contro, d.abr_tercer
              FROM ".BASE_DATOS.".tab_genera_noveda a, 
                   ".BASE_DATOS.".tab_report_noveda b,
                   ".BASE_DATOS.".tab_genera_usuari c,
                   ".BASE_DATOS.".tab_tercer_tercer d
             WHERE a.cod_noveda  = b.cod_noveda
               AND b.fec_repnov BETWEEN '".$_REQUEST['fecha']." 00:00:00' AND '".$_REQUEST['fecha']." 23:59:59'
               ".$condition3."
               AND LOWER(b.usr_creaci) = LOWER(c.cod_usuari)
               AND c.ind_estado = 1 
               AND b.cod_tercer = d.cod_tercer
               AND LOWER(b.usr_creaci) = '".$_REQUEST['cod_usuari']."'
               AND ( c.cod_perfil IN(1,7,8,73,70,77,669,713) )
            ORDER BY 2 DESC,4,1
           ";

    $consulta  = new Consulta($sql, $this -> conexion);
    $_DESPAC = $consulta -> ret_matriz();
    
    $mHtml = '';
    $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "INFORMACION DE NOVEDADES", "form");
    $formulario -> nueva_tabla(); 
    $formulario -> botoni("Excel","exportarXls()",0);
    $formulario -> oculto("dir_aplica\" id=\"dir_aplicaID\"",DIR_APLICA_CENTRAL,0);
    $formulario -> oculto("based\" id=\"basedID\"",BASE_DATOS,0);
    $formulario -> cerrar();
    $formulario -> nueva_tabla();
    $ind = $this -> GetDayOfWeek( $_REQUEST['fecha'] );
    $day_of_week = $this -> cDays[(int)$ind][1];
    $_REQUEST['hora'] = strlen( $_REQUEST['hora'] ) == '1' ? "0".$_REQUEST['hora'] : $_REQUEST['hora'];
    $formulario -> linea("Total de Novedades: ".sizeof($_DESPAC)." <br>Usuario: ".$_USUARIO[0][1]." <br>Día: ".$day_of_week.", ".$this -> getDate( $_REQUEST['fecha'] )." Desde las: ".$_REQUEST['hora'].":00 Hasta ".$_REQUEST['hora'].":59",0,"t2","15%");
    
    $this -> Style();
            
    $mHtml  .= "<table width='100%'>";
    $mHtml .= "<tr>";
        $mHtml .= "<th class=cellHead >Transportadora</th>";
        $mHtml .= "<th class=cellHead >Despacho</th>";
        $mHtml .= "<th class=cellHead >Novedad</th>";
        $mHtml .= "<th class=cellHead >Fecha</th>";
        $mHtml .= "<th class=cellHead >Observaci&oacute;n</th>";
    $mHtml .= "</tr>";
            
    foreach($_DESPAC AS $row){
         $mHtml .= "<tr class='row'>";
            $mHtml .= "<td class='cellInfo' >".$row[5]."</td>";
            $mHtml .= "<td class='cellInfo' >".$row[2]."</td>";
            $mHtml .= "<td class='cellInfo' >".$row[3]."</td>";
            $mHtml .= "<td class='cellInfo' >".$row[1]."</td>";
            $mHtml .= "<td class='cellInfo' >".$row[4]."</td>";
         $mHtml .= "</tr>";
    }
            
    $mHtml .= "</table>";
    
    echo $mHtml;
    
    $_SESSION['LIST_TOTAL'] = $mHtml;
    
    $formulario -> nueva_tabla(); 
    $formulario -> botoni("Excel","exportarXls()",1);
    $formulario -> cerrar();
  }
  
  function SetFilters( $mData )
  {
    /************************************* INCLUSION DE JAVASCRIPT, JQUERY Y ESTILOS ********************************************/
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/inf_proyec_despac.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/es.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/time.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";
    echo '<script>
            jQuery(function($) { 
              $( "#fec_iniciaID,#fec_finaliID" ).datepicker();

              $.mask.definitions["A"]="[12]";
              $.mask.definitions["M"]="[01]";
              $.mask.definitions["D"]="[0123]";
              $.mask.definitions["n"]="[0123456789]";
              
              $( "#fec_iniciaID, #fec_finaliID" ).mask("Annn-Mn-Dn");

            });
          </script>';
    echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";
    /******************************************************************************************************************************/  
    $mData['fec_inicia'] = $mData['fec_inicia'] ? $mData['fec_inicia'] : date('Y-m-d') ;  
    $mData['fec_finali'] = $mData['fec_finali'] ? $mData['fec_finali'] : date('Y-m-d') ;  
    $_USUARIOS = array_merge( $this->cNull, $this->getUsers() );
    $_DIAS = $this->cDays ;
    
    $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "Informe de Proyección de Despachos por Usuario", "form\" id=\"formID");
    $formulario -> nueva_tabla();
    $formulario -> linea( "-Filtros", 1, "t2" );
    $formulario -> nueva_tabla();
    $formulario -> texto ("* Fecha Inicial:","text","fec_inicia\" id=\"fec_iniciaID",0,7,7,"", $mData['fec_inicia'] );
    $formulario -> texto ("* Fecha Final:","text","fec_finali\" id=\"fec_finaliID",1,7,7,"", $mData['fec_finali'] );
    $formulario -> lista ("Usuario:","cod_usuari\" id=\"cod_usuariID",$_USUARIOS,0 );
    $formulario -> lista ("Día:","cod_diaxxx\" id=\"cod_diaxxxID",$_DIAS,1 );
    $formulario -> nueva_tabla();
    $formulario -> botoni("Buscar","Validate()",0);
    $formulario -> nueva_tabla();
    echo "<BR><BR>";
    echo "<BR><BR>";
    echo "<BR><BR>";
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],0);
    $formulario -> oculto("option\" id=\"optionID",'',0);
    $formulario -> cerrar();
  
  }
  
  function getUsers( $mData = null  ){
    $sql =" SELECT LOWER(cod_usuari), CONCAT( UPPER(nom_usuari ), ' - ', LOWER(cod_usuari) ) AS nom_usuari, cod_usuari
              FROM ".BASE_DATOS.".tab_genera_usuari
             WHERE ind_estado = 1 
               AND ( cod_perfil IN(1,7,8,73,70,77,669,713) OR cod_usuari LIKE '%eal%' OR nom_usuari LIKE '%eal%' OR cod_usuari LIKE '%ecl%' OR nom_usuari LIKE '%ecl%'  )
               ".(empty($mData['cod_usuari']) ? null : " AND cod_usuari =  '".$mData['cod_usuari']."' ")."
            ORDER BY 2";
    $consulta = new Consulta($sql, $this -> conexion);
    return $consulta -> ret_matriz(); 
  }
  

  //--------------------
  function isEAL( $cod_usuari = '' )
  {
    $SQL =  " SELECT  b.cod_usuari, a.nom_perfil
                FROM  ".BASE_DATOS.".tab_genera_perfil a INNER JOIN
                      ".BASE_DATOS.".tab_genera_usuari b ON a.cod_perfil = b.cod_perfil
               WHERE  b.cod_usuari = '" . $cod_usuari . "' AND
                      (
                        a.nom_perfil LIKE '%eal%' OR 
                        b.cod_usuari LIKE '%eal%' OR 
                        b.nom_usuari LIKE '%eal%'
                      )
            ";

    $consul = new Consulta( $SQL, $this -> conexion );
    $consul =  $consul -> ret_matriz(); 

    if( count( $consul ) > 0 )
      return TRUE;
    else
      return FALSE;
  }
  //--------------------



  //--------------------
  function SetResult( $mData )
  {
    $_TOTVE = array();
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/inf_proyec_despac.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/es.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/time.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";
    
    
    $this -> Style();
    $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "INFORME DE PROYECCI&Oacute;N DE DESPACHOS ", "form\" id=\"formID");
    $formulario -> oculto("dir_aplica\" id=\"dir_aplicaID\"",DIR_APLICA_CENTRAL,0);
    $formulario -> oculto("url_archiv\" id=\"url_archivID\"","inf_proyec_usuari.php",0);
    $formulario -> oculto("based\" id=\"basedID\"",BASE_DATOS,0);
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],0);
    $formulario -> oculto("option\" id=\"optionID",'',0);
    $formulario -> nueva_tabla();

    $_CICLOX = 25;
    
    $_USUARI = $this -> getUsers( $mData );
    $_NOVEDA = $this -> GetTotalNovedades( $mData );

    //--------------------
    $connov = count( $_NOVEDA );
    if( $connov > 1 ) $sep_day = TRUE;
    else $sep_day = FALSE;
    //--------------------
    
    $con_noveda = 0;
    $mHtml = ''; 
    foreach ( $_NOVEDA as $key => $_DAY )
    {
      $con_noveda++;
      if( $mData['cod_diaxxx'] != NULL )
      {
        if( $mData['cod_diaxxx'] != $this -> GetDayOfWeek( $key ) )
          continue;
      }
      
      $ind = $this -> GetDayOfWeek( $key );
      $day_of_week = $this -> cDays[(int)$ind][1];

      //--------------------
      $colspa = count( $_DAY );
      //--------------------
      $seccio = 'CONTROL TRAFICO';
      //--------------------
      $mHtml .= $this -> Encabezado( $seccio, $_DAY, $day_of_week, $key, $colspa );
      //--------------------

      $hor_actual = "0";
      
      //--------------------
      $row_ealxxx = '';
      $total_E = 0;
      $total_T = 0;
      //--------------------

      foreach($_USUARI AS $_USUARIO_)
      {
        //----------
        $_TOTAL = 0;
        $mHtmlr = '';
        $isEAL = $this -> isEAL( $_USUARIO_['cod_usuari'] );
        //----------

        //----------
        $mHtmlr .= "<tr class='row'>";
        $mHtmlr .= "<td class='cellInfo' >".$_USUARIO_[nom_usuari]."</td>";
        foreach ( $_DAY as $_HORA => $HORA_VAL)
        {
          $mHtmlr .= "<td class='cellInfo' align='right' style='cursor: pointer;' ".( ((int)$HORA_VAL[ $_USUARIO_[0] ] ) > 0 ? "  onclick=\"Details('".$_HORA."', '".$_USUARIO_[0]."','".$key."');\" " : null )." >".((int)$HORA_VAL[ $_USUARIO_[0] ])."</td>";
          $_TOTAL += (int)$HORA_VAL[ $_USUARIO_[0] ];
          $_TOTAL_[$_FECHA][$_HORA] += (int)$HORA_VAL[ $_USUARIO_[0] ];
          $_TOTVE[ $key ][ $_HORA ] += (int)$HORA_VAL[ $_USUARIO_[0] ];

          if( $isEAL )
          {
            $total_E += (int)$HORA_VAL[ $_USUARIO_[0] ];
            $horasE[ $_HORA ] += $HORA_VAL[ $_USUARIO_[0] ];
          }
          else
          {
            $total_T += (int)$HORA_VAL[ $_USUARIO_[0] ];
            $horasT[ $_HORA ] += $HORA_VAL[ $_USUARIO_[0] ];
          }
        }
        $mHtmlr .= "<td class='cellInfo' align='right'>".$_TOTAL."</td>";
        $mHtmlr .= "</tr>";
        //----------
        
        if($_TOTAL > 0)
        {
          if( $isEAL )
            $row_ealxxx .= $mHtmlr;
          else
            $mHtml .= $mHtmlr;
        }
      }
      //--------------------
     
      //--------------------
      $rtHORAE = '';
      $rtHORAT = '';

      foreach( $horasE as $itemx )
        $rtHORAE .= "<td class='footer'>" . $itemx . "</td>";

      foreach( $horasT as $itemx )
        $rtHORAT .= "<td class='footer'>" . $itemx . "</td>";
      //--------------------

      //--------------------
      $mHtml .= "<tr>";
      $mHtml .= "<td class='footer' >TOTAL</td>";
      $mHtml .= $rtHORAT;
      $mHtml .= "<td class='footer'>" . $total_T . "</td>";
      $mHtml .= "</tr>";
      //--------------------

      //--------------------
      $stylef = 'color:#FFFFFF; height:20px;';
      $mHtml .= '<tr><td colspan="' . $colspa .'" style="' . $stylef . '">-</td></tr>';
      //--------------------
      
      //--------------------
      $seccio = 'EAL';
      $mHtml .= $this -> Encabezado( $seccio, $_DAY, $day_of_week, $key, $colspa );
      $mHtml .= $row_ealxxx;
      //--------------------
      $mHtml .= "<tr>";
      $mHtml .= "<td class='footer' >TOTAL</td>";
      $mHtml .= $rtHORAE;
      $mHtml .= "<td class='footer'>".$total_E."</td>";
      $mHtml .= "</tr>";
      //--------------------
      $mHtml .= '<tr><td colspan="' . $colspa .'" style="' . $stylef . '">-</td></tr>';
      //--------------------

      //--------------------
      if( $sep_day && $con_noveda < $connov )
        $mHtml .= '<tr><td colspan="' . $colspa .'" style="' . $stylef . ' background-color:#AAAAAA;"></td></tr>';
      //--------------------
    }
    
    $mHtml .= "</table>";
    
    if( $ind )
      echo $mHtml; 
    else
    {
      $formulario -> nueva_tabla();
      $formulario -> linea( "NO SE ENCONTRARON RESULTADOS", 1, "t2" );
    }  

    $formulario -> nueva_tabla();
    $formulario -> botoni("Volver","history.go(-1)",0);
    
    /*------------------------------------------Div Para la apertura del POPUP------------------------------------------*/
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
    echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";
    echo ' <script>
            $(function() {
              $( "#popupDIV" ).dialog({
                resizable: false,
                height: 510,
                width: $(document).width() - 900,
                autoOpen: false,
                show: {
                  effect: "fold",
                  duration: 600
                },
                hide: {
                  effect: "fold",
                  duration: 600,
                }
              });
            });
            </script>';
    echo '<div id="popupDIV" style=" left: 0px; top: 0px; z-index: 3; overflow: auto; border: 5px solid #333333;  "></div>';
            
    /*-------------------------------------------------------------------------------------------------------------------*/ 
  }
  //--------------------


  //--------------------
  function Encabezado( $seccio = '', $_DAY = NULL, $day_of_week = '', $key = '', $colspa = 1 )
  {
    //--------------------
    $encab =  "
                <table width='150%' >
                <tr>
                <th class='cellHead2' colspan='27' align='left'>" . $day_of_week . ", " . $this -> getDate( $key ) . " - " . $seccio . "</th>
                </tr>
                <tr>
                <th class='cellHead' rowspan='2'>USUARIO</th>
                <th class='cellHead' colspan='". $colspa ."'>RANGOS (" . $colspa . " HORAS)</th>
                <th class='cellHead' rowspan='2'>TOTAL</th>
                </tr>
              ";
    //--------------------
    $encab .= "<tr>";
    foreach( $_DAY AS $_DAY_ => $_DAY_VAL )
    {
      if( strlen( $_DAY_ ) == '1' )
        $_DAY_ = '0'.$_DAY_; 

      $encab .= "<th class='cellHead'>". $_DAY_ .":00 a ".$_DAY_.":59</th>";
    }
    $encab .= "</tr>";
    //--------------------

    return $encab;
  }
  //--------------------

  //--------------------
  function GetTotalNovedades( $mData = NULL )
  {

    $mQuery = "SELECT b.num_despac AS num_despac, b.usr_creaci, b.fec_contro AS fec_noveda,  DATE_FORMAT(b.fec_contro, '%k') AS hor_noveda,  a.nom_noveda, b.obs_contro AS obs_contro
                      FROM ".BASE_DATOS.".tab_genera_noveda a, 
                           ".BASE_DATOS.".tab_despac_contro b
                     WHERE a.cod_noveda  = b.cod_noveda
                       AND b.fec_contro  BETWEEN  '".$mData['fec_inicia']." 00:00:00' AND '".$mData['fec_finali']." 23:59:59'
                       
                    UNION ALL
                    SELECT b.num_despac AS num_despac, b.usr_creaci, b.fec_noveda AS fec_noveda, DATE_FORMAT(b.fec_noveda, '%k') AS hor_noveda, a.nom_noveda, b.des_noveda AS obs_contro
                      FROM ".BASE_DATOS.".tab_genera_noveda a, 
                           ".BASE_DATOS.".tab_despac_noveda b
                     WHERE a.cod_noveda  = b.cod_noveda
                       AND b.fec_noveda  BETWEEN  '".$mData['fec_inicia']." 00:00:00' AND '".$mData['fec_finali']." 23:59:59'
                    UNION ALL
                    SELECT b.num_repnov AS num_despac, b.usr_creaci, b.fec_repnov AS fec_noveda, DATE_FORMAT(b.fec_repnov, '%k') AS hor_noveda, a.nom_noveda, b.obs_repnov AS obs_contro
                      FROM ".BASE_DATOS.".tab_genera_noveda a,
                           ".BASE_DATOS.".tab_report_noveda b
                     WHERE a.cod_noveda  = b.cod_noveda 
                       AND b.fec_repnov  BETWEEN  '".$mData['fec_inicia']." 00:00:00' AND '".$mData['fec_finali']." 23:59:59' ";

    
    $mSql = "SELECT COUNT(*), 
             TRIM(LOWER(a.usr_creaci)) AS usr_creaci,
             HOUR(a.fec_noveda), 
             CONCAT( SUBSTRING( DATE_FORMAT(a.fec_noveda, '%r'), 1, 2), ':00', ' ', SUBSTRING( DATE_FORMAT(a.fec_noveda, '%r'), -2 )  ), 
             DATE(a.fec_noveda)
        FROM ( ".$mQuery." ) AS a, 
             ".BASE_DATOS.".tab_genera_usuari b
       WHERE LOWER(a.usr_creaci) = LOWER(b.cod_usuari)
         AND b.ind_estado = 1
         AND ( b.cod_perfil IN(1,7,8,73,70,77,669,713) OR cod_usuari LIKE '%eal%' OR nom_usuari LIKE '%eal%' OR cod_usuari LIKE '%ecl%' OR nom_usuari LIKE '%ecl%' )";
    
    if( $mData['cod_usuari'] && $mData['cod_usuari'] != '')
    {
      $mSql .= " AND LOWER(b.cod_usuari) = '".$mData['cod_usuari']."' ";
    }     
     
    $mSql .= "GROUP BY 2, 3, 5
              ORDER BY 5, 3";
    $_NOVEDA = array();
    $consulta = new Consulta($mSql, $this -> conexion);
    $result = $consulta -> ret_matriz();

    $MATRIZ = array();
    
    foreach( $result as $row )
    {
      $MATRIZ[ $row[4] ][ $row[2] ][ $row[1] ] = $row[0]; 
    }
    return $MATRIZ;
  }
  //--------------------
 
  function GetDayOfWeek( $date )
  {
    $mSql = "SELECT DAYOFWEEK( '".$date."' ) AS DATE ";
    $consulta = new Consulta($mSql, $this -> conexion);
    $DAY = $consulta -> ret_matriz(); 
    return $DAY[0][0];
  }
  
  function getDate( $date )
  {
    $MONTH = array(1=>'Enero', 2=>'Febrero', 3=>'Marzo', 4=>'Abril', 5=>'Mayo', 6=>'Junio', 
                   7=>'Julio', 8=>'Agosto', 9=>'Septiembre', 10=>'Octubre', 11=>'Noviembre', 12=>'Diciembre');               
    $DATE = explode('-',$date);
    return $DATE[2]." de ".$MONTH[(int)$DATE[1]]." de ".$DATE[0];
  }
  
  function Style(){
		echo "	<style>
            .cellHead
            {
              padding:5px 10px;
              background: -webkit-gradient(linear, left top, left bottom, from( #009617 ), to( #00661b )); 
              background: -moz-linear-gradient(top, #009617, #00661b ); 
              filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#009617', endColorstr='#00661b');
              color:#fff;
              text-align:center;
            }

            .cellHead2
            {
              padding:5px 10px;
              background: -webkit-gradient(linear, left top, left bottom, from( #009617 ), to( #00661b )); 
              background: -moz-linear-gradient(top, #009617, #00661b ); 
              filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#009617', endColorstr='#00661b');
              color:#fff;
              text-align:left;
            }
            
            .footer
            {
              padding:5px 10px;
              background: -webkit-gradient(linear, left top, left bottom, from( #009617 ), to( #00661b )); 
              background: -moz-linear-gradient(top, #009617, #00661b ); 
              background-image: -ms-linear-gradient(top, #00660f 0%,#00660f 100%); 
              background-image: linear-gradient(to bottom, #00660f 0%,#00660f 100%); 
              filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#03ad39', endColorstr='#00660f',GradientType=0 );
              color:#fff;
              text-align:right;
            }

            .row:hover > td{
              background-color: #9ad9ae;
            }
            .cellInfo
            {
              padding:5px 10px;
              background-color:#fff;
              border:1px solid #ccc;
            }

            .cellInfo2
            {
              padding:5px 10px;
              background-color:#9ad9ae;
              border:1px solid #ccc;
            }



            .label
            {
              font-size:12px;
              font-weight:bold;
            }

            .select
            {
              background-color:#fff;
              border:1px solid #009617;
            }

            .boton
            {
              background: -webkit-gradient(linear, left top, left bottom, from( #009617 ), to( #00661b )); 
              background: -moz-linear-gradient(top, #009617, #00661b ); 
              filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#009617', endColorstr='#00661b');
              color:#fff;
              border:1px solid #fff;
              padding:3px 15px;
              -webkit-border-radius: 5px;
              -moz-border-radius: 5px;
              border-radius: 5px;
            }

            .boton:hover
            {
              background:#fff;
              color:#00661b;
              border:1px solid #00661b;
              cursor:pointer;
            }
    </style>";
	}

}
$proceso = new Inf_Proyec_Despac($_SESSION['conexion']);
  
?>