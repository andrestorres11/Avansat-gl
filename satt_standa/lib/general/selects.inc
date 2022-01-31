<?php
/*
 * @author : Ing. Christiam Barrera
 * @class  : GeneralSelects
 * @desc   : Genera las matrices para desplegar las listas HTML (Tag select)
 */
$_SESSION["HOST_CENTRAL_POLIZA"] = HOST_CENTRAL_POLIZA;
$_SESSION["USR_CENTRAL_POLIZA"] = USR_CENTRAL_POLIZA;
$_SESSION["PWD_CENTRAL_POLIZA"] = PWD_CENTRAL_POLIZA;
$_SESSION["DB_PANEL_CONTROL"] = DB_PANEL_CONTROL;
$_SESSION["DB_ADS_POLIZA"] = DB_ADS_POLIZA;


Class GeneralSelects
{
  var $conexion  = NULL;
  var $cNull     = array();
  var $usuario   = NULL;
  var $codigo    = NULL;

  
  function GeneralSelects( $mConecction, $mUser = NULL, $mCodigo = NULL )
  {
    $this -> conexion = $mConecction;
    $this -> cNull = array( array( "", "--" ) );
    
    $this -> usuario = $mUser;
    $this -> codigo  = $mCodigo;
  }
  

  function Result( $mReturn, $mSql )
  {
    if ( $mReturn == 'query' || $mReturn == 'sql' )
    {
      return $mSql;
    }
    else
    {
      $mSql = new Consulta( $mSql, $this -> conexion );
      return array_merge( $this -> cNull, $mSql -> ret_matriz( "i" ) );
    }
  }

  //------------------------------------------------------------------------------------------------------------
  //@ Metodo que retorna la Matriz de Agencias seg?n los filtros de la Aplicaci?n
  function GetAgencias( $mReturn = 'matrix' )
  {
    $mSql  = "SELECT a.cod_agenci, a.nom_agenci ";
    $mSql .= "FROM ".BASE_DATOS.".tab_genera_agenci a ";
    //$mSql .= "WHERE a.ind_estado = 1 ";
    if ( !$datos_usuario["cod_perfil"] )
    {
      $filtro = new Aplica_Filtro_Usuari( $this -> codigo, COD_FILTRO_AGENCI, $datos_usuario["cod_usuari"] );
      if( $filtro -> listar( $this -> conexion ) )
      {
        $datos_filtro = $filtro -> retornar();
        $mSql .= " AND a.cod_agenci = '".$datos_filtro["clv_filtro"]."' ";
      }
      else
      {
        $mSql .= "ORDER BY 2 ASC";
      }
    }
    else
    {
      $filtro = new Aplica_Filtro_Perfil( $datos_usuario["codigo"], COD_FILTRO_AGENCI, $datos_usuario["cod_perfil"] );
      if( $filtro -> listar( $this -> conexion ) )
      {
        $datos_filtro = $filtro -> retornar();
        $mSql .= " AND a.cod_agenci = '".$datos_filtro["clv_filtro"]."' ";
      }
      else
      {
        $mSql .= "ORDER BY 2 ASC";
      }
    }
    //echo $mSql;
    return $this -> Result( $mReturn, $mSql );
  }

  //------------------------------------------------------------------------------------------------------------------
  function GetAplicaClientes( $mReturn = 'matrix', $mComplement = 'yes' )
  {
    $datos_usuario = $this -> usuario -> retornar();
    $usuario = $datos_usuario["cod_usuari"];
    $mSql = "SELECT g.cod_tercer, g.abr_tercer
               FROM ".BASE_DATOS.".tab_ordenx_cargax a,
                    ".BASE_DATOS.".tab_tercer_tercer g
              WHERE g.cod_tercer = a.cod_client AND
                    a.ind_patcon = 'N' AND
                    a.num_remesa > a.num_remasi AND
                    a.ind_anulad = 'N' ";
    if ( $datos_usuario['cod_perfil'] == '' )
    {
      $filtro = new Aplica_Filtro_Usuari( $this -> cod_aplica, COD_FILTRO_CLIENT, $datos_usuario['cod_usuari'] );
      if ( $filtro -> listar( $this -> conexion ) )
      {
        $datos_filtro = $filtro -> retornar();
        $mSql .= " AND g.cod_tercer = '".$datos_filtro['clv_filtro']."' ";
      }
    }//fin if
    else
    {
      $filtro = new Aplica_Filtro_Perfil( $this -> cod_aplica, COD_FILTRO_CLIENT, $datos_usuario['cod_perfil'] );
      if ( $filtro -> listar( $this -> conexion ) )
      {
        $datos_filtro = $filtro -> retornar();
        $mSql .= " AND g.cod_tercer = '".$datos_filtro['clv_filtro']."' ";
      }
    }//fin else
    if ( $mComplement === 'yes' )
    {
      $mSql .= " GROUP BY 1 ORDER BY 2 ";
    }
    return $this -> Result( $mReturn, $mSql );
  }
  //---------------------------------------------------------------------------------------------------------------------

  function GetClientes( $mReturn = 'matrix' )
  {
    $mSql  = "SELECT a.cod_tercer, IF( b.cod_tipdoc = 'N', b.abr_tercer, CONCAT( b.nom_tercer, ' ', b.nom_apell1 ) ) ";
    $mSql .= "FROM ".BASE_DATOS.".tab_tercer_client a, ".BASE_DATOS.".tab_tercer_tercer b ";
    $mSql .= "WHERE a.cod_tercer = b.cod_tercer ";
    $mSql .= "ORDER BY 2 ASC";
    return $this -> Result( $mReturn, $mSql );
  }


  function GetCiudades( $mReturn = 'matrix', $mValue = 1 )
  {
    if ( $mValue == 1 )
    {
      $mSql  = "SELECT a.cod_ciudad, a.nom_ciudad ";
    }
    else
    {
      $mSql  = "SELECT CONCAT(a.cod_ciudad, '-*-', a.nom_ciudad) AS a.cod_ciudad, a.nom_ciudad ";
    }
    $mSql .= "FROM ".BASE_DATOS.".tab_genera_ciudad a ";
    $mSql .= "WHERE a.ind_estado = 1 ";
    $mSql .= "ORDER BY 2 ASC";
    return $this -> Result( $mReturn, $mSql );
  }


  function GetPlacas( $mReturn = 'matrix' )
  {
    $mSql  = "SELECT a.num_placax, b.nom_marcax, c.nom_lineax, d.nom_colorx, a.ano_modelo, ";
    $mSql .= "e.abr_tercer, g.abr_tercer, CONCAT(h.nom_tercer, ' ', h.nom_apell1) AS nom_tercer, ";
    $mSql .= "a.cod_conduc, i.nom_tipveh, a.cod_propie, a.cod_tenedo, a.num_remolq, a.cod_tipveh ";
    $mSql .= "FROM ".BASE_DATOS.".tab_vehicu_vehicu a, ";
    $mSql .= BASE_DATOS.".tab_genera_marcas b, ";
    $mSql .= BASE_DATOS.".tab_vehige_colore d, ";
    $mSql .= BASE_DATOS.".tab_vehige_lineas c, ";
    $mSql .= BASE_DATOS.".tab_tercer_tercer e, ";
    $mSql .= BASE_DATOS.".tab_tercer_tercer g, ";
    $mSql .= BASE_DATOS.".tab_tercer_tercer h, ";
    $mSql .= BASE_DATOS.".tab_genera_tipveh i ";
    $mSql .= "WHERE a.cod_tenedo = g.cod_tercer AND ";
    $mSql .= "a.cod_conduc = h.cod_tercer AND ";
    $mSql .= "a.cod_propie = e.cod_tercer AND ";
    $mSql .= "a.cod_colorx = d.cod_colorx AND ";
    $mSql .= "a.cod_marcax = c.cod_marcax AND ";
    $mSql .= "a.cod_lineax = c.cod_lineax AND ";
    $mSql .= "a.cod_tipveh = i.cod_tipveh AND ";
    $mSql .= "c.cod_marcax = b.cod_marcax";
    return $this -> Result( $mReturn, $mSql );
  }



  function GetEmpaques( $mReturn = 'matrix' )
  {
    $mSql  = "SELECT a.cod_empaqu, a.nom_empaqu ";
    $mSql .= "FROM ".BASE_DATOS.".tab_genera_empaqu a ";
    $mSql .= "WHERE a.ind_activa = 1 ";
    $mSql .= "ORDER BY 2 ASC";
    return $this -> Result( $mReturn, $mSql );
  }


  function GetMercancias( $mReturn = 'matrix' )
  {
    $mSql  = "SELECT a.cod_mercan, a.abr_mercan ";
    $mSql .= "FROM ".BASE_DATOS.".tab_genera_mercan a ";
    $mSql .= "WHERE a.ind_activa = 1 ";
    $mSql .= "ORDER BY 2 ASC";
    return $this -> Result( $mReturn, $mSql );
  }


  function GetConductores( $mReturn = 'matrix' )
  {
    $mSql  = "SELECT a.cod_tercer, CONCAT(b.nom_tercer, ' ', b.nom_apell1, ' - [ ', a.cod_tercer, ' ]') ";
    $mSql .= "FROM ".BASE_DATOS.".tab_tercer_conduc a, ".BASE_DATOS.".tab_tercer_tercer b ";
    $mSql .= "WHERE a.cod_tercer = b.cod_tercer AND a.fec_venlic >= NOW() AND b.cod_estado = '1' ";
    $mSql .= "ORDER BY 2 ASC";
    return $this -> Result( $mReturn, $mSql );
  }


  function GetRemolques( $mReturn = 'matrix' )
  {
    $mSql  = "SELECT a.num_trayle, a.num_trayle ";
    $mSql .= "FROM ".BASE_DATOS.".tab_vehige_trayle a ";
    $mSql .= "ORDER BY 2 ASC";
    return $this -> Result( $mReturn, $mSql );
  }


  function GetUnidadMedida( $mReturn = 'matrix' )
  {
    $mSql  = "SELECT a.cod_unimed, a.nom_unimed ";
    $mSql .= "FROM ".BASE_DATOS.".tab_genera_unimed a ";
    $mSql .= "ORDER BY 2 ASC";
    return $this -> Result( $mReturn, $mSql );
  }


  function GetNaturaleza( $mReturn = 'matrix' )
  {
    $mSql  = "SELECT a.cod_natemp, a.nom_natemp ";
    $mSql .= "FROM ".BASE_DATOS.".tab_genera_natemp a ";
    $mSql .= "ORDER BY 2 ASC";
    return $this -> Result( $mReturn, $mSql );
  }

  function GetRH( $mReturn = 'matrix' )
  {
    $mSql  = "SELECT a.nom_tiporh, a.nom_tiporh ";
    $mSql .= "FROM ".BD_STANDA.".tab_genera_tiporh  a ";
    $mSql .= "ORDER BY 2 ASC";
    return $this -> Result( $mReturn, $mSql );
  }


  function GetTipoDocumentos( $mReturn = 'matrix' )
  {
    $mSql  = "SELECT a.cod_tipdoc, a.nom_tipdoc  ";
    $mSql .= "FROM ".BASE_DATOS.".tab_genera_tipdoc a ";
    $mSql .= "ORDER BY 2 ASC";
    return $this -> Result( $mReturn, $mSql );
  }


  function GetAseguradoras( $mReturn = 'matrix' )
  {
    $mSql  = "SELECT a.cod_tercer, a.abr_tercer ";
    $mSql .= "FROM ".BASE_DATOS.".tab_tercer_tercer a, ".BASE_DATOS.".tab_tercer_activi b ";
    $mSql .= "WHERE a.cod_tercer = b.cod_tercer AND b.cod_activi = '5' ";
    $mSql .= "ORDER BY 2 ASC";
    return $this -> Result( $mReturn, $mSql );
  }


  function GetMarcas( $mReturn = 'matrix' )
  {
    $mSql  = "SELECT a.cod_marcax, a.nom_marcax ";
    $mSql .= "FROM ".BASE_DATOS.".tab_genera_marcas a ";
    $mSql .= "ORDER BY 2 ASC";
    return $this -> Result( $mReturn, $mSql );
  }


  function GetColores( $mReturn = 'matrix' )
  {
    $mSql  = "SELECT a.cod_colorx, a.nom_colorx ";
    $mSql .= "FROM ".BASE_DATOS.".tab_vehige_colore a ";
    $mSql .= "ORDER BY 2 ASC";
    return $this -> Result( $mReturn, $mSql );
  }


  function GetTipoVinculacion( $mReturn = 'matrix' )
  {
    $mSql  = "SELECT a.cod_tipveh, a.nom_tipveh ";
    $mSql .= "FROM ".BASE_DATOS.".tab_genera_tipveh a ";
    $mSql .= "ORDER BY 2 ASC";
    return $this -> Result( $mReturn, $mSql );
  }


  function GetCarrocerias( $mReturn = 'matrix' )
  {
    $mSql  = "SELECT a.cod_carroc, a.nom_carroc ";
    $mSql .= "FROM ".BASE_DATOS.".tab_vehige_carroc a ";
    $mSql .= "ORDER BY 2 ASC";
    return $this -> Result( $mReturn, $mSql );
  }


  function GetCalificacion( $mReturn = 'matrix' )
  {
    $mSql  = "SELECT a.cod_califi, a.nom_califi ";
    $mSql .= "FROM ".BASE_DATOS.".tab_genera_califi a ";
    $mSql .= "ORDER BY 2 ASC";
    return $this -> Result( $mReturn, $mSql );
  }

  function GetCategorias( $mReturn = 'matrix' )
  {
    $mSql  = "SELECT a.cod_catlic, a.nom_catlic ";
    $mSql .= "FROM ".BASE_DATOS.".tab_genera_catlic a ";
    $mSql .= "ORDER BY 2 ASC";
    return $this -> Result( $mReturn, $mSql );
  }


  function GetTerceros( $mReturn = 'matrix', $mQuery = '' )
  {
    $mSql  = "SELECT a.cod_tercer, b.nom_tipdoc, ";
    $mSql .= "       IF ( a.cod_tipdoc = 'N', a.abr_tercer, CONCAT( a.nom_tercer, ' ', a.nom_apell1 ) ) AS nom_tercer, ";
    $mSql .= "       a.dir_domici, c.nom_ciudad, a.num_telef1, a.cod_ciudad ";
    $mSql .= "FROM ".BASE_DATOS.".tab_tercer_tercer a, ";
    $mSql .= "     ".BASE_DATOS.".tab_genera_tipdoc b, ";
    $mSql .= "     ".BASE_DATOS.".tab_genera_ciudad c ";
    $mSql .= "WHERE a.cod_tipdoc = b.cod_tipdoc AND a.cod_ciudad = c.cod_ciudad ";
    $mSql .= $mQuery;
    return $this -> Result( $mReturn, $mSql );
  }

  function GetRutas( $mReturn = 'matrix' )
  {
    $mSql  = "SELECT a.cod_rutasx, LEFT( a.nom_rutasx, 20 ) AS nom_rutasx ";
    $mSql .= "FROM ".BASE_DATOS.".tab_genera_rutasx a ";
    $mSql .= "ORDER BY 2 ASC";
    return $this -> Result( $mReturn, $mSql );
  }


  //----------------------------------------------------------------------------------------------------------------------
  //@Retorna el Indicador de Rangos. 1 = Nivel Nacional
  function GetDocnal()
  {
    $ind_docnal = "SELECT ind_docnal FROM ".BASE_DATOS.".tab_emptra_config ";
    $ind_docnal = new Consulta( $ind_docnal, $this -> conexion );
    $ind_docnal = $ind_docnal -> ret_matriz( "a" );
    $ind_docnal = $ind_docnal[0]["ind_docnal"];
    return $ind_docnal;
  }

  //----------------------------------------------------------------------------------------------------------------------
  //@Retorna el Porcentaje de Retencin en la Fuente
  function GetRetefu()
  {
    $val_pctret  = "SELECT val_pctret FROM ".BASE_DATOS.".tab_emptra_config ";
    $val_pctret = new Consulta( $val_pctret, $this -> conexion );
    $val_pctret = $val_pctret -> ret_matriz( "a" );
    $val_pctret = $val_pctret[0]["val_pctret"];
    return $val_pctret;
  }

  //----------------------------------------------------------------------------------------------------------------------
  //@Retorna el Numero de Poliza
  function GetPoliza()
  {
    $num_poliza  = "SELECT num_poliza FROM ".BASE_DATOS.".tab_emptra_config ";
    $num_poliza = new Consulta( $num_poliza, $this -> conexion );
    $num_poliza = $num_poliza -> ret_matriz( "a" );
    $num_poliza = $num_poliza[0]["num_poliza"];
    return $num_poliza;
  }
  
  //----------------------------------------------------------------------------------------------------------------------
  //@Retorna el Porcentaje de Retencin en la Fuente
  function GetObsManifi()
  {
    $obs_manifi  = "SELECT obs_manifi FROM ".BASE_DATOS.".tab_emptra_config ";
    $obs_manifi = new Consulta( $obs_manifi, $this -> conexion );
    $obs_manifi = $obs_manifi -> ret_matriz( "a" );
    $obs_manifi = $obs_manifi[0]["obs_manifi"];
    return $obs_manifi;
  }
  //----------------------------------------------------------------------------------------------------------------------
  //@SELECTS PARA EL MODULO DE MANIFIESTO
  function GetConfigManifi( $mAgencia = '1' )
  {
    if ( $this -> GetDocnal() == '1' )
    {
      $mAgencia = '1';
    }
    //$mAgencia = '18';
    $mConf  = "SELECT a.ran_iniman, a.ran_finman, a.ran_actman, a.ind_manman, a.cod_agenci, b.nom_agenci ";
    $mConf .= "FROM tab_agenci_randoc a, tab_genera_agenci b ";
    $mConf .= "WHERE a.cod_agenci = '".$mAgencia."' ";
    $mConf  = new Consulta( $mConf, $this -> conexion );
    $mConf  = $mConf -> ret_matriz( "a" );
    return $mConf[0];
  }
  
  //----------------------------------------------------------------------------------------------------------------------
  //@SELECTS PARA EL MODULO DE REMESA
  //----------------------------------------------------------------------------------------------------------------------
  //@RETORNA LA MATRIZ DE DATOS DE LA ORDEN DE CARGUE
  function GetRemesaOrdcar( $mClient = NULL, $mOrdcar = NULL )
  {
    $mSql = "SELECT a.cod_ordcar, a.cod_client, 
                    a.nom_remite, a.dir_remite, a.tel_remite, a.cod_ciudad AS ciu_remite, 
                    b.nom_destin, b.dir_destin, b.tel_destin, b.cod_ciudad AS ciu_destin,
                    a.num_remesa, c.cod_agenci, e.nom_agenci, 
                    c.cod_ciuori, f.nom_ciudad AS nom_ciuori, 
                    c.cod_ciudes, g.nom_ciudad AS nom_ciudes 
               FROM ".BASE_DATOS.".tab_ordenx_cargax a, 
                    ".BASE_DATOS.".tab_ordcar_destin b, 
                    ".BASE_DATOS.".tab_requis_cargax c, 
                    ".BASE_DATOS.".tab_relaci_reqord d,
                    ".BASE_DATOS.".tab_genera_agenci e,
                    ".BASE_DATOS.".tab_genera_ciudad f, 
                    ".BASE_DATOS.".tab_genera_ciudad g  
              WHERE a.cod_ordcar = b.cod_ordcar AND
                    a.cod_ordcar = d.cod_ordcar AND
                    c.cod_reqcar = d.cod_reqcar AND
                    c.cod_agenci = e.cod_agenci AND
                    c.cod_agenci = e.cod_agenci AND
                    c.cod_ciuori = f.cod_ciudad AND
                    c.cod_ciudes = g.cod_ciudad ";
    
    $mSql .= $mClient ? " AND a.cod_client = '".$mClient."' " : NULL;
    $mSql .= $mOrdcar ? " AND a.cod_ordcar = '".$mOrdcar."' " : NULL;
    
    return $mSql;
  }
  
  
  
  function GetConfigRemesa( $mAgencia = '1' )
  {
    if ( $this -> GetDocnal() == '1' )
    {
      $mAgencia = '1';
    }
    //$mAgencia = '18';
    $mConf  = "SELECT a.ind_manrem, a.ran_inirem, a.ran_actrem, a.ran_finrem, a.cod_agenci, b.nom_agenci 
                 FROM ".BASE_DATOS.".tab_agenci_randoc a, ".BASE_DATOS.".tab_genera_agenci b 
                WHERE a.cod_agenci = '".$mAgencia."' ";
    $mConf  = new Consulta( $mConf, $this -> conexion );
    $mConf  = $mConf -> ret_matriz( "a" );
    return $mConf[0];
  }

  //---------------------------------------------------------------------------------------------------------------------
  function GetManifiRemesas( $mSource, $mForm = NULL, $cod_tipveh = NULL )
  {
    if ( $mForm == NULL )
    {
      $mSql  = "SELECT 
                       a.cod_remesa , DATE(a.fec_emisix) AS fec_emisix, c.nom_agenci ,
                       a.can_cargax , a.pes_cargax , f.abr_mercan , 
                       b.nom_ciudad , d.nom_ciudad , a.nom_remite , 
                       a.nom_destin , g.abr_tercer , h.num_placax , 
                       a.cod_client , b.cod_ciudad AS origen , d.cod_ciudad AS destino , 
                       c.cod_agenci , a.cod_mercan , a.cod_tomseg , 
                       a.cod_agenci 
                  FROM 
                       ".BASE_DATOS.".tab_remesa_oguiax a LEFT JOIN 
                       ".BASE_DATOS.".tab_tercer_tercer g ON a.cod_tomseg = g.cod_tercer LEFT JOIN 
                       ".BASE_DATOS.".tab_ordenx_cargax h ON a.cod_ordcar = h.cod_ordcar, 
                       ".BASE_DATOS.".tab_genera_mercan f, 
                       ".BASE_DATOS.".tab_genera_ciudad b, 
                       ".BASE_DATOS.".tab_genera_agenci c, 
                       ".BASE_DATOS.".tab_genera_ciudad d   
                 WHERE 
                       a.cod_ciuori = b.cod_ciudad 
                       AND a.cod_agenci = c.cod_agenci 
                       AND a.cod_ciudes = d.cod_ciudad 
                       AND a.cod_mercan = f.cod_mercan 
                       AND a.ind_anulad = 'R' AND a.ind_asigna = 'N' 
                       AND a.cod_ciuori = '".$mSource."' GROUP BY 1";
    }
    else
    {
       $mSql  = "SELECT 
                        a.cod_remesa , DATE_FORMAT( a.fec_emisix, '%Y-%m-%d' ) AS fec_emisix , a.can_cargax , 
                        a.pes_cargax , a.val_declar , e.abr_mercan , 
                        g.nom_uniser , d.nom_ciudad AS nom_ciudes , a.nom_destin , 
                        a.nom_remite , a.cod_tomseg , a.cod_uniser , 
                        c.nom_ciudad AS nom_ciuori , i.val_maxcom AS val_univen , a.cod_reqcar , 
                        a.ind_remcet , a.ind_remcon , a.val_remesa 
                   FROM 
                        ".BASE_DATOS.".tab_remesa_oguiax a, 
                        ".BASE_DATOS.".tab_genera_ciudad c, 
                        ".BASE_DATOS.".tab_genera_ciudad d, 
                        ".BASE_DATOS.".tab_genera_mercan e, 
                        ".BASE_DATOS.".tab_tercer_tercer f, 
                        ".BASE_DATOS.".tab_genera_uniser g, 
                        ".BASE_DATOS.".tab_requis_cargax h LEFT JOIN 
                        ".BASE_DATOS.".tab_lispre_tipvin i ON h.cod_lispre = i.cod_lispre 
                                                          AND i.cod_tipveh = '".$cod_tipveh."' 
                  WHERE 
                        a.cod_ciuori = c.cod_ciudad 
                        AND a.cod_ciudes = d.cod_ciudad 
                        AND a.cod_mercan = e.cod_mercan 
                        AND a.cod_uniser = g.cod_uniser 
                        AND a.cod_reqcar = h.cod_reqcar 
                        AND a.ind_anulad = 'R' 
                        AND a.cod_remesa IN ( ".$mSource." ) GROUP BY 1";
       //echo "<hr />".$mSql;
    }
    return $mSql;
  }


  function GetManifiOrigenes()
  {
    $mSql  = "SELECT a.cod_ciudad, a.abr_ciudad ";
    $mSql .= "FROM ".BASE_DATOS.".tab_genera_ciudad a, ".BASE_DATOS.".tab_remesa_oguiax b ";
    $mSql .= "WHERE a.cod_ciudad = b.cod_ciuori AND b.ind_asigna = 'N' AND b.ind_anulad = 'R' ";
    $mSql .= "GROUP BY 1 ORDER BY 2";
    $mSql = new Consulta( $mSql, $this -> conexion );
    $mSql = $mSql -> ret_matriz( "i" );

    if ( sizeof( $mSql ) == 0 )
    {
      //$this -> cOrigenes = $this -> cNull;
      return $this -> cNull;
    }
    else
    {
      //$this -> cOrigenes = array_merge( $this -> cNull, $mSql );
      return array_merge( $this -> cNull, $mSql );
    }
  }


  function GetManifiCiuori( $mCity )
  {
    $mSql  = "SELECT a.cod_ciudad, a.abr_ciudad, a.val_icaxxx ";
    $mSql .= "FROM ".BASE_DATOS.".tab_genera_ciudad a ";
    $mSql .= "WHERE a.cod_ciudad = '".$mCity."'";
    $mSql = new Consulta( $mSql, $this -> conexion );
    $mSql = $mSql -> ret_matriz( "i" );
    //$this -> cCiuori = $mSql;
    return $mSql;
  }


  function GetManifiAgencias( $mCity = '' )
  {
    $mSql  = "SELECT a.cod_agenci, a.nom_agenci ";
    $mSql .= "FROM ".BASE_DATOS.".tab_genera_agenci a, ".BASE_DATOS.".tab_remesa_oguiax b, ".BASE_DATOS.".tab_agenci_randoc c ";
    $mSql .= "WHERE a.cod_agenci = b.cod_agenci AND b.ind_asigna = 'N' AND b.ind_anulad = 'R' AND ";
    $mSql .= "      a.cod_agenci = c.cod_agenci ";
    if ( !$mCity )
    {
      $mSql .= "AND b.cod_ciuori = '".$mCity."' ";
    }
    $mSql .= " GROUP BY 1 ORDER BY 2";
    $mSql = new Consulta( $mSql, $this -> conexion );
    $mSql = $mSql -> ret_matriz( "i" );
    //$this -> cAgencias = array_merge( $this -> cNull, $mSql );
    return array_merge( $this -> cNull, $mSql );
  }



  function GetManifiDestinos( $mCity, $mForm = 'list' )
  {
    if ( $mForm == 'list' )
    {
      $mSql  = "SELECT a.cod_ciudad, a.abr_ciudad ";
      $mSql .= "FROM ".BASE_DATOS.".tab_genera_ciudad a, ".BASE_DATOS.".tab_remesa_oguiax b ";
      $mSql .= "WHERE a.cod_ciudad = b.cod_ciudes AND b.ind_asigna = 'N' AND b.ind_anulad = 'R' AND b.cod_ciuori = '".$mCity."' ";
      $mSql .= "GROUP BY 1 ORDER BY 2";
      $mSql = new Consulta( $mSql, $this -> conexion );
      $mSql = $mSql -> ret_matriz( "i" );
      //$this -> cDestinos = array_merge( $this -> cNull, $mSql );
      return array_merge( $this -> cNull, $mSql );
    }
    else
    {
      $mSql  = "SELECT a.cod_ciudad, a.abr_ciudad ";
      $mSql .= "FROM ".BASE_DATOS.".tab_genera_ciudad a, ".BASE_DATOS.".tab_remesa_oguiax b ";
      $mSql .= "WHERE a.cod_ciudad = b.cod_ciudes AND b.cod_remesa IN ( ".$mCity." ) ";
      $mSql .= "GROUP BY 1 ORDER BY 2";
      $mSql = new Consulta( $mSql, $this -> conexion );
      $mSql = $mSql -> ret_matriz( "i" );
      //$this -> cDestinos = array_merge( $this -> cNull, $mSql );
      return array_merge( $this -> cNull, $mSql );
    }
  }


  function GetManifiMercancias( $mCity )
  {
    $mSql  = "SELECT a.cod_mercan, a.abr_mercan ";
    $mSql .= "FROM ".BASE_DATOS.".tab_genera_mercan a, ".BASE_DATOS.".tab_remesa_oguiax b ";
    $mSql .= "WHERE a.cod_mercan = b.cod_mercan AND b.ind_asigna = 'N' AND b.ind_anulad = 'R' AND b.cod_ciuori = '".$mCity."' ";
    $mSql .= "GROUP BY 1 ORDER BY 2";
    //echo "<hr />".$mSql."<hr />";
    $mSql = new Consulta( $mSql, $this -> conexion );
    $mSql = $mSql -> ret_matriz( "i" );
    //$this -> cMercancias = array_merge( $this -> cNull, $mSql );
    return array_merge( $this -> cNull, $mSql );
  }



  //---------------------------------------------------------------------------------------------------
  //@RETORNA EL QUERY DE VEHICULO SEGUN ESTE O NO ASIGNADO A ORDENES DE CARGUE.
  function GetVehiculo( $num_placax, $cod_remesa, $fIndex = NULL )
  {
    //if ( $this -> InOrdenCargax( $num_placax ) || $fIndex == 1 )
    if ( $this -> InRemesaOrdenCargax( $num_placax, $cod_remesa ) || $fIndex == 1 )
    {
      $mSql  = "SELECT ";
      $mSql .= "  a.num_placax, b.nom_marcax, c.nom_lineax, a.ano_modelo, a.ano_repote, a.num_config, d.nom_colorx, k.nom_carroc, ";
      $mSql .= "  m.nom_tipveh, h.num_trayle, ";
      $mSql .= "  DATE_FORMAT( a.fec_vigfin, '%Y-%m-%d' ) AS fec_vigfin, ";
      $mSql .= "  CONCAT( e.nom_tercer, ' ', e.nom_apell1 ) AS nom_propie, h.cod_propie, ";
      $mSql .= "  CONCAT( f.nom_tercer, ' ', f.nom_apell1 ) AS nom_tenedo, h.cod_tenedo, ";
      $mSql .= "  CONCAT( g.nom_tercer, ' ', g.nom_apell1 ) AS nom_conduc, h.cod_conduc, ";
      $mSql .= "  DATE_FORMAT( j.fec_venlic, '%Y-%m-%d' ) AS fec_venlic, l.val_pesmax, ";
      $mSql .= "  a.cod_marcax, a.cod_lineax, a.cod_colorx, a.cod_carroc, a.cod_tipveh, a.nom_vincul, i.cod_ordcar, i.cod_remesa ";
      $mSql .= "FROM ";
      $mSql .= "  ".BASE_DATOS.".tab_vehicu_vehicu a, ";
      $mSql .= "  ".BASE_DATOS.".tab_genera_marcas b, ";
      $mSql .= "  ".BASE_DATOS.".tab_vehige_lineas c, ";
      $mSql .= "  ".BASE_DATOS.".tab_vehige_colore d, ";
      $mSql .= "  ".BASE_DATOS.".tab_tercer_tercer e, ";
      $mSql .= "  ".BASE_DATOS.".tab_tercer_tercer f, ";
      $mSql .= "  ".BASE_DATOS.".tab_tercer_tercer g, ";
      $mSql .= "  ".BASE_DATOS.".tab_ordenx_cargax h, ";
      if ( $mType != 'list' )
      $mSql .= "  ".BASE_DATOS.".tab_remesa_oguiax i, ";
      $mSql .= "  ".BASE_DATOS.".tab_tercer_conduc j, ";
      $mSql .= "  ".BASE_DATOS.".tab_vehige_carroc k, ";
      $mSql .= "  ".BASE_DATOS.".tab_vehige_config l, ";
      $mSql .= "  ".BASE_DATOS.".tab_genera_tipveh m  ";
      $mSql .= "WHERE ";
      $mSql .= "  a.cod_colorx = d.cod_colorx AND ";
      $mSql .= "  a.cod_lineax = c.cod_lineax AND ";
      $mSql .= "  a.cod_marcax = b.cod_marcax AND ";
      $mSql .= "  b.cod_marcax = c.cod_marcax AND ";
      $mSql .= "  h.cod_propie = e.cod_tercer AND ";
      $mSql .= "  h.cod_tenedo = f.cod_tercer AND ";
      $mSql .= "  h.cod_conduc = g.cod_tercer AND ";
      $mSql .= "  a.num_placax = h.num_placax AND ";
      if ( $mType != 'list' )
      $mSql .= "  h.cod_ordcar = i.cod_ordcar AND ";
      $mSql .= "  h.cod_conduc = j.cod_tercer AND ";
      $mSql .= "  a.cod_carroc = k.cod_carroc AND ";
      $mSql .= "  a.num_config = l.num_config AND ";
      $mSql .= "  a.cod_tipveh = m.cod_tipveh AND ";
      $mSql .= "  a.fec_vigfin >= '".date( "Y-m-d" )."' AND ";
      $mSql .= "  a.ind_aproba = '1' AND ";
    }
    else
    {
      $mSql  .= "SELECT a.num_placax, b.nom_marcax, c.nom_lineax, a.ano_modelo, a.ano_repote, 
                        a.num_config, d.nom_colorx, k.nom_carroc, m.nom_tipveh, n.num_trayle, 
                        DATE_FORMAT( a.fec_vigfin, '%Y-%m-%d' ) AS fec_vigfin, 
                        CONCAT(e.nom_tercer, ' ', e.nom_apell1) AS nom_propie, a.cod_propie, 
                        CONCAT(f.nom_tercer, ' ', f.nom_apell1) AS nom_tenedo, a.cod_tenedo, 
                        CONCAT(g.nom_tercer, ' ', g.nom_apell1) AS nom_conduc, a.cod_conduc, 
                        DATE_FORMAT( j.fec_venlic, '%Y-%m-%d' ) AS fec_venlic, l.val_pesmax, 
                        a.cod_marcax, a.cod_lineax, a.cod_colorx, a.cod_carroc, a.cod_tipveh, a.nom_vincul 
                   FROM 
                        ".BASE_DATOS.".tab_vehicu_vehicu a LEFT JOIN 
                        ".BASE_DATOS.".tab_trayle_placas n ON a.num_placax = n.num_placax 
                                                          AND n.num_noveda = ( SELECT MAX( num_noveda ) 
                                                                                 FROM ".BASE_DATOS.".tab_trayle_placas 
                                                                                WHERE num_placax = a.num_placax ), 
                        ".BASE_DATOS.".tab_genera_marcas b, 
                        ".BASE_DATOS.".tab_vehige_lineas c, 
                        ".BASE_DATOS.".tab_vehige_colore d, 
                        ".BASE_DATOS.".tab_tercer_tercer e, 
                        ".BASE_DATOS.".tab_tercer_tercer f, 
                        ".BASE_DATOS.".tab_tercer_tercer g, 
                        ".BASE_DATOS.".tab_tercer_conduc j, 
                        ".BASE_DATOS.".tab_vehige_carroc k, 
                        ".BASE_DATOS.".tab_vehige_config l, 
                        ".BASE_DATOS.".tab_genera_tipveh m 
                  WHERE 
                        a.cod_colorx = d.cod_colorx AND 
                        a.cod_lineax = c.cod_lineax AND 
                        a.cod_marcax = b.cod_marcax AND 
                        b.cod_marcax = c.cod_marcax AND 
                        a.cod_propie = e.cod_tercer AND 
                        a.cod_tenedo = f.cod_tercer AND 
                        a.cod_conduc = j.cod_tercer AND 
                        a.cod_conduc = g.cod_tercer AND 
                        a.cod_carroc = k.cod_carroc AND 
                        a.num_config = l.num_config AND 
                        a.cod_tipveh = m.cod_tipveh AND 
                        a.fec_vigfin >= '".date( "Y-m-d" )."' AND 
                        a.ind_aproba = '1' AND ";
    }
    return $mSql;
  }


  //---------------------------------------------------------------------------------------------------
  //@RETORNA EL QUERY O LA MATRIZ DE DATOS DEL VEHICULO O VEHICULOS SEGUN SEA EL CASO EN MANIFIESTO
  function GetManifiVehiculo( $mItem = NULL, $mType = 'remesas', $mRemesa = NULL )
  {
    switch( strtolower( $mType ) )
    {
      case 'remesas' :
        $mSql  = $this -> GetVehiculo( '', $mRemesa, 1 );
        $mSql .= "  i.cod_remesa IN (".$mItem.") ";
        $mSql .= "GROUP BY 1 ";
        $mSql = new Consulta( $mSql, $this -> conexion );
        $mSql = $mSql -> ret_matriz( "a" );
      break;
      case 'placa' :
        $mSql  = $this -> GetVehiculo( $mItem, $mRemesa );
        $mSql .= "  a.num_placax = '".$mItem."' ";
        $mSql .= $mRemesa && strstr( $mSql, 'tab_remesa_oguiax i' ) ? " AND i.cod_remesa = '".str_replace( "'", "", $mRemesa )."' " : NULL;
        $mSql .= " GROUP BY 1 ";
        
        //echo "<hr />".$mSql;
        
        $mSql = new Consulta( $mSql, $this -> conexion );
        $mSql = $mSql -> ret_matriz( "a" );
        
        /*
        $mQuery = $mSql;
        
        
        
        $mSql = new Consulta( $mSql, $this -> conexion );
        $mSql = $mSql -> ret_matriz( "a" );
        
        if ( sizeof( $mSql ) == 0 )
        {
          return $this -> GetManifiVehiculo( $mItem, 'placa', NULL );
        }
        */
      break;
      default :
        $mSql  = $this -> GetVehiculo( 'ZZZXXX', NULL, NULL );
        $mSql .= "  1 = 1 ";
        $mSql .= "GROUP BY 1 ";
        //echo "<hr />".$mSql;
      break;
    }
    return $mSql;

  }


  
  function InOrdenCargax( $num_placax )
  {
    $mSql = "SELECT a.cod_ordcar FROM ".BASE_DATOS.".tab_ordenx_cargax a WHERE a.num_placax = '$num_placax' ";
    $mSql = new Consulta( $mSql, $this -> conexion );
    $mSql = $mSql -> ret_matriz( "a" );
    return sizeof( $mSql ) == 0 ? false : true;
  }
  
  
  
  function InRemesaOrdenCargax( $num_placax, $cod_remesa )
  {
    $mSql = "SELECT a.cod_ordcar, b.cod_remesa 
               FROM ".BASE_DATOS.".tab_ordenx_cargax a, 
                    ".BASE_DATOS.".tab_remesa_oguiax b 
              WHERE a.cod_ordcar = b.cod_ordcar 
                AND a.num_placax = '$num_placax'
                AND b.cod_remesa = '".str_replace( "'", "", $cod_remesa )."' ";
    $mSql = new Consulta( $mSql, $this -> conexion );
    $mSql = $mSql -> ret_matriz( "a" );
    return sizeof( $mSql ) == 0 ? false : true;
  }











  function GetManifiVehiculo2( $mItem = NULL, $mType = 'remesas' )
  {

  
  
  $mSql  = "SELECT ";

    $mSql .= "  a.num_placax, b.nom_marcax, c.nom_lineax, a.ano_modelo, a.ano_repote, a.num_config, d.nom_colorx, k.nom_carroc, ";
    $mSql .= "  m.nom_tipveh, h.num_trayle, ";
    $mSql .= "  DATE_FORMAT( a.fec_vigfin, '%Y-%m-%d' ) AS fec_vigfin, ";
    $mSql .= "  CONCAT(e.nom_tercer, ' ', e.nom_apell1) AS nom_propie, h.cod_propie, ";
    $mSql .= "  CONCAT(f.nom_tercer, ' ', f.nom_apell1) AS nom_tenedo, h.cod_tenedo, ";
    $mSql .= "  CONCAT(g.nom_tercer, ' ', g.nom_apell1) AS nom_conduc, h.cod_conduc, ";
    $mSql .= "  DATE_FORMAT( j.fec_venlic, '%Y-%m-%d' ) AS fec_venlic, l.val_pesmax, ";
    $mSql .= "  a.cod_marcax, a.cod_lineax, a.cod_colorx, a.cod_carroc, a.cod_tipveh, a.nom_vincul ";
    $mSql .= "FROM ";
    $mSql .= "  ".BASE_DATOS.".tab_vehicu_vehicu a, ";
    $mSql .= "  ".BASE_DATOS.".tab_genera_marcas b, ";
    $mSql .= "  ".BASE_DATOS.".tab_vehige_lineas c, ";
    $mSql .= "  ".BASE_DATOS.".tab_vehige_colore d, ";
    $mSql .= "  ".BASE_DATOS.".tab_tercer_tercer e, ";
    $mSql .= "  ".BASE_DATOS.".tab_tercer_tercer f, ";
    $mSql .= "  ".BASE_DATOS.".tab_tercer_tercer g, ";
    $mSql .= "  ".BASE_DATOS.".tab_ordenx_cargax h, ";
    if ( $mType != 'list' )
    $mSql .= "  ".BASE_DATOS.".tab_remesa_oguiax i, ";
    $mSql .= "  ".BASE_DATOS.".tab_tercer_conduc j, ";
    $mSql .= "  ".BASE_DATOS.".tab_vehige_carroc k, ";
    $mSql .= "  ".BASE_DATOS.".tab_vehige_config l, ";
    $mSql .= "  ".BASE_DATOS.".tab_genera_tipveh m ";
    $mSql .= "WHERE ";
    $mSql .= "  a.cod_colorx = d.cod_colorx AND ";
    $mSql .= "  a.cod_lineax = c.cod_lineax AND ";
    $mSql .= "  a.cod_marcax = b.cod_marcax AND ";
    $mSql .= "  b.cod_marcax = c.cod_marcax AND ";
    $mSql .= "  h.cod_propie = e.cod_tercer AND ";
    $mSql .= "  h.cod_tenedo = f.cod_tercer AND ";
    $mSql .= "  h.cod_conduc = g.cod_tercer AND ";
    $mSql .= "  a.num_placax = h.num_placax AND ";
    if ( $mType != 'list' )
    $mSql .= "  h.cod_ordcar = i.cod_ordcar AND ";
    $mSql .= "  h.cod_conduc = j.cod_tercer AND ";
    $mSql .= "  a.cod_carroc = k.cod_carroc AND ";
    $mSql .= "  a.num_config = l.num_config AND ";
    $mSql .= "  a.cod_tipveh = m.cod_tipveh AND ";
    $mSql .= "  a.ind_aproba = '1' AND ";

    switch( strtolower( $mType ) )
    {
      case 'remesas' :
        $mSql .= "  i.cod_remesa IN (".$mItem.") ";
        $mSql .= "GROUP BY 1 ";
        $mSql = new Consulta( $mSql, $this -> conexion );
        $mSql = $mSql -> ret_matriz( "a" );
        break;
      case 'placa' :
        $mSql .= "  a.num_placax = '".$mItem."' ";
        $mSql .= "GROUP BY 1 ";
        $mSql = new Consulta( $mSql, $this -> conexion );
        $mSql = $mSql -> ret_matriz( "a" );
        break;
      default :
        $mSql .= "  1 = 1 ";
        $mSql .= "GROUP BY 1 ";
        break;
    }
     
    /*
     $mSql .= "GROUP BY 1 ";
     //echo "<hr />$mSql<hr />";
     $mSql = new Consulta( $mSql, $this -> conexion );
     $mSql = $mSql -> ret_matriz( "a" );
     */
    return $mSql;

  }


  function GetManifiConductores()
  {
    $mSql  = "SELECT a.cod_tercer, CONCAT(b.nom_tercer, ' ', b.nom_apell1, ' - [ ', a.cod_tercer, ' ]') ";
    $mSql .= "FROM ".BASE_DATOS.".tab_tercer_conduc a, ".BASE_DATOS.".tab_tercer_tercer b ";
    $mSql .= "WHERE a.cod_tercer = b.cod_tercer AND a.fec_venlic >= NOW() AND b.cod_estado = '1' ";
    $mSql .= "ORDER BY 2 ASC";
    $mSql = new Consulta( $mSql, $this -> conexion );
    return array_merge( $this -> cNull, $mSql -> ret_matriz( "i" ) );
  }


  function GetAutorization( $cod_autori, $cod_perfil )
  {
    $mSql  = "SELECT cod_autori, cod_perfil, val_minimo, val_maximo ";
    $mSql .= "FROM ".BASE_DATOS.".tab_autori_perfil ";
    $mSql .= "WHERE cod_autori = '".$cod_autori."' AND cod_perfil = '".$cod_perfil."' ";
    
    //echo "<hr />$mSql<hr />";

    $mSql = new Consulta( $mSql, $this -> conexion );

    return $mSql -> ret_matriz( "a" );
  }


  function GetPercentAntici( $cod_tipveh, $cod_remesa )
  {
    //---------------------------------------------------------------------------------------
    //@RETORNA EL MAXIMO DE ANTICIPO SEGUN TIPO DE VINCULACION Y LA TARIFA DE LA REMESA
    $mSql  = "SELECT c.pct_maxant ";
    $mSql .= "FROM ".BASE_DATOS.".tab_remesa_oguiax a, ";
    $mSql .=         BASE_DATOS.".tab_requis_cargax b, ";
    $mSql .=         BASE_DATOS.".tab_lispre_tipvin c  ";
    $mSql .= "WHERE  a.cod_reqcar = b.cod_reqcar AND ";
    $mSql .= "       b.cod_lispre = c.cod_lispre AND ";
    $mSql .= "       a.cod_remesa = '".$cod_remesa."' AND ";
    $mSql .= "       c.cod_tipveh = '".$cod_tipveh."' ";
    $mSql = new Consulta( $mSql, $this -> conexion );
    $mSql = $mSql -> ret_matriz( "a" );
    $pct_maxant = sizeof( $mSql ) == 0 ? '0' : $mSql[0]["pct_maxant"];
    //---------------------------------------------------------------------------------------
    //@RETORNA EL MAXIMO DE ANTICIPO PARA PROPIO Y TERCEROS A NIVEL GENERAL
    $mSql = "SELECT max_antpro, max_antotr FROM ".BASE_DATOS.".tab_emptra_config ";
    $mSql = new Consulta( $mSql, $this -> conexion );
    $mSql = $mSql -> ret_matriz( "a" );
    $mSql = $mSql[0];

    $max_antpro = $mSql["max_antpro"];
    $max_antotr = $mSql["max_antotr"];


    //$max_antpro = NULL;
    //$max_antotr = NULL;
    //$pct_maxant = 0;


    if ( $cod_tipveh == '1' || $cod_tipveh == '7' )
    {
      if ( NULL == $max_antpro || '0' == $max_antpro )
      //if ( NULL == $max_antpro )
      {
        return $pct_maxant;
      }
      else
      {
        return $max_antpro;
      }
    }
    else
    {
      //if ( NULL == $max_antotr )
      if ( NULL == $max_antotr || '0' == $max_antotr )
      {
        return $pct_maxant;
      }
      else
      {
        return $max_antotr;
      }
    }
  }
  
  //----------------------------------------------------------------------------------------
  //@TRAE EL LISTADO DE RUTAS DEL MINISTERIO
  function GetRutasMinist( $mReturn = 'matrix', $cod_minfle = NULL )
  {
    $mSql  = "SELECT a.cod_minfle, b.nom_ciudad AS nom_ciuori, c.nom_ciudad AS nom_ciudes, 
                     a.val_tonelx, a.cod_ciuori, a.cod_ciudes 
                FROM ".BD_STANDA.".tab_minist_fletex a, 
                     ".BASE_DATOS.".tab_genera_ciudad b,  
                     ".BASE_DATOS.".tab_genera_ciudad c
               WHERE a.ind_estado = '1' 
                 AND a.cod_minfle <> '-1'  
                 AND a.cod_ciuori = b.cod_ciudad 
                 AND a.cod_ciudes = c.cod_ciudad ";
    $mSql .= $cod_minfle ? " AND a.cod_minfle = '".$cod_minfle."' " : NULL;
    
    //echo $mSql;
    return $this -> Result( $mReturn, $mSql );
  }
  //----------------------------------------------------------------------------------------
  //@TRAE EL LISTADO DE RUTAS DEL MINISTERIO
  function VerifyMinfle( $cod_ciuori, $cod_ciudes )
  {
    $mSql  = "SELECT a.cod_minfle 
                FROM ".BD_STANDA.".tab_minist_fletex a 
               WHERE a.ind_estado = '1' 
                 AND a.cod_ciuori = '".$cod_ciuori."'  
                 AND a.cod_ciudes = '".$cod_ciudes."' ";
    $mSql = new Consulta( $mSql, $this -> conexion );
    $mSql = $mSql -> ret_matriz( "a" );
    return sizeof( $mSql ) == 0 ? false : true; 
  }
  
  //---------------------------------------------------------------------------------------
  //@RETORNA LOS DEPARTAMENTOS
  function GetDepartamentos( $cod_pais = '3' )
  {
    $mSql = "SELECT a.cod_depart, a.nom_depart
               FROM ".BASE_DATOS.".tab_genera_depart a 
              WHERE a.cod_paisxx = '".$cod_pais."' GROUP BY 1";
    $mSql = new Consulta( $mSql, $this -> conexion );
    return array_merge( $this -> cNull, $mSql -> ret_matriz( "i" ) );
  }

  function GetOperadoresGPS( $mReturn = 'matrix' )
  {
    $mSql  = "SELECT a.cod_operad, a.nom_operad 
                FROM ".BD_STANDA.".tab_genera_opegps a, 
                     ".BASE_DATOS.".tab_interf_gps b 
                WHERE a.cod_operad = b.cod_operad
                  AND a.ind_estado = '1'
                ORDER BY 2 ASC ";
    return $this -> Result( $mReturn, $mSql );
  }

}
?>