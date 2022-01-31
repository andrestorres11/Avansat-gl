<?php

class InformBandejEntrad { 
  var $conexion      = NULL;
  var $genera_alarma = NULL;
  var $cod_perfil    = NULL;
  var $cod_usuari    = NULL;
  var $superusuario  = NULL;
  var $tmp_transp    = NULL;
  var $cod_serlis    = 3302;
  var $tot_despac    = array();
  
  
  function InformBandejEntrad( $conexion, $mData ) { 
    $this -> conexion = $conexion;
    $this -> genera_alarma = $this -> GetGeneraAlarma();
    //-------------------------------------------------
    $datos_usuario = $_SESSION[datos_usuario];
    $this -> cod_perfil = $datos_usuario['cod_perfil'];
    $this -> cod_usuari = $datos_usuario['cod_usuari'];
    //----------------------------------------------------
    $this -> superusuario = $this -> VerifySuperUsuario();
    //----------------------------------------------------
    echo $this -> FirstLoad( $mData );
    /*
    if ( $mData['ajax'] == 1 ) {
      echo $this -> FirstLoad( $mData );
    }
    else { 
      echo $this -> FirstLoad( $mData );
    }
    */
  }
  
  
  function FirstLoad( $mData ) {
    $html  = NULL;
    $html .= '<tr>';
      $html .= '<td align="center" width="100%">';
        $html .= '<script type="text/javascript" src="../'.DIR_APLICA_CENTRAL.'/js/min.js"></script>';
        $html .= '<script type="text/javascript" src="../'.DIR_APLICA_CENTRAL.'/js/inf_bandej_entrad.js"></script>';
        $html .= '<script type="text/javascript" src="../'.DIR_APLICA_CENTRAL.'/js/jquery.blockUI.js"></script>';
        $html .= '<form action="index.php?" method="post" name="frm_report" id="frm_reportID">';
          $html .= '<div id="informDIV">';
            $html .= $this -> DrawReport( $mData );
          $html .= '</div>';
          $html .= '<input type="hidden" name="dir_centra" id="dir_centraID" value="'.DIR_APLICA_CENTRAL.'" />';
          $html .= '<input type="hidden" name="cod_servic" id="cod_servicID" value="'.$_REQUEST['cod_servic'].'" />';
          $html .= '<input type="hidden" name="window" id="windowID" value="central" />';
        $html .= '</form>';
        $html .= '<script language="javascript">ReloadReport();</script>';
      $html .= '</td>';
    $html .= '</tr>';
    return $html;
  }
  
  
  function DrawStyle() {
    $html  = NULL;
    $html .= '<style>';
    $html .= '.classTable{ font-family:Arial; font-size:11px; color:#444444; background:#eeeeee; }';
    $html .= '.classHead{ border-right: 1px solid #ffffff; border-bottom: 1px solid #ffffff; padding:5px 15px 5px 15px; color:#333333; }';
    $html .= '.classCell{ border-right: 1px solid #ffffff; border-bottom: 1px solid #ffffff; padding:2px 5px 2px 5px; color:#444444; }';
    $html .= '.classList{ background:#eeeeee; font-family:Arial; font-size:11px; color:#444444; font-weight:bold; padding:0px 5px 0px 5px; }';
    for ( $h = 0, $size_alarma = count( $this -> genera_alarma ); $h < $size_alarma; $h++ ) { 
      $html .= '.bg'.$h.'{ background:#'.$this -> genera_alarma[$h]['cod_colorx'].'; }';
      $html .= '.co'.$h.'{ color:#'.$this -> genera_alarma[$h]['cod_colorx'].'; font-weight:bold; }';
    }
    $html .= '.bgEAL{ background:#7FFF99; }';
    $html .= '.cp{ cursor:pointer; }';
    $html .= '.classLink{ background:#eeeeee; font-family:Arial; font-size:11px; color:#006600; font-weight:bold; }';
    $html .= '.classMenu{ border-left: 1px solid #ffffff; border-right: 1px solid #ffffff; background:#009900; cursor:pointer; width:7px; }';
    $html .= '</style>';
    return $html;
  }
  
  
  function set_link( $val_link, $data = NULL ) { 
    if ( $val_link == '-' )
      return '-';
    $a  = '<a class="classLink" href="index.php?';
    $a .= 'cod_servic='.$this -> cod_serlis;
    $a .= '&window=central';
    $a .= '&atras=si';
    $a .= '&transp='.$this -> tmp_transp;
    $a .= $data;
    $a .= '"';
    $a .= ' target="centralFrame">';
    $a .= $val_link;
    $a .= '</a>';
    return $a;
  }
  
  
  function DrawReport( $mData ) { 
    //------------------------------------
    $_TIPSER = $this -> GetGeneraTipser();
    $size_tipser = sizeof( $_TIPSER );
    //------------------------------------
    $_REPORT = $this -> GetReport( $mData );
    $size_report = count( $_REPORT );
    //--------------------------------------
    $html  = NULL;
    $html .= $this -> DrawStyle();
    $html .= '<table class="classTable" align="center" width="100%" cellspacing="0" cellpadding="0">';
    
    $html .= '<tr>';
      $rowspan = $size_report + 3;
      
      $html .= '<th nowrap class="classMenu" align="center" rowspan="'.$rowspan.'" onclick="Hide();">&nbsp;</th>';
    
      $html .= '<th nowrap class="classHead" align="center">NO.</th>';

      $html .= '<th nowrap class="classHead" align="center">';
        $html .= '<select class="classList" name="cod_tipser" id="cod_tipserID">';
          $html .= '<option value="">SERVICIO</option>';
          for ( $s = 0; $s < $size_tipser; $s++ ) {
            $selected = $mData['cod_tipser'] == $_TIPSER[$s]['cod_tipser'] ? 'selected="selected"' : NULL;
            $html .= '<option value="'.$_TIPSER[$s]['cod_tipser'].'" '.$selected.'>'.$_TIPSER[$s]['nom_tipser'].'</option>';
          }
        $html .= '</select>';
      $html .= '</th>';

      $html .= '<th nowrap class="classHead" align="center">EMPRESA</th>';
      $html .= '<th nowrap class="classHead" align="center">NO. DESPACHOS</th>';
      $html .= '<th nowrap class="classHead" align="center">SIN RETRASO</th>';
      for ( $h = 0, $size_alarma = count( $this -> genera_alarma ); $h < $size_alarma; $h++ ) { 
        //------------------------------------
        $_GENALA = $this -> genera_alarma[$h];
        //------------------------------------
        $nom_alarma[$h] = $_GENALA['nom_alarma'].' - '.$_GENALA['can_tiempo'];
        $html .= '<th nowrap class="classHead bg'.$h.'" align="center">'.$nom_alarma[$h].'</th>';
      }
      if ( $this -> superusuario ) { 
        $html .= '<th nowrap class="classHead bgEAL" align="center">EAL</th>';
      }
      $html .= '<th nowrap class="classHead" align="center">FUERA DE PLATAFORMA</th>';
      $html .= '<th nowrap class="classHead" align="center">POR LLEGADA</th>';
      $html .= '<th nowrap class="classHead" align="center">A CARGO EMPRESA</th>';
      if ( $this -> superusuario ) {
        //----------------------------------------
        $_ASIGNA = $this -> GetListSuperUsuario();
        $size_asigna = sizeof( $_ASIGNA );
        //----------------------------------------
        $html .= '<th nowrap class="classHead" align="center">';
          $html .= '<select class="classList" name="usr_asigna" id="usr_asignaID">';
            $html .= '<option value="">USUARIO ASIGNADO</option>';
            for ( $a = 0; $a < $size_asigna; $a++ ) {
              $selected = $mData['usr_asigna'] == $_ASIGNA[$a][0] ? 'selected="selected"' : NULL;
              $html .= '<option value="'.$_ASIGNA[$a][0].'" '.$selected.'>'.$_ASIGNA[$a][0].'</option>';
            }
          $html .= '</select>';
        $html .= '</th>';
      }
    $html .= '</tr>';
    //----------------------------
    $html .= '<totals>|</totals>';
    //--------------------------------------
    if ( $size_report == 0 ) {
      $colspan = $this -> superusuario ? 14 : 12;
      $html .= '<tr>';
        $html .= '<th nowrap class="classCell" align="center" colspan="'.$colspan.'">NO SE ENCONTRARON DESPACHOS</th>';
      $html .= '</tr>';
    }
    else { 
      $i = 1;
      foreach ( $_REPORT as $row ) { 
        
        /*
        if ( $row['cod_tipser'] <> 1 ) {
          continue;
        }
        */
        
        $nom_tipser = $row['nom_tipser'];
        $cod_transp = $row['cod_transp'];
        $nom_transp = $row['nom_transp'];
        $can_despac = $row['can_despac'];
        $can_sinret = $row['can_sinret'] ? $row['can_sinret'] : '-';
        $can_penlle = $row['can_penlle'] ? $row['can_penlle'] : '-';
        $can_alaEAL = $row['can_alaEAL'] ? $row['can_alaEAL'] : '-';
        $can_fuepla = $row['can_fuepla'] ? $row['can_fuepla'] : '-';
        $can_defini = $row['can_defini'] ? $row['can_defini'] : '-';
        $usr_asigna = $row['usr_asigna'];
        
        $this -> tot_despac['can_despac'] += $row['can_despac'];
        $this -> tot_despac['can_sinret'] += $row['can_sinret'];
        $this -> tot_despac['can_penlle'] += $row['can_penlle'];
        
        $this -> tot_despac['can_fuepla'] += $row['can_fuepla'];
        $this -> tot_despac['can_defini'] += $row['can_defini'];
        //--------------------------------
        $this -> tmp_transp = $cod_transp;
        //--------------------------------
        
        $can_despac = $this -> set_link( $can_despac, '&totregif='.$can_despac );
        $can_sinret = $this -> set_link( $can_sinret, '&totregif='.$can_sinret.'&defini=0&alacla=S' );
        $can_alaEAL = $this -> set_link( $can_alaEAL, '&EAL=1' );
        $can_fuepla = $this -> set_link( $can_fuepla, '&FUEPLA=1' );
        $can_penlle = $this -> set_link( $can_penlle, '&totregif='.$can_penlle.'&alacla=L' );
        $can_defini = $this -> set_link( $can_defini, '&defini=1' );


        $line  = NULL;
        $line .= '<tr onclick="this.style.background=this.style.background==\'#ceecf5\'?\'#eeeeee\':\'#ceecf5\';">';
          $line .= '<th nowrap class="classCell" align="left">'.$i.'</th>';
          $line .= '<td nowrap class="classCell" align="left">'.$nom_tipser.'</td>';
          $line .= '<td nowrap class="classCell" align="left" title="EMPRESAS">'.$nom_transp.'</td>';
          $line .= '<td nowrap class="classCell" align="center" title="NO. DESPACHOS">'.$can_despac.'</td>';
          $line .= '<td nowrap class="classCell" align="center" title="SIN RETRASO">'.$can_sinret.'</td>';
          for ( $h = 0, $size_alarma = count( $this -> genera_alarma ); $h < $size_alarma; $h++ ) { 
            //------------------------------------
            $_GENALA = $this -> genera_alarma[$h];
            //------------------------------------
            $can_tiempo = $row['can_'.$_GENALA['can_tiempo'].'mins'] ? $row['can_'.$_GENALA['can_tiempo'].'mins'] : '-';
            $can_tiempo = $this -> set_link( $can_tiempo, '&totregif='.$can_tiempo.'&defini=0&alacla='.( $h + 1 ) );
            
            $line .= '<td nowrap class="classCell" align="center" title="'.$nom_alarma[$h].'">'.$can_tiempo.'</td>';
            
            $this -> tot_despac['can_alarma'][$h] += $row['can_'.$_GENALA['can_tiempo'].'mins'];
          }
          if ( $this -> superusuario ) { 
            $line .= '<td nowrap class="classCell" align="center" title="EAL">'.$can_alaEAL.'</td>';
            $this -> tot_despac['can_alaEAL'] += $row['can_alaEAL'];
          }
          $line .= '<td nowrap class="classCell" align="center" title="FUERA DE PLATAFORMA">'.$can_fuepla.'</td>';
          $line .= '<td nowrap class="classCell" align="center" title="PENDIENTES POR LLEGAR">'.$can_penlle.'</td>';
          $line .= '<td nowrap class="classCell" align="center" title="A CARGO EMPRESA">'.$can_defini.'</td>';
          if ( $this -> superusuario ) {
            $line .= '<td nowrap class="classCell" align="left" title="USUARIO ASIGNADO">'.$usr_asigna.'</td>';
          }
        $line .= '</tr>';
        //-------------
        $html .= $line;
        //-------------
        $i ++;
      }  
    }
    $html .= '<totals>|</totals>';
    
    $html .= '</table>';
    
    $tota  = NULL;
    $tota .= '<tr>';
      $tota .= '<th nowrap class="classHead" align="right" colspan="3">TOTALES:</th>';
      $tota .= '<th nowrap class="classHead" align="center">'.$this -> tot_despac['can_despac'].'</th>';
      $tota .= '<th nowrap class="classHead" align="center">'.$this -> tot_despac['can_sinret'].'</th>';
      foreach( $this -> tot_despac['can_alarma'] as $tot_tiempo ) {
        $tota .= '<th nowrap class="classHead" align="center">'.$tot_tiempo.'</th>';
      }
      $tota .= '<th nowrap class="classHead" align="center">'.$this -> tot_despac['can_alaEAL'].'</th>';
      $tota .= '<th nowrap class="classHead" align="center">'.$this -> tot_despac['can_fuepla'].'</th>';
      $tota .= '<th nowrap class="classHead" align="center">'.$this -> tot_despac['can_penlle'].'</th>';
      $tota .= '<th nowrap class="classHead" align="center">'.$this -> tot_despac['can_defini'].'</th>';
      $tota .= '<th nowrap class="classHead" align="center">&nbsp;</th>';
    $tota .= '</tr>';
    
    if ( $size_report == 0 ) {
      $tota = NULL;
    }
    //---------------------------------------------------------
    $html  = str_replace( '<totals>|</totals>', $tota, $html );
    //---------------------------------------------------------
    return $html;
  }
  
  
  function sumarMinutosFecha( $FechaStr, $MinASumar ) {
    $FechaStr = str_replace( "-", " ", $FechaStr );
    $FechaStr = str_replace( ":", " ", $FechaStr );
    $FechaOrigen = explode( " ", $FechaStr );
    $Dia = $FechaOrigen[2];
    $Mes = $FechaOrigen[1];
    $Ano = $FechaOrigen[0];
    $Horas = $FechaOrigen[3];
    $Minutos = $FechaOrigen[4];
    $Segundos = $FechaOrigen[5];
    // Sumo los minutos
    $Minutos = ( (int)$Minutos) + ((int)$MinASumar ); 
    // Asigno la fecha modificada a una nueva variable
    $FechaNueva = date( "Y-m-d H:i:s", mktime( $Horas, $Minutos, $Segundos, $Mes, $Dia, $Ano ) );
    return $FechaNueva;
  } 
  
  
  function diferenciaMinutosFecha( $fecha_inicio, $fecha_fin ) {
    $time_inicio = strtotime( $fecha_inicio );
    $time_fin = strtotime( $fecha_fin );
    if ( $time_inicio > $time_fin ) {
      return (int)abs( round( ( $time_inicio - $time_fin ) / 60 ) );
    }
    else {
      return (int)abs( round( ( $time_fin - $time_inicio ) / 60 ) );
    }
  }
  
  
  function GetGeneraAlarma() {
    $mSql  = "SELECT a.cod_alarma, 
                     UPPER( a.nom_alarma ) AS nom_alarma, 
                     a.cant_tiempo AS can_tiempo, 
                     a.cod_colorx 
                FROM ".BASE_DATOS.".tab_genera_alarma a ORDER BY 1 ";
    $consul = new Consulta( $mSql, $this -> conexion );
    $matriz = $consul -> ret_matriz( 'a' );
    return $matriz;
  }
  
  
  function GetDespacUsuariAsigna( $cod_usuari = NULL ) {
    $mSql  = "SELECT b.cod_tercer
                    FROM ".BASE_DATOS.".tab_monito_encabe a,
                         ".BASE_DATOS.".tab_monito_detall b
                   WHERE a.ind_estado = '1' 
                     AND b.ind_estado = '1' 
                     AND a.cod_consec = b.cod_consec 
                     AND a.fec_inicia <= NOW() 
                     AND a.fec_finalx >= NOW() ";
    $mSql .= $cod_usuari == NULL ? NULL : " AND a.cod_usuari = '".$cod_usuari."' ";
    $consul = new Consulta( $mSql, $this -> conexion );
    $matriz = $consul -> ret_matriz( 'a' );
    $in  = NULL;
    for ( $i = 0; $i < sizeof( $matriz ); $i++ ) {
      $in .= $in == NULL ? $matriz[$i]['cod_tercer'] : ", " . $matriz[$i]['cod_tercer'];
    }
    return $in;
  }

  
  function GetDespacDespac( $mData ) { 
    //AND b.cod_transp IN ( 805016737, 800015838, 805027046, 890104487 ) 
    
    if ( $this -> superusuario ) { 
      if ( $mData['usr_asigna'] == 'SIN ASIGNAR' ) {
        $notin = $this -> GetDespacUsuariAsigna( NULL );
      }
      elseif ( $mData['usr_asigna'] ) {
        $in = $this -> GetDespacUsuariAsigna( $mData['usr_asigna'] );
      }
    }
    else { 
      $in = $this -> GetDespacUsuariAsigna( $this -> cod_usuari );
    }
    
    $mSql  = "SELECT a.num_despac, 
                     b.cod_transp, 
                     UPPER( TRIM( c.abr_tercer ) ) AS nom_transp, 
                     a.fec_salida, 
                     a.fec_llegad, 
                     a.fec_ultnov, 
                     a.cod_tipdes, 
                     a.fec_manala, 
                     a.ind_defini 
                FROM ".BASE_DATOS.".tab_despac_despac a, 
                     ".BASE_DATOS.".tab_despac_vehige b, 
                     ".BASE_DATOS.".tab_tercer_tercer c 
               WHERE a.num_despac = b.num_despac 
                 AND b.cod_transp = c.cod_tercer 
                 AND a.fec_salida IS NOT NULL 
                 AND a.fec_salida <= NOW() 
                 AND a.fec_llegad IS NULL 
                 AND a.ind_planru = 'S' 
                 AND a.ind_anulad = 'R' ";
    $mSql .= $in == NULL ? NULL : " AND b.cod_transp IN ( " . $in . " ) ";
    $mSql .= $notin == NULL ? NULL : " AND b.cod_transp NOT IN ( " . $notin . " ) ";
    $mSql .= " ORDER BY 3 ";
    //echo '<hr />' . $mSql;
    $consul = new Consulta( $mSql, $this -> conexion );
    $matriz = $consul -> ret_matriz( 'a' );
    return $matriz;
  }
  
  
  function GetGeneraTipser() {
    $mSql  = "SELECT a.cod_tipser, 
                     UPPER( TRIM( a.nom_tipser ) ) AS nom_tipser 
                FROM ".BASE_DATOS.".tab_genera_tipser a ORDER BY 2 ";
    $consul = new Consulta( $mSql, $this -> conexion );
    $matriz = $consul -> ret_matriz( 'a' );
    return $matriz;
  }
  
  
  function GetTranspTipser( $cod_transp ) {
    $mSql  = "SELECT e1.num_consec, 
                     e1.cod_transp, 
                     e1.tie_contro, 
                     e1.tie_conurb, 
                     e1.cod_tipser, 
                     e2.nom_tipser 
                FROM ".BASE_DATOS.".tab_transp_tipser e1, 
                     ".BASE_DATOS.".tab_genera_tipser e2, 
                     ( SELECT MAX( num_consec ) AS max_consec 
                         FROM ".BASE_DATOS.".tab_transp_tipser 
                        WHERE cod_transp = '".$cod_transp."' ) e3 
               WHERE e1.cod_tipser = e2.cod_tipser 
                 AND e1.cod_transp = '".$cod_transp."' 
                 AND e1.num_consec = e3.max_consec ";
    $consul = new Consulta( $mSql, $this -> conexion );
    $matriz = $consul -> ret_matriz( 'a' );
    return $matriz[0];
  }
  
  
  function GetDespacPendidLlegad( $num_despac ) {
    $mSql  = "SELECT a.cod_contro 
                FROM ".BASE_DATOS.".tab_despac_seguim a 
               WHERE a.num_despac = '".$num_despac."' ORDER BY a.fec_planea DESC LIMIT 1 ";
    $consul = new Consulta( $mSql, $this -> conexion );
    $matriz = $consul -> ret_matriz( 'a' );
    $cod_contro = $matriz[0]['cod_contro'];
    
    $mSql  = "SELECT 1  
                FROM ".BASE_DATOS.".tab_despac_noveda a 
               WHERE a.num_despac = '".$num_despac."' 
                 AND a.cod_contro IN ( '".$cod_contro."' ) ";
    $consul = new Consulta( $mSql, $this -> conexion );
    $matriz = $consul -> ret_matriz( 'a' );
    return count( $matriz ) == 0 ? FALSE : TRUE;
  }
  
  
  function GetDespacUltimaNoveda( $num_despac ) {
    $mSql  = "SELECT a.fec_creaci, 
                     a.cod_noveda, 
                     a.tiem_duraci AS tie_noveda, 
                     a.fec_noveda, 
                     a.cod_contro 
                FROM ".BASE_DATOS.".tab_despac_noveda a 
               WHERE a.num_despac = '".$num_despac."' ORDER BY fec_creaci DESC LIMIT 1";
    $consul = new Consulta( $mSql, $this -> conexion );
    $matriz = $consul -> ret_matriz( 'a' );
    return $matriz[0];
  }
  
  
  function GetDespacUltimaNovedaContro( $num_despac ) {
    $mSql  = "SELECT a.fec_creaci, 
                     a.cod_noveda, 
                     a.tiem_duraci AS tie_noveda, 
                     a.fec_noveda 
                FROM ".BASE_DATOS.".tab_despac_noveda a 
               WHERE a.num_despac = '".$num_despac."'
               UNION 
              SELECT a.fec_creaci, 
                     a.cod_noveda, 
                     a.tiem_duraci AS tie_noveda, 
                     a.fec_contro AS fec_noveda 
                FROM ".BASE_DATOS.".tab_despac_contro a 
               WHERE a.num_despac = '".$num_despac."' ";
    $mSql  = "SELECT * FROM ( " . $mSql . " ) AS sub ORDER BY fec_creaci DESC LIMIT 1";
    $consul = new Consulta( $mSql, $this -> conexion );
    $matriz = $consul -> ret_matriz( 'a' );
    return $matriz[0];
  }
  
  
  function GetDespacUltimoSitio( $num_despac ) {
    $mSql  = "SELECT a.fec_creaci, 
                     a.cod_noveda, 
                     a.tiem_duraci AS tie_noveda, 
                     a.fec_noveda 
                FROM ".BASE_DATOS.".tab_despac_noveda a 
               WHERE a.num_despac = '".$num_despac."' ORDER BY fec_creaci DESC LIMIT 1";
    $consul = new Consulta( $mSql, $this -> conexion );
    $matriz = $consul -> ret_matriz( 'a' );
    return $matriz[0];
  }
  
  
  function GetDespacNoveda( $cod_noveda ) {
    $mSql  = "SELECT a.ind_manala, 
                     a.ind_tiempo, 
                     a.nov_especi, 
                     a.ind_fuepla 
                FROM ".BASE_DATOS.".tab_genera_noveda a 
               WHERE a.cod_noveda = '".$cod_noveda."' ";
    $consul = new Consulta( $mSql, $this -> conexion );
    $matriz = $consul -> ret_matriz( 'a' );
    return $matriz[0];
  }
  
  
  function GetDespacUltimoPuestoEAL( $num_despac, $cod_contro ) {
    $mSql  = "SELECT a.fec_planea, 
                     a.fec_alarma, 
                     a.ind_estado 
                FROM ".BASE_DATOS.".tab_despac_seguim a 
               WHERE a.num_despac = '".$num_despac."' 
                 AND a.cod_contro = '".$cod_contro."' 
            ORDER BY a.fec_planea ASC ";
    $consul = new Consulta( $mSql, $this -> conexion );
    $matriz = $consul -> ret_matriz( 'a' );
    $_ACTUAL = $matriz[0];
    //-------------------------------------------
    $i = $_ACTUAL['ind_estado'] == 1 ? "1" : "0";
    //-------------------------------------------
    $mSql  = "SELECT a.fec_planea, 
                     a.fec_alarma
                FROM ".BASE_DATOS.".tab_despac_seguim a 
                WHERE a.num_despac = '".$num_despac."' 
                  AND a.ind_estado = '1'
             ORDER BY a.fec_planea ASC LIMIT ".$i.", 1 ";
    $consul = new Consulta( $mSql, $this -> conexion );
    $matriz = $consul -> ret_matriz( 'a' );
    $_SIGUIE = $matriz[0];
    return array( $_ACTUAL, $_SIGUIE );
  }
  
  
  function GetDespacPuestoHabili( $num_despac ) {
    $mSql  = "SELECT a.fec_planea, 
                     a.fec_alarma
                FROM ".BASE_DATOS.".tab_despac_seguim a 
               WHERE a.num_despac = '".$num_despac."' 
                 AND a.ind_estado = '1'
            ORDER BY a.fec_planea ASC ";
    $consul = new Consulta( $mSql, $this -> conexion );
    $matriz = $consul -> ret_matriz( 'a' );
    return $matriz;
  }
  
  
  function GetDespacAlarmaEAL( $num_despac ) {
    $mSql  = "SELECT a.num_despac, a.cod_contro
                FROM ".BASE_DATOS.".tab_despac_seguim a, ".BASE_DATOS.".tab_genera_contro b
               WHERE a.cod_contro = b.cod_contro
                 AND b.ind_virtua = 0
                 AND a.ind_estado = 0
                 AND a.num_despac = '".$num_despac."' 
                 AND ( a.num_despac, a.cod_contro ) NOT IN ( SELECT num_despac, cod_contro
                                                               FROM ".BASE_DATOS.".tab_despac_noveda ) ";
    $consul = new Consulta( $mSql, $this -> conexion );
    $matriz = $consul -> ret_matriz( 'a' );
    return sizeof( $matriz ) == 0 ? FALSE : TRUE;
  }
  
  
  function VerifySuperUsuario() {
    $_SUPER[] = COD_PERFIL_ADMINIST;
    $_SUPER[] = COD_PERFIL_SUPEGENE;
    $_SUPER[] = COD_PERFIL_SUPEFARO;
    $_SUPER[] = COD_PERFIL_SUPERUSR;
    $_SUPER[] = COD_PERFIL_AUDITORX;
    return in_array( $this -> cod_perfil, $_SUPER ) ? TRUE : FALSE;
  }
  
  
  function GetSuperUsuario( $cod_transp ) { 
    $mSql  = "SELECT a.cod_usuari
                FROM ".BASE_DATOS.".tab_monito_encabe a,
                     ".BASE_DATOS.".tab_monito_detall b,
                     ".BASE_DATOS.".tab_genera_usuari c
               WHERE a.ind_estado = '1' 
                 AND b.ind_estado = '1' 
                 AND a.cod_consec = b.cod_consec 
                 AND a.fec_inicia <= NOW() 
                 AND a.fec_finalx >= NOW() 
                 AND a.cod_usuari = c.cod_usuari 
                 AND c.cod_perfil NOT IN ( ".CONS_PERFIL." ) 
                 AND b.cod_tercer = '".$cod_transp."' GROUP BY 1 ";
    $consult = new Consulta( $mSql, $this -> conexion );
    $_USUARI = $consult -> ret_matriz( 'a' );
    $size = sizeof( $_USUARI );
    $str = NULL;
    for ( $i = 0; $i < $size; $i++ ) {
      $str .= $str == NULL ? $_USUARI[$i]['cod_usuari'] : ', ' . $_USUARI[$i]['cod_usuari'];
    }
    return $str == NULL ? 'SIN ASIGNAR' : $str;
  }
  
  
  function GetListSuperUsuario() { 
    $mSql  = "SELECT a.cod_usuari  
                FROM ".BASE_DATOS.".tab_monito_encabe a,
                     ".BASE_DATOS.".tab_monito_detall b,
                     ".BASE_DATOS.".tab_genera_usuari c
               WHERE a.ind_estado = '1' 
                 AND b.ind_estado = '1' 
                 AND a.cod_consec = b.cod_consec 
                 AND a.fec_inicia <= NOW() 
                 AND a.fec_finalx >= NOW() 
                 AND a.cod_usuari = c.cod_usuari 
                 AND c.cod_perfil NOT IN ( ".CONS_PERFIL." )  GROUP BY 1 ORDER BY 1 ";
    $consult = new Consulta( $mSql, $this -> conexion );
    $_USUARI = $consult -> ret_matriz( 'i' );
    return array_merge( array( array( 'SIN ASIGNAR' ) ), $_USUARI );
  }
  
  
  function GetReport( $mData ) {
    
    $aux_despac = '290012';
    //----------------------------------
    $fec_actual = date( 'Y-m-d H:i:s' );
    //----------------------------------
    $_REPORT = $this -> GetDespacDespac( $mData );
    $size_report = sizeof( $_REPORT );
    $m = NULL;
    for ( $i = 0; $i < $size_report; $i++ ) {
      //----------------------
      $_DESPAC = $_REPORT[$i];
      //----------------------------
      $key = $_DESPAC['cod_transp'];
      //----------------------------
      $m[$key]['num_despac']  =  $_DESPAC['num_despac'];
      $m[$key]['cod_transp']  =  $_DESPAC['cod_transp'];
      $m[$key]['nom_transp']  =  $_DESPAC['nom_transp'];
      if ( $m[$key]['cod_tipser'] == NULL ) {
        //------------------------------------------------------------
        $_TIPSER = $this -> GetTranspTipser( $_DESPAC['cod_transp'] );
        //------------------------------------------------------------
        $m[$key]['cod_tipser']  =  $_TIPSER['cod_tipser'];
        $m[$key]['nom_tipser']  =  $_TIPSER['nom_tipser'];
        $m[$key]['tie_contro']  =  $_TIPSER['tie_contro'];
        $m[$key]['tie_conurb']  =  $_TIPSER['tie_conurb']; 
      }
      if ( $mData['cod_tipser'] && $mData['cod_tipser'] <> $m[$key]['cod_tipser'] ) { 
        unset( $m[$key] );
        continue;
      }
      //-----------------------------------------------------------
      //@C O L U M N A   C A N T I D A D   D E   D E S P A C H O S
      //-----------------------------------------------------------
      $m[$key]['can_despac'] ++;
      //------------------------------------------------------------------------
      //@Despacho pendiente por llegar?
      $PENDID_LLEGAD = $this -> GetDespacPendidLlegad( $_DESPAC['num_despac'] );
      //-----------------------------------------------
      //@Cantidad de despachos pendientes por llegar
      //-----------------------------------------------
      $m[$key]['can_penlle'] += $PENDID_LLEGAD ? 1 : 0;
      //------------------------------------------------------------------------
      //@Datos de última novedad / control
      $_ULTNOVCON = $this -> GetDespacUltimaNovedaContro( $_DESPAC['num_despac'] );
      //---------------------------------------------------------------------------
      //@Datos de última novedad 
      $_ULTNOV = $this -> GetDespacUltimaNoveda( $_DESPAC['num_despac'] );
      //---------------------------------------------------------------
      //@Novedades del Despacho
      $_NOVEDA = $this -> GetDespacNoveda( $_ULTNOVCON['cod_noveda'] );
      //---------------------------------------------------------------
      if ( $this -> superusuario && $m[$key]['usr_asigna'] == NULL ) {
        $m[$key]['usr_asigna'] = $this -> GetSuperUsuario( $_DESPAC['cod_transp'] );
      }
      if ( $this -> superusuario && ( $m[$key]['cod_tipser'] == '1' || $m[$key]['cod_tipser'] == '3' ) ) { 
        $_EAL = $this -> GetDespacAlarmaEAL( $_DESPAC['num_despac'] );
        $m[$key]['can_alaEAL'] += $_EAL ? 1 : 0;
      }
      $m[$key]['can_fuepla'] += $_NOVEDA['ind_fuepla'] == '1' ? 1 : 0;
      $m[$key]['can_defini'] += $_DESPAC['ind_defini'] == '1' ? 1 : 0;
      //-----------------------------------
      //@SERVICIO SOLO EAL - cod_tipser = 1
      //-----------------------------------
      if ( $m[$key]['cod_tipser'] == '1' ) {
        //-----------------------------------------------------------------
        $_ULTSIT = $this -> GetDespacUltimoSitio( $_DESPAC['num_despac'] );
        //---------------------------------------------------------------------------------------------
        $_PUESTO = $this -> GetDespacUltimoPuestoEAL( $_DESPAC['num_despac'], $_ULTNOV['cod_contro'] );
        //---------------------------------------------------------------------------------------------
        $_ACTPUE = $_PUESTO[0];
        $_SIGPUE = $_PUESTO[1];
        //------------------------
        //@SI HAY PUESTO SIGUIENTE
        //------------------------
        if ( $_SIGPUE['fec_planea'] ) {
          if ( $_ULTSIT['fec_noveda'] ) {
            $fec_ultsit = $_ULTSIT['fec_noveda'];
            //----------------------------------------------------------------------------------------------
            $dif_minuto = $this -> diferenciaMinutosFecha( $_SIGPUE['fec_planea'], $_ACTPUE['fec_planea'] );
            //----------------------------------------------------------------------------------------------
            $fec_progra = $this -> sumarMinutosFecha( $fec_ultsit, $dif_minuto );
            //------------------------------------------------------------------------------
          }
          else {
            $_HABILI = $this -> GetDespacPuestoHabili( $_DESPAC['num_despac'] );
            $fec_progra = $_HABILI[0]['fec_planea'];
            //$fec_progra = $_DESPAC['fec_salida'];
          }
          //------------------------------
          //@SI LA NOVEDAD SOLICITA TIEMPO
          //------------------------------
          if ( (int)$_ULTNOVCON['tie_noveda'] > 0 ) {
            $fec_progra = $this -> sumarMinutosFecha( $_ULTNOVCON['fec_noveda'], $_ULTNOVCON['tie_noveda'] );
          }
          
          /*
          if ( $_DESPAC['num_despac'] == $aux_despac ) {
            echo '<hr />num_despac: ' . $_DESPAC['num_despac'];
            echo '<br />fec_progra: ' . $fec_progra;
            echo '<br />fec_ultsit: ' . $fec_ultsit;
            echo '<br />dif_minuto: ' . $dif_minuto;
            echo '<br />ind_tiempo: ' . $_NOVEDA['ind_tiempo'];
            echo '<br />tie_noveda: ' . $_ULTNOVCON['tie_noveda'];
            echo '<br />fec_sigpue: ' . $_SIGPUE['fec_planea'];
            echo '<br />fec_actpue: ' . $_ACTPUE['fec_planea'];
            echo '<br />fec_manala: ' . $_DESPAC['fec_manala'];
          }
          */
          //------------------
          //@SI NO HAY RETRASO
          //------------------
          if ( $fec_actual < $fec_progra && $_DESPAC['fec_manala'] == NULL && !$PENDID_LLEGAD ) {
            $m[$key]['can_sinret'] ++;
          }
          else { 
            //----------------------------------------------------------------------------------
            $dif_minuto = $this -> diferenciaMinutosFecha( $fec_actual, $fec_progra );
            //----------------------------------------------------------------------------------
            for ( $h = 0, $size_alarma = count( $this -> genera_alarma ); $h < $size_alarma; $h++ ) { 
              //------------------------------------------------------------
              $ant_minuto = (int)$this -> genera_alarma[$h-1]['can_tiempo'];
              $act_minuto = (int)$this -> genera_alarma[$h-0]['can_tiempo'];
              $sig_minuto = (int)$this -> genera_alarma[$h+1]['can_tiempo'];
              //------------------------------------------------------------
              if ( $sig_minuto ) { 
                if ( $dif_minuto > $ant_minuto && $dif_minuto <= $act_minuto ) {
                  $m[$key]['can_'.$act_minuto.'mins'] ++;
                }
              }
              else { 
                if ( $dif_minuto > $ant_minuto ) {
                  $m[$key]['can_'.$act_minuto.'mins'] ++;
                }
              }
            }
          }
        }
        else {
          if ( !$PENDID_LLEGAD ) { 
            $m[$key]['can_sinret'] ++;
          }
        } 
        /*
        if ( $this -> superusuario ) { 
          $_EAL = $this -> GetDespacAlarmaEAL( $_DESPAC['num_despac'] );
          $m[$key]['can_alaEAL'] += $_EAL ? 1 : 0;
        }
        
        $m[$key]['can_fuepla'] += $_NOVEDA['ind_fuepla'] == '1' ? 1 : 0;
        $m[$key]['can_defini'] += $_DESPAC['ind_defini'] == '1' ? 1 : 0;
         */
      } //FIN EAL
      
      //----------------------------------------------------------------
      //@MONITOREO ACTIVO O COMBINADO
      //----------------------------------------------------------------
      else {
        
        if ( $_ULTNOVCON['fec_noveda'] ) {
          $fec_progra = $_ULTNOVCON['fec_noveda'];
        }
        else {
          $fec_progra = $_DESPAC['fec_salida'];
        }
        $tie_tipser = $_DESPAC['cod_tipdes'] == 1 ? $_TIPSER['tie_conurb'] : $_TIPSER['tie_contro'];
        //-------------------------------------------------------------------
        $fec_progra = $this -> sumarMinutosFecha( $fec_progra, $tie_tipser );
        //-------------------------------------------------------------------
        if ( (int)$_ULTNOVCON['tie_noveda'] > 0 ) {
          $fec_progra = $this -> sumarMinutosFecha( $_ULTNOVCON['fec_noveda'], $_ULTNOVCON['tie_noveda'] );
        }
        if ( $fec_actual < $fec_progra && $_DESPAC['fec_manala'] == NULL && !$PENDID_LLEGAD ) { 
          $m[$key]['can_sinret'] ++;
        }
        else { 
          if ( !$PENDID_LLEGAD ) {
            //------------------------------------------------------------------------
            $dif_minuto = $this -> diferenciaMinutosFecha( $fec_progra, $fec_actual );
            //------------------------------------------------------------------------
            for ( $h = 0, $size_alarma = count( $this -> genera_alarma ); $h < $size_alarma; $h++ ) { 
              //------------------------------------------------------------
              $ant_minuto = (int)$this -> genera_alarma[$h-1]['can_tiempo'];
              $act_minuto = (int)$this -> genera_alarma[$h-0]['can_tiempo'];
              $sig_minuto = (int)$this -> genera_alarma[$h+1]['can_tiempo'];
              //------------------------------------------------------------
              if ( $sig_minuto ) { 
                if ( $dif_minuto > $ant_minuto && $dif_minuto <= $act_minuto ) {
                  $m[$key]['can_'.$act_minuto.'mins'] ++;
                }
              }
              else { 
                if ( $dif_minuto > $ant_minuto ) {
                  $m[$key]['can_'.$act_minuto.'mins'] ++;
                }
              }
            }
          }

          
        }
        
        
      }
      

      
   
      
    }
    
    
    
    //echo '<h1>'.$willy.'</h1>';
    
    

    /*
    echo '<pre>';
    print_r( $m['890104487']['det_despac']['can_sinret'] );
    echo '</pre>';
    */

    
    /*
    echo '<pre>';
    print_r( $_MINS );
    echo '</pre>';
    */
    
    /*
     * 
    //---------------------------
    ksort( $m, SORT_STRING );
    //---------------------------
     */
    
    /*
    echo '<pre>';
    print_r( $m );
    echo '</pre>';
    
    die();
    */
    
    
    return $m;
  }


}

$_DATA = count( $_POST ) == 0 ? $_GET : $_POST;
$InformBandejEntrad = new InformBandejEntrad( $this -> conexion, $_DATA );

?>