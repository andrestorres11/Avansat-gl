<?php
/*******************************************************************************************************
 *@class: DespacTransi                                                                                 *
 *@company: Intrared.net                                                                               *
 *@author: Christiam Barrera( The Messias, The Only One, The Choosen One, The Unique )                 *
 *@date: 2012-06-01                                                                                    *
 *@brief: Clase que realiza las consultas para retornar la información de los Despachos en Transito    *
 *******************************************************************************************************/
class DespacTransi { 
  var $conexion      = NULL;
  var $SUBMIT        = NULL;
  var $genera_alarma = NULL;
  var $cod_perfil    = NULL;
  var $cod_usuari    = NULL;
  var $superusuario  = NULL;
  var $tmp_transp    = NULL;
  var $cod_serlis    = 3302;
  var $tot_despac    = array();
  
  //----------------------------------------------------------------------------------------------------
  //@method: DespacTransi                                                                              |
  //@brief : Constructor de la Clase                                                                   |
  //----------------------------------------------------------------------------------------------------
  function DespacTransi( $conexion, $mData ) { 
    $this -> conexion = $conexion;
    $this -> SUBMIT = $mData;
    $this -> genera_alarma = $this -> GetGeneraAlarma();
    //-------------------------------------------------
    $datos_usuario = $_SESSION['datos_usuario'];
    $this -> cod_perfil = $datos_usuario['cod_perfil'];
    $this -> cod_usuari = $datos_usuario['cod_usuari'];
    //----------------------------------------------------
    $this -> superusuario = $this -> VerifySuperUsuario();
    //----------------------------------------------------
    if ( !$this -> superusuario ) :
      if ( !$this -> VerifyHorario() && $this -> setFilters() == NULL ) : 
        $mensaje = 'El Usuario "'.$datos_usuario['cod_usuari'].'" no Tiene Horario Asignado.';
        $mens = new mensajes();
        $mens -> advert( NULL, $mensaje );
        die();
      endif;
    endif;
  }
  
  //----------------------------------------------------------------------------------------------------
  //@method: GetDespacTransiReport                                                                     |
  //@brief : Retorna la matrix asociativa por transportadora para el informe de Despachos en Transito  |
  //----------------------------------------------------------------------------------------------------
  function GetDespacTransiReport() {
    return $this -> GetDespacTransp();
  }
  
  
  //-----------------------------------------------------------------------------------------------
  //@method: GetTranspData                                                                        |
  //@brief : Retorna la información del Despacho en Transito determinado segun su transportadora  |
  //-----------------------------------------------------------------------------------------------
	function GetTranspData( $cod_transp ) 
	{
		//echo '<br />cod_transp: ' . $cod_transp;
		$this -> cod_transp = $cod_transp;
		$_REPORT = $this -> GetDespacTransp();
		
		if( $this -> cod_transp ) 
		{
			$_INFO = $_REPORT[$cod_transp]['all_despac'];
		}
		else 
		{
			$_INFO = array();
			
			foreach ( $_REPORT as $_TRANSP ) :
				$_KEYS = array_keys( $_TRANSP['all_despac'] );
				foreach ( $_KEYS as $key ) :
					$_INFO[$key] .= $_INFO[$key] == NULL ? $_TRANSP['all_despac'][$key] : ', ' . $_TRANSP['all_despac'][$key];
				endforeach;
			endforeach;
		}
		
		/*echo "<pre>";
		print_r($_INFO);
		echo "</pre>";*/
		
		return $_INFO ? $_INFO : FALSE;
		
	}
  
  //-----------------------------------------------------------------------------------------------
  //@method: GetDespacData                                                                        |
  //@brief : Retorna la información del Despacho en Transito determinado segun su transportadora  |
  //-----------------------------------------------------------------------------------------------
  function GetDespacData( $num_despac, $data = TRUE ) { 
    
    if ( $data ) {
      $mSql  = "SELECT a.cod_manifi, 
                       a.cod_ciuori, 
                       c.abr_ciudad AS nom_ciuori, 
                       c.cod_depart AS cod_depori, 
                       UPPER( d.abr_depart ) AS nom_depori, 
                       a.cod_ciudes, 
                       f.abr_ciudad AS nom_ciudes, 
                       f.cod_depart AS cod_depdes, 
                       UPPER( g.abr_depart ) AS nom_depdes, 
                       b.num_placax, 
                       b.cod_conduc, 
				       IF( b.nom_conduc IS NOT NULL, b.nom_conduc, UPPER( TRIM( i.abr_tercer ) ) ) AS abr_conduc,
                       UPPER( TRIM( i.nom_tercer ) ) AS nom_conduc, 
                       UPPER( TRIM( i.nom_apell1 ) ) AS ap1_conduc, 
                       IF( a.con_telmov IS NULL OR a.con_telmov = '', i.num_telmov , a.con_telmov ) AS cel_conduc 
                  FROM ".BASE_DATOS.".tab_despac_despac a, 
                       ".BASE_DATOS.".tab_despac_vehige b, 
                       ".BASE_DATOS.".tab_genera_ciudad c, 
                       ".BASE_DATOS.".tab_genera_depart d, 
                       ".BASE_DATOS.".tab_genera_ciudad f, 
                       ".BASE_DATOS.".tab_genera_depart g, 
                       ".BASE_DATOS.".tab_tercer_tercer i  
                 WHERE a.num_despac = b.num_despac 
                   AND a.cod_ciuori = c.cod_ciudad 
                   AND c.cod_depart = d.cod_depart  
                   AND a.cod_ciudes = f.cod_ciudad 
                   AND f.cod_depart = g.cod_depart  
                   AND b.cod_conduc = i.cod_tercer 
                   AND a.num_despac = '".$num_despac."' 
        ";

      //echo '<hr />' . $mSql;
              
      $consul = new Consulta( $mSql, $this -> conexion );
      $_ARRAY = $consul -> ret_matrix( 'a' );
      $_ARRAY = $_ARRAY[0];
    }
    
    /*
    echo '<pre>';
    print_r( $_ARRAY );
    echo '</pre>';
    */
    
    $this -> num_despac = $num_despac;
    $_REPORT = $this -> GetDespacTransp();
    $_KEYS = @array_keys( $_REPORT );
    $_INFO = $_REPORT[$_KEYS[0]];

    if( isset( $_ARRAY ) )
      return $_INFO ? array_merge( $_INFO, $_ARRAY ) : FALSE;
    else
      return $_INFO ? $_INFO : FALSE;

  }
  
  //-----------------------------------------------------------------------------------------------
  //@method: sumarMinutosFecha                                                                    |
  //@brief : Retorna la Fecha resultado de sumar a una Fecha una cantidad determinada de minutos  |
  //-----------------------------------------------------------------------------------------------
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
    $Minutos = ( (int)$Minutos) + ((int)$MinASumar ); 
    $FechaNueva = date( "Y-m-d H:i:s", mktime( $Horas, $Minutos, $Segundos, $Mes, $Dia, $Ano ) );
    return $FechaNueva;
  } 
  
  //-----------------------------------------------------------------------------------------------
  //@method: diferenciaMinutosFecha                                                               |
  //@brief : Retorna la cantidad de minutos existentes entre dos fechas determinadas              |
  //-----------------------------------------------------------------------------------------------
  function diferenciaMinutosFecha( $fecha_inicio, $fecha_fin ) {
    $time_inicio = strtotime( $fecha_inicio );
    $time_fin = strtotime( $fecha_fin );
    
    //return round( ( $time_inicio - $time_fin ) / 60 );
    
    if ( $time_inicio > $time_fin ) {
      return (int)abs( round( ( $time_inicio - $time_fin ) / 60 ) );
    }
    else {
      return (int)abs( round( ( $time_fin - $time_inicio ) / 60 ) );
    }
  }
  
  //---------------------------------------------------------------------------------------------------
  //@method: GetDespacDespac                                                                          |
  //@brief : Retorna la información de transportadoras y terceros de todos los despachos en transito  |
  //---------------------------------------------------------------------------------------------------
  function GetDespacDespac( $ind_urbano = NULL ) { 
    if ( $this -> superusuario ) { 
      if ( $this -> SUBMIT['usr_asigna'] == 'SIN ASIGNAR' ) {
        $notin = $this -> GetDespacUsuariAsigna( NULL );
      }
      elseif ( $this -> SUBMIT['usr_asigna'] ) {
        $in = $this -> GetDespacUsuariAsigna( $this -> SUBMIT['usr_asigna'] );
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
                     a.ind_defini,
					 a.tie_contra,
					 a.ind_tiemod,
					 a.obs_tiemod
                FROM ".BASE_DATOS.".tab_despac_despac a, 
                     ".BASE_DATOS.".vis_despac_seguim d, 
                     ".BASE_DATOS.".tab_despac_vehige b, 
                     ".BASE_DATOS.".tab_tercer_tercer c 
               WHERE a.num_despac = d.num_despac 
                 AND a.num_despac = b.num_despac 
                 AND b.cod_transp = c.cod_tercer 
                 AND a.fec_salida IS NOT NULL 
                 AND a.fec_salida <= NOW() 
                 AND a.fec_llegad IS NULL 
                 AND a.ind_planru = 'S' 
                 AND a.ind_anulad = 'R' ";
    if ( $this -> cod_transp ) {
      $mSql .= $this -> cod_transp == NULL ? NULL : " AND b.cod_transp = '".$this -> cod_transp."' ";
    }
    else { 
      $mSql .= $in == NULL ? NULL : " AND b.cod_transp IN ( " . $in . " ) ";
      $mSql .= $notin == NULL ? NULL : " AND b.cod_transp NOT IN ( " . $notin . " ) ";
      //---------------------------------------------------------------------------------------------
      $mSql .= $this -> num_despac == NULL ? NULL : " AND a.num_despac = '".$this -> num_despac."' ";
      //---------------------------------------------------------------------------------------------
    }
    
    //-----------------------------
    $mSql .= $this -> setFilters();
    //-----------------------------
    
    if( $ind_urbano != NULL )
    {
      $mSql .= " AND a.cod_ciuori = a.cod_ciudes ";
    }
    
    $mSql .= " GROUP BY a.num_despac ORDER BY 3 ";
    
    //echo '<hr />' . $mSql;
    
    $consul = new Consulta( $mSql, $this -> conexion );
    $matriz = $consul -> ret_matrix( 'a' );
    return $matriz;
  }
  
  //-------------------------------------------------------------------------
  //@method: GetDespacUsuariAsigna                                          |
  //@brief : Retorna los usuarios asignados al monitoreo de los despachos   |
  //-------------------------------------------------------------------------
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
    $matriz = $consul -> ret_matrix( 'a' );
    $in  = NULL;
    for ( $i = 0; $i < sizeof( $matriz ); $i++ ) {
      $in .= $in == NULL ? $matriz[$i]['cod_tercer'] : ", " . $matriz[$i]['cod_tercer'];
    }
    return $in;
  }
  
  //-------------------------------------------------------------------------
  //@method: GetGeneraAlarma                                                |
  //@brief : Retorna la Información de las Alarmas de Tiempo de retraso     |
  //-------------------------------------------------------------------------
  function GetGeneraAlarma() {
    $mSql  = "SELECT a.cod_alarma, 
                     UPPER( a.nom_alarma ) AS nom_alarma, 
                     a.cant_tiempo AS can_tiempo, 
                     a.cod_colorx 
                FROM ".BASE_DATOS.".tab_genera_alarma a ORDER BY 1 ";
    $consul = new Consulta( $mSql, $this -> conexion );
    $matriz = $consul -> ret_matrix( 'a' );
    return $matriz;
  }

  //-------------------------------------------------------------------------
  //@method: GetGeneraTipser                                                |
  //@brief : Retorna los Tipos de Servicio de los despachos                 |
  //-------------------------------------------------------------------------
  function GetGeneraTipser() {
    $mSql  = "SELECT a.cod_tipser, 
                     UPPER( TRIM( a.nom_tipser ) ) AS nom_tipser 
                FROM ".BASE_DATOS.".tab_genera_tipser a ORDER BY 2 ";
    $consul = new Consulta( $mSql, $this -> conexion );
    $matriz = $consul -> ret_matrix( 'a' );
    return $matriz;
  }
  
  //------------------------------------------------------------------------------------
  //@method: GetTranspTipser                                                           |
  //@brief : Retorna la Información de los tipos de servicio según la transportadora   |
  //------------------------------------------------------------------------------------
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
    $matriz = $consul -> ret_matrix( 'a' );
    return $matriz[0];
  }
  
  //------------------------------------------------------------------------------------
  //@method: GetDespacPendidLlegad                                                     |
  //@brief : Determina si un despacho está o no pendiente por llegar                   |
  //------------------------------------------------------------------------------------
  function GetDespacPendidLlegad( $num_despac ) {
    $mSql  = "SELECT a.cod_contro 
                FROM ".BASE_DATOS.".vis_despac_seguim a 
               WHERE a.num_despac = '".$num_despac."'  AND a.ind_estado = '1' ORDER BY a.fec_planea DESC LIMIT 1 ";
    $consul = new Consulta( $mSql, $this -> conexion );
    $matriz = $consul -> ret_matrix( 'a' );
    $cod_contro = $matriz[0]['cod_contro'];
    
    $mSql  = "SELECT 1  
                FROM ".BASE_DATOS.".vis_despac_noveda a 
               WHERE a.num_despac = '".$num_despac."' 
                 AND a.cod_contro IN ( '".$cod_contro."' ) ";
    $consul = new Consulta( $mSql, $this -> conexion );
    $matriz = $consul -> ret_matrix( 'a' );
    return count( $matriz ) == 0 ? FALSE : TRUE;
  }
  
  //------------------------------------------------------------------------------------
  //@method: GetDespacTiempoPropio                                                     |
  //@brief : Determina si un despacho está con novedad de tiempo propio                |
  //------------------------------------------------------------------------------------
  function GetDespacTiempoPropio( $num_despac ) {
    $mSql  = "SELECT 1  
                FROM ".BASE_DATOS.".tab_despac_despac a 
               WHERE a.num_despac = '".$num_despac."' 
                 AND a.ind_tiemod = '1' ";
    $consul = new Consulta( $mSql, $this -> conexion );
    $matriz = $consul -> ret_matrix( 'a' );
    return count( $matriz ) == 0 ? FALSE : TRUE;
  }
  
  
  
  //------------------------------------------------------------------------------------
  //@method: GetDespacUltimaNoveda                                                     |
  //@brief : Retorna los datos de la última novedad de un despacho determinado         |
  //------------------------------------------------------------------------------------
  function GetDespacUltimaNoveda( $num_despac ) {
    $mSql  = "SELECT a.fec_creaci, 
                     a.cod_noveda, 
                     a.tiem_duraci AS tie_noveda, 
                     a.fec_noveda, 
                     a.cod_contro 
                FROM ".BASE_DATOS.".vis_despac_noveda a 
               WHERE a.num_despac = '".$num_despac."' ORDER BY fec_creaci DESC LIMIT 1";
    $consul = new Consulta( $mSql, $this -> conexion );
    $matriz = $consul -> ret_matrix( 'a' );
    return $matriz[0];
  }
  
  //---------------------------------------------------------------------------------------------
  //@method: GetDespacUltimaNovedaContro                                                        |
  //@brief : Retorna los datos de la última novedad o último contro de un despacho determinado  |
  //---------------------------------------------------------------------------------------------
  function GetDespacUltimaNovedaContro( $num_despac ) {
    $mSql  = "SELECT a.fec_creaci, 
                     a.cod_noveda, 
                     a.tiem_duraci AS tie_noveda, 
                     a.fec_noveda, b.nom_contro AS nom_sitiox
                FROM ".BASE_DATOS.".tab_despac_noveda a,
                     ".BASE_DATOS.".tab_genera_contro b
               WHERE a.num_despac = '".$num_despac."' 
                 AND a.cod_contro = b.cod_contro
               UNION 
              SELECT a.fec_creaci, 
                     a.cod_noveda, 
                     a.tiem_duraci AS tie_noveda, 
                     a.fec_contro AS fec_noveda,
                     IF( UPPER( IF( a.cod_noveda = ".CONS_NOVEDA_GPSXXX.", REPLACE(SUBSTRING(a.obs_contro, 11, INSTR(a.obs_contro, 'Velocidad')-11 ),',,',','), c.nom_sitiox ) ) IS NULL, d.nom_contro, UPPER( IF( a.cod_noveda = ".CONS_NOVEDA_GPSXXX.", REPLACE(SUBSTRING(a.obs_contro, 11, INSTR(a.obs_contro, 'Velocidad')-11 ),',,',','), c.nom_sitiox ) )) AS nom_sitiox
                FROM ".BASE_DATOS.".tab_despac_contro a,
                     ".BASE_DATOS.".tab_despac_sitio c,
                     ".BASE_DATOS.".tab_genera_contro d                     
               WHERE a.num_despac = '".$num_despac."' 
                 AND a.cod_sitiox = c.cod_sitiox AND 
                     a.cod_contro = d.cod_contro";
    $mSql  = "SELECT * FROM ( " . $mSql . " ) AS sub ORDER BY fec_creaci DESC LIMIT 1";
    /*if( $num_despac == '427541' )
      echo "<hr>".$mSql;*/
    $consul = new Consulta( $mSql, $this -> conexion );
    $matriz = $consul -> ret_matrix( 'a' );
    return $matriz[0];
  }
  
  //---------------------------------------------------------------------------------------------
  //@method: GetDespacUltimaNovedaContro                                                        |
  //@brief : Retorna los datos de la última novedad o último contro de un despacho determinado  |
  //---------------------------------------------------------------------------------------------
  function GetDespacUltimaNovedaSinManala( $num_despac ) {
    $mSql  = "SELECT a.fec_creaci, 
                     a.cod_noveda, 
                     a.tiem_duraci AS tie_noveda, 
                     a.fec_noveda 
                FROM ".BASE_DATOS.".vis_despac_noveda a,
                     ".BASE_DATOS.".tab_genera_noveda b
               WHERE a.num_despac = '".$num_despac."'
                 AND a.cod_noveda = b.cod_noveda
                 AND b.ind_manala = '0'
               UNION 
              SELECT a.fec_creaci, 
                     a.cod_noveda, 
                     a.tiem_duraci AS tie_noveda, 
                     a.fec_contro AS fec_noveda 
                FROM ".BASE_DATOS.".vis_despac_contro a,
                     ".BASE_DATOS.".tab_genera_noveda b
               WHERE a.num_despac = '".$num_despac."'
                 AND a.cod_noveda = b.cod_noveda
                 AND b.ind_manala = '0'
               ";
    $mSql  = "SELECT * FROM ( " . $mSql . " ) AS sub ORDER BY fec_creaci DESC LIMIT 1";
    /*if( $num_despac == '427541' )
      echo "<hr>".$mSql;*/
    $consul = new Consulta( $mSql, $this -> conexion );
    $matriz = $consul -> ret_matrix( 'a' );
    return $matriz[0];
  }
  
  
  
  //-------------------------------------------------------------------------------
  //@method: GetDespacUltimoSitio                                                 |
  //@brief : Retorna los datos del último sitio reportado para el despacho        |
  //-------------------------------------------------------------------------------
  function GetDespacUltimoSitio( $num_despac ) {
    $mSql  = "SELECT a.fec_creaci, 
                     a.cod_noveda, 
                     a.tiem_duraci AS tie_noveda, 
                     a.fec_noveda 
                FROM ".BASE_DATOS.".vis_despac_noveda a 
               WHERE a.num_despac = '".$num_despac."' ORDER BY fec_creaci DESC LIMIT 1";
    $consul = new Consulta( $mSql, $this -> conexion );
    $matriz = $consul -> ret_matrix( 'a' );
    return $matriz[0];
  }
  
  //-------------------------------------------------------------------------------
  //@method: GetDespacNoveda                                                      |
  //@brief : Retorna los datos de una novedad determinada                         |
  //-------------------------------------------------------------------------------
  function GetDespacNoveda( $cod_noveda ) {
    $mSql  = "SELECT a.ind_manala, 
                     a.ind_tiempo, 
                     a.nov_especi, 
                     a.ind_fuepla 
                FROM ".BASE_DATOS.".tab_genera_noveda a 
               WHERE a.cod_noveda = '".$cod_noveda."' ";
    $consul = new Consulta( $mSql, $this -> conexion );
    $matriz = $consul -> ret_matrix( 'a' );
    return $matriz[0];
  }
  
  //------------------------------------------------------------------------------------------------
  //@method: GetDespacUltimoPuestoEAL                                                              |
  //@brief : Retorna los datos del puesto actual y siguiente del despacho en transito determinado  |
  //------------------------------------------------------------------------------------------------
  function GetDespacUltimoPuestoEAL( $num_despac, $cod_contro ) {
    $mSql  = "SELECT a.fec_planea, 
                     a.fec_alarma, 
                     a.ind_estado 
                FROM ".BASE_DATOS.".vis_despac_seguim a 
               WHERE a.num_despac = '".$num_despac."' 
                 AND a.cod_contro = '".$cod_contro."' 
            ORDER BY a.fec_planea ASC ";
    $consul = new Consulta( $mSql, $this -> conexion );
    $matriz = $consul -> ret_matrix( 'a' );
    $_ACTUAL = $matriz[0];
    //-------------------------------------------
    $i = $_ACTUAL['ind_estado'] == 1 ? "1" : "0";
    //-------------------------------------------
    $mSql  = "SELECT a.fec_planea, 
                     a.fec_alarma
                FROM ".BASE_DATOS.".vis_despac_seguim a 
                WHERE a.num_despac = '".$num_despac."' 
                  AND a.ind_estado = '1'
             ORDER BY a.fec_planea ASC LIMIT ".$i.", 1 ";
    $consul = new Consulta( $mSql, $this -> conexion );
    $matriz = $consul -> ret_matrix( 'a' );
    $_SIGUIE = $matriz[0];
    return array( $_ACTUAL, $_SIGUIE );
  }
  
  //-------------------------------------------------------------------------------
  //@method: GetDespacPuestoHabili                                                |
  //@brief : Retorna las Fechas planeadas de un despacho determinado              |
  //-------------------------------------------------------------------------------
  function GetDespacPuestoHabili( $num_despac ) {
    $mSql  = "SELECT a.fec_planea, 
                     a.fec_alarma
                FROM ".BASE_DATOS.".vis_despac_seguim a 
               WHERE a.num_despac = '".$num_despac."' 
                 AND a.ind_estado = '1'
            ORDER BY a.fec_planea ASC ";
    $consul = new Consulta( $mSql, $this -> conexion );
    $matriz = $consul -> ret_matrix( 'a' );
    return $matriz;
  }
  
  //-------------------------------------------------------------------------------
  //@method: GetDespacAlarmaEAL                                                   |
  //@brief : Determina si un despacho está o no en Alarma EAL                     |
  //-------------------------------------------------------------------------------
  function GetDespacAlarmaEAL( $num_despac ) {
    $mSql  = "SELECT a.num_despac, a.cod_contro
                FROM ".BASE_DATOS.".vis_despac_seguim a, ".BASE_DATOS.".tab_genera_contro b
               WHERE a.cod_contro = b.cod_contro
                 AND b.ind_virtua = 0
                 AND a.ind_estado = 0
                 AND a.num_despac = '".$num_despac."' 
                 AND ( a.num_despac, a.cod_contro ) NOT IN ( SELECT num_despac, cod_contro
                                                               FROM ".BASE_DATOS.".vis_despac_noveda ) ";
    $consul = new Consulta( $mSql, $this -> conexion );
    $matriz = $consul -> ret_matrix( 'a' );
    return sizeof( $matriz ) == 0 ? FALSE : TRUE;
  }
  
  //-----------------------------------------------------------------------------------
  //@method: GetDespacAlarmaEAL                                                       |
  //@brief : Determina si el perfil del usuario logueado pertenece a un SuperUsuario  |
  //-----------------------------------------------------------------------------------
  function VerifySuperUsuario() {
    $_SUPER[] = COD_PERFIL_ADMINIST;
    $_SUPER[] = COD_PERFIL_SUPEGENE;
    $_SUPER[] = COD_PERFIL_SUPEFARO;
    $_SUPER[] = COD_PERFIL_SUPERUSR;
    $_SUPER[] = COD_PERFIL_AUDITORX;
    $_SUPER[] = COD_PERFIL_DIRECTOR;
    return in_array( $this -> cod_perfil, $_SUPER ) ? TRUE : FALSE;
  }
  
  //-----------------------------------------------------------------------------------
  //@method: GetSuperUsuario                                                          |
  //@brief : Retorna los Superusuarios según una transportadora determinada           |
  //-----------------------------------------------------------------------------------
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
    $_USUARI = $consult -> ret_matrix( 'a' );
    $size = sizeof( $_USUARI );
    $str = NULL;
    for ( $i = 0; $i < $size; $i++ ) {
      $str .= $str == NULL ? $_USUARI[$i]['cod_usuari'] : ', ' . $_USUARI[$i]['cod_usuari'];
    }
    return $str == NULL ? 'SIN ASIGNAR' : $str;
  }
  
  //-----------------------------------------------------------------------------------
  //@method: GetListSuperUsuario                                                      |
  //@brief : Retorna el listado de superusuarios del sistema                          |
  //-----------------------------------------------------------------------------------
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
    $_USUARI = $consult -> ret_matrix( 'i' );
    return array_merge( array( array( 'SIN ASIGNAR' ) ), $_USUARI );
  }
  
  //--------------------------------------------------------------------------------
  //@method: GetDespacTransp                                                       |
  //@brief : Retorna la Matriz con la Información de Despachos por Transportadora  |
  //--------------------------------------------------------------------------------
  function GetDespacTransp( $ind_urbano = NULL ) { 
  $nov_especi = 126;
    //--------------------------------------------
    $fec_actual = date( 'Y-m-d H:i:s' );
    //--------------------------------------------
    $_REPORT = $this -> GetDespacDespac( $ind_urbano );
    
	/*echo "<pre>";
	print_r($_REPORT);
	echo "</pre>";*/
	
    $size_report = sizeof( $_REPORT );
    //--------------------------------------------
    $m = NULL;
    for ( $i = 0; $i < $size_report; $i++ ) {
	  //----------------------
      $_DESPAC = $_REPORT[$i];
      //----------------------------
      $key = $_DESPAC['cod_transp'];
      //----------------------------
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
	  
	  if ( $this -> SUBMIT['servic']  ) :
		if ( !in_array( $m[$key]['cod_tipser'], $this -> SUBMIT['servic'] ) ) : 
		  unset( $m[$key] );
          continue;
		endif;
	  endif;

	  /*
      if ( $this -> SUBMIT['cod_tipser'] && $this -> SUBMIT['cod_tipser'] <> $m[$key]['cod_tipser'] ) { 
        unset( $m[$key] );
        continue;
      }
      */
      
      
      
      //-----------------------------------------------------------
      //@C O L U M N A   C A N T I D A D   D E   D E S P A C H O S
      //-----------------------------------------------------------
      $m[$key]['can_despac'] ++;
      $m[$key]['all_despac']['can_despac'] .= $m[$key]['all_despac']['can_despac'] == NULL ? $_DESPAC['num_despac'] : ', ' . $_DESPAC['num_despac'];
      //------------------------------------------------------------------------
      //@Despacho pendiente por llegar?
      $PENDID_LLEGAD = $this -> GetDespacPendidLlegad( $_DESPAC['num_despac'] );
      //@Despacho con tiempo propio?
      $DESPAC_TIEPRO = $this -> GetDespacTiempoPropio( $_DESPAC['num_despac'] );
      //-----------------------------------------------
      //@Cantidad de despachos pendientes por llegar
      //-----------------------------------------------
      if ( $PENDID_LLEGAD ) {
        $m[$key]['can_penlle'] ++;
        $m[$key]['all_despac']['can_penlle'] .= $m[$key]['all_despac']['can_penlle'] == NULL ? $_DESPAC['num_despac'] : ', ' . $_DESPAC['num_despac'];
      }
	  //-----------------------------------------------
      //@Cantidad de despachos con tiempo propio 	    
      //-----------------------------------------------
	  if ( $DESPAC_TIEPRO ) {
        $m[$key]['can_tiepro'] ++;
        $m[$key]['all_despac']['can_tiepro'] .= $m[$key]['all_despac']['can_tiepro'] == NULL ? $_DESPAC['num_despac'] : ', ' . $_DESPAC['num_despac'];
      }
      //------------------------------------------------------------------------
      //@Datos de última novedad / control
      $_ULTNOVCON = $this -> GetDespacUltimaNovedaContro( $_DESPAC['num_despac'] );
      //---------------------------------------------------------------------------
      //@Datos de última novedad 
      $_ULTNOV = $this -> GetDespacUltimaNoveda( $_DESPAC['num_despac'] );
      
      //Para Novedad 126 //
      if( $_ULTNOVCON['cod_noveda'] == $nov_especi )
      {
      $m[$key]['can_especi']++;
      $m[$key]['all_despac']['can_especi'] .= $m[$key]['all_despac']['can_especi'] == NULL ? $_DESPAC['num_despac'] : ', ' . $_DESPAC['num_despac'];
      }
            
      //---------------------------------------------------------------
      //@Novedades del Despacho
      $_NOVEDA = $this -> GetDespacNoveda( $_ULTNOVCON['cod_noveda'] );
      /*if( $_DESPAC['num_despac'] == '427541'	 )
      {
        echo "<pre>";
        print_r( $_NOVEDA );
        echo "<pre>";
      }*/
      
      if ( $_NOVEDA['ind_manala'] == '1' )
      {
        $_ULTNOVCON = $this -> GetDespacUltimaNovedaSinManala( $_DESPAC['num_despac'] );
      }
      
      //---------------------------------------------------------------
      if ( $this -> superusuario && $m[$key]['usr_asigna'] == NULL ) {
        $m[$key]['usr_asigna'] = $this -> GetSuperUsuario( $_DESPAC['cod_transp'] );
      }
      if ( $this -> superusuario && ( $m[$key]['cod_tipser'] == '1' || $m[$key]['cod_tipser'] == '3' ) ) { 
        $_EAL = $this -> GetDespacAlarmaEAL( $_DESPAC['num_despac'] );
        if ( $_EAL ) {
          $m[$key]['can_alaEAL'] ++;
          $m[$key]['all_despac']['can_alaEAL'] .= $m[$key]['all_despac']['can_alaEAL'] == NULL ? $_DESPAC['num_despac'] : ', ' . $_DESPAC['num_despac'];
        }
      }
      
      if ( $_NOVEDA['ind_fuepla'] == '1' && date( 'Y-m-d H:i:s' ) < $this -> sumarMinutosFecha( $_ULTNOVCON['fec_noveda'], $_ULTNOVCON['tie_noveda'] ) ) {
        $m[$key]['can_fuepla'] ++;
        $m[$key]['all_despac']['can_fuepla'] .= $m[$key]['all_despac']['can_fuepla'] == NULL ? $_DESPAC['num_despac'] : ', ' . $_DESPAC['num_despac'];
      }
      
      if ( $_DESPAC['ind_defini'] == '1' ) {
        $m[$key]['can_defini'] ++;
        $m[$key]['all_despac']['can_defini'] .= $m[$key]['all_despac']['can_defini'] == NULL ? $_DESPAC['num_despac'] : ', ' . $_DESPAC['num_despac'];
      }
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
          if ( $_NOVEDA['ind_fuepla'] == '1' && date( 'Y-m-d H:i:s' ) < $fec_progra ) {
            $m[$key]['can_fuepla'] ++;
            $m[$key]['all_despac']['can_fuepla'] .= $m[$key]['all_despac']['can_fuepla'] == NULL ? $_DESPAC['num_despac'] : ', ' . $_DESPAC['num_despac'];
          }
          
           */
          //----------------------------------------------------------------------------------
          $dif_minuto = $this -> diferenciaMinutosFecha( $fec_actual, $fec_progra );
          //----------------------------------------------------------------------------------
          //------------------
          //@SI NO HAY RETRASO
          //------------------
          if ( $fec_actual < $fec_progra && $_DESPAC['fec_manala'] == NULL && !$PENDID_LLEGAD ) {
            $m[$key]['can_sinret'] ++;
            $m[$key]['all_despac']['can_sinret'] .= $m[$key]['all_despac']['can_sinret'] == NULL ? $_DESPAC['num_despac'] : ', ' . $_DESPAC['num_despac'];
          }
          else { 
            for ( $h = 0, $size_alarma = count( $this -> genera_alarma ); $h < $size_alarma; $h++ ) { 
              //------------------------------------------------------------
              $ant_minuto = (int)$this -> genera_alarma[$h-1]['can_tiempo'];
              $act_minuto = (int)$this -> genera_alarma[$h-0]['can_tiempo'];
              $sig_minuto = (int)$this -> genera_alarma[$h+1]['can_tiempo'];
              //------------------------------------------------------------
              if ( $sig_minuto ) { 
                if ( $dif_minuto > $ant_minuto && $dif_minuto <= $act_minuto ) : 
                  if ( $_DESPAC['ind_defini'] != '1' ) : 
                    $m[$key]['can_'.$act_minuto.'mins'] ++;
                    $m[$key]['all_despac']['can_'.$act_minuto.'mins'] .= $m[$key]['all_despac']['can_'.$act_minuto.'mins'] == NULL ? $_DESPAC['num_despac'] : ', ' . $_DESPAC['num_despac'];
                  endif;
                endif;
              }
              else { 
                if ( $dif_minuto > $ant_minuto ) : 
                  if ( $_DESPAC['ind_defini'] != '1' ) : 
                    $m[$key]['can_'.$act_minuto.'mins'] ++;
                    $m[$key]['all_despac']['can_'.$act_minuto.'mins'] .= $m[$key]['all_despac']['can_'.$act_minuto.'mins'] == NULL ? $_DESPAC['num_despac'] : ', ' . $_DESPAC['num_despac'];
                  endif;
                endif;
              }
              if ( $this -> num_despac && $m[$key]['can_'.$act_minuto.'mins'] > 0 ) {
                $m[$key]['col_alarma'] = $this -> genera_alarma[$h]['cod_colorx'];
              }
            }
          }
        }
        else {
          if ( !$PENDID_LLEGAD ) { 
            $m[$key]['can_sinret'] ++;
            $m[$key]['all_despac']['can_sinret'] .= $m[$key]['all_despac']['can_sinret'] == NULL ? $_DESPAC['num_despac'] : ', ' . $_DESPAC['num_despac'];
          }
        } 
      } //FIN EAL
      
      //----------------------------------------------------------------
      //@MONITOREO ACTIVO O COMBINADO
      //----------------------------------------------------------------
      else {
	  
		/*echo "<pre>";
		print_r($_DESPAC);
		echo "</pre>";*/
		
		if( $_DESPAC['ind_tiemod'] == 1 )
		{
			$tie_tipser = $_DESPAC['tie_contra'];
		}
		else
		{
			$tie_tipser = $_DESPAC['cod_tipdes'] == 1 ? $_TIPSER['tie_conurb'] : $_TIPSER['tie_contro'];
        }
		/*if($_DESPAC['num_despac'] == '2062')
		{
		echo "<pre>";
		echo $tie_tipser;
		echo "</pre>";
		}*/
		
        if ( sizeof( $_ULTNOVCON ) > 0 ) {
          $fec_progra = $_ULTNOVCON['fec_noveda'];
          $fec_progra = $this -> sumarMinutosFecha( $fec_progra, $tie_tipser );
        }
        else {
        
          $fec_progra = $_DESPAC['fec_salida'];
           
        }
        
        /*
        //-------------------------------------------------------------------
        if ( $_DESPAC['num_despac'] == '450151' )
        {
          echo "<hr>fec_progra: ".$fec_progra;
          echo "<br>tie_tipser: ".$tie_tipser;
        }
       
         */
        
        /*if($_DESPAC['num_despac'] == '427541')
        {
          echo "<br>fec_progra: ".$fec_progra;
        }*/
        //-------------------------------------------------------------------
        if ( (int)$_ULTNOVCON['tie_noveda'] > 0 ) {
          $fec_progra = $this -> sumarMinutosFecha( $_ULTNOVCON['fec_noveda'], $_ULTNOVCON['tie_noveda'] );
        }
        //----------------------------------------------------------------------------------
        $dif_minuto = $this -> diferenciaMinutosFecha( $fec_actual, $fec_progra );
        
        /*
        if ( $_DESPAC['num_despac'] == '449820' )
        {
          echo "<hr>fec_progra: ".$fec_progra;
          echo "<br>tie_tipser: ".$tie_tipser;
          echo "<br>dif_minuto: ".$dif_minuto;
          echo "<br>fec_manala: ".$_DESPAC['fec_manala'];
        }
        */
          
        
        if ( (string)$_ULTNOVCON['fec_noveda'] == (string)$_DESPAC['fec_manala'] ) :
          $_DESPAC['fec_manala']  = NULL;
        endif;
        
          
        //----------------------------------------------------------------------------------
        if ( $fec_actual < $fec_progra && $_DESPAC['fec_manala'] == NULL && !$PENDID_LLEGAD ) { 
          $m[$key]['can_sinret'] ++;
          $m[$key]['all_despac']['can_sinret'] .= $m[$key]['all_despac']['can_sinret'] == NULL ? $_DESPAC['num_despac'] : ', ' . $_DESPAC['num_despac'];
        }
        else { 
          if ( !$PENDID_LLEGAD ) {
            for ( $h = 0, $size_alarma = count( $this -> genera_alarma ); $h < $size_alarma; $h++ ) { 
              //------------------------------------------------------------
              $ant_minuto = (int)$this -> genera_alarma[$h-1]['can_tiempo'];
              $act_minuto = (int)$this -> genera_alarma[$h-0]['can_tiempo'];
              $sig_minuto = (int)$this -> genera_alarma[$h+1]['can_tiempo'];
              //------------------------------------------------------------
              if ( $sig_minuto ) { 
                if ( $dif_minuto > $ant_minuto && $dif_minuto <= $act_minuto ) : 
                  if ( $_DESPAC['ind_defini'] != '1' ) :
                    $m[$key]['can_'.$act_minuto.'mins'] ++;
                    $m[$key]['all_despac']['can_'.$act_minuto.'mins'] .= $m[$key]['all_despac']['can_'.$act_minuto.'mins'] == NULL ? $_DESPAC['num_despac'] : ', ' . $_DESPAC['num_despac'];
                  endif;  
                endif;
              }
              else { 
                if ( $dif_minuto > $ant_minuto ) : 
                  if ( $_DESPAC['ind_defini'] != '1' ) : 
                    $m[$key]['can_'.$act_minuto.'mins'] ++;
                    $m[$key]['all_despac']['can_'.$act_minuto.'mins'] .= $m[$key]['all_despac']['can_'.$act_minuto.'mins'] == NULL ? $_DESPAC['num_despac'] : ', ' . $_DESPAC['num_despac'];
                  endif;
                endif;
              }
              if ( $this -> num_despac && $m[$key]['can_'.$act_minuto.'mins'] > 0 ) {
                $m[$key]['col_alarma'] = $this -> genera_alarma[$h]['cod_colorx'];
              }
            }
          }
        }
      }
      if ( $this -> num_despac ) {
        $m[$key]['fec_progra']  = $fec_progra;
        $m[$key]['dif_minuto']  = $m[$key]['can_sinret'] == 1 ? $dif_minuto * -1 : $dif_minuto;
        $m[$key]['col_alarma']  = $m[$key]['col_alarma'] ? $m[$key]['col_alarma'] : 'FFFFFF';
        unset( $m[$key]['all_despac'] );
      }
    }
	
	/*echo "<pre>";
	print_r($m);
	echo "</pre>";*/
    return $m;
  }
  
 
  
  function setFilters() {
    $query = NULL;
    if ( $_SESSION['datos_usuario']['cod_perfil'] == NULL ) {
      //--------------------------
      //@PARA EL FILTRO DE EMPRESA
      //--------------------------
      $filtro = new Aplica_Filtro_Usuari( 1, COD_FILTRO_EMPTRA, $_SESSION['datos_usuario']['cod_usuari'] );
      if ( $filtro -> listar( $this -> conexion ) ) : 
        $datos_filtro = $filtro -> retornar();
        $query .= " AND b.cod_transp = '".$datos_filtro['clv_filtro']."' ";
      endif;
    }
    else { 
      //--------------------------
      //@PARA EL FILTRO DE EMPRESA
      //--------------------------
      $filtro = new Aplica_Filtro_Perfil( 1, COD_FILTRO_EMPTRA, $_SESSION['datos_usuario']['cod_perfil'] );
      if ( $filtro -> listar( $this -> conexion ) ) : 
        $datos_filtro = $filtro -> retornar();
        $query .= " AND b.cod_transp = '".$datos_filtro['clv_filtro']."' ";
      endif;
    }
    return $query;
  }
  
  
  function VerifyHorario() {
    $mSql = "SELECT 1
               FROM ".BASE_DATOS.".tab_monito_encabe a,
                    ".BASE_DATOS.".tab_monito_detall b
              WHERE a.ind_estado = '1' 
                AND b.ind_estado = '1' 
                AND a.cod_consec = b.cod_consec 
                AND a.fec_inicia <= NOW() 
                AND a.fec_finalx >= NOW() 
                AND a.cod_usuari = '".$_SESSION['datos_usuario']['cod_usuari']."' ";
    $consult = new Consulta( $mSql, $this -> conexion );
    $matriz = $consult -> ret_matrix( 'a' );
    return sizeof( $matriz ) == 0 ? FALSE : TRUE;
  }
}
?>