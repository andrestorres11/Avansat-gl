<?php 
/*! \file: inp_noveda_despac.php
 *  \brief: Importa novedades por placa
 *  \author: 
 *  \author: 
 *  \version: 1.0
 *  \date: dd/mm/aaaa
 *  \bug: 
 *  \warning: 
 */

class ImportNovedad
{
  var $conexion,
      $cod_aplica,
      $usuario,
      $cNitCorona = '860068121'; #Nit Corona
  
  var $field   = array();
  var $error   = array();  
  var $row     = array();
  
  function __construct( $co, $us, $ca )
  {
    $this -> conexion = $co;
    $this -> usuario = $us;
    $this -> cod_aplica = $ca;
    $this -> principal();
  }
  
  function principal()
  {
    switch( $_REQUEST['opcion'] )
    {
      case 99:
        $this -> preValidator( $_REQUEST );
      break;
      
      default:
        $this -> MainForm( $_REQUEST );
      break;
    }
  }
  
  function preValidator( $mData )
  {
    $_DATA = $this -> GetFileData();
    $_INFO = $this -> VerifyData( $mData, $_DATA );
    $size_info = sizeof( $_INFO );
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";

    $formulario = new Formulario ( "index.php", "post", "IMPORTAR NOVEDADES", "form\" enctype=\"multipart/form-data\" id=\"formID" );
    
    $mHtml = '</tr><tr><td><center>';
    $mHtml .= '<div class="StyleDIV" style="padding-bottom:15px;" align="center">';
 
    if( $size_info > 0 )
    {
      $mHtml .= '<table width="60%" cellpadding="0" cellspacing="1">';
        $mHtml .= '<tr>';
          $mHtml .= '<td colspan="5" style="padding:15px;font-family:Trebuchet MS, Verdana, Arial;font-size:13px;">Las Novedades no fueron Insertadas Debido a los Siguientes Errores:</td>';
        $mHtml .= '</tr>';
        
        $mHtml .= '<tr>';
          $mHtml .= '<td class="CellHead" align="center">No.</td>';
          $mHtml .= '<td class="CellHead" align="center">L&Iacute;NEA</td>';
          $mHtml .= '<td class="CellHead" align="center">COLUMNA</td>';
          $mHtml .= '<td class="CellHead" align="center">VALOR</td>';
          $mHtml .= '<td class="CellHead" align="center">OBSERVACI&Oacute;N</td>';
        $mHtml .= '</tr>';
        
        for( $j = 0; $j < $size_info; $j++ )
        {      
          $class = $j % 2 == 0 ? 'cellInfo1' : 'cellInfo2';
          $mHtml .= '<tr>';
            $mHtml .= '<td class="'.$class.'" align="center"><b>'.( $j + 1 ).'</b></td>';
            $mHtml .= '<td class="'.$class.'" align="center">'.$_INFO[$j][0].'</td>';
            $mHtml .= '<td class="'.$class.'" align="center">'.$_INFO[$j][1].'</td>';
            $valor = $_INFO[$j][2] ? $_INFO[$j][2] :'( VACÍO )';
            $mHtml .= '<td class="'.$class.'" align="center">'.$valor.'</td>';
            $mHtml .= '<td class="'.$class.'">'.$_INFO[$j][3].'</td>';
          $mHtml .= '</tr>';
        }
        
      $mHtml .= '</table>';
      $mHtml .= '</div>';
      echo $mHtml;
    }
    else
    {
      $insercion = new Consulta( "SELECT 1", $this -> conexion, "BR" );
      $count = 0;
      
      for ( $r = 1; $r < sizeof( $_DATA ); $r++ ) 
      {
        $row = $_DATA[$r];
        $mInsert = $this -> insertNovedad( $row );
        $count++;
      }
      
      if( $insercion = new Consulta( "COMMIT", $this -> conexion ) )
      {
        $mensaje =  "Se ha(n) Importado ".$count." Novedad(es)";
        $mens = new mensajes();
        $mens -> correcto( "IMPORTAR NOVEDADES", $mensaje );
      }
    }
    
    $formulario -> nueva_tabla();
   
    $formulario -> nueva_tabla();
    $formulario->oculto("Standa\" id=\"StandaID\"", DIR_APLICA_CENTRAL, 0);
    $formulario->oculto("filter\" id=\"filterID\"", COD_FILTRO_EMPTRA, 0);
    $formulario->oculto("window\" id=\"windowID\"", 'central', 0);
    $formulario->oculto("cod_servic\" id=\"cod_servicID\"", $mData['cod_servic'], 0);
    $formulario -> cerrar();
  }

  /*! \fn: getDataDespac
   *  \brief: Trae la Data del Despacho segun placa
   *  \author: Ing. Fabian Salinas
   *  \date:  23/11/2015
   *  \date modified: dd/mm/aaaa
   *  \modified by: 
   *  \param: $mNumPlacax  String  Numero de la placa
   *  \return: Matriz
   */
  private function getDataDespac( $mNumPlacax = null )
  {
    $mSql = " SELECT a.num_despac, b.cod_rutasx, b.cod_transp
                     FROM ".BASE_DATOS.".tab_despac_despac a,
                          ".BASE_DATOS.".tab_despac_vehige b
                    WHERE a.num_despac = b.num_despac
                      AND a.fec_salida IS NOT NULL 
                      AND a.fec_salida <= NOW() 
                      AND (a.fec_llegad IS NULL OR a.fec_llegad = '0000-00-00 00:00:00')
                      AND a.ind_planru = 'S' 
                      AND a.ind_anulad = 'R'
                      AND b.ind_activo = 'S' 
                      AND b.num_placax LIKE '". $mNumPlacax ."' 
                      AND b.cod_transp = '".$this -> cNitCorona."'";  
    $mConsult = new Consulta( $mSql, $this -> conexion );
    return $mResult = $mConsult -> ret_matriz();
  }

  /*! \fn: getDataDespacCorona
   *  \brief: Trae la Data del Despacho segun placa
   *  \author: Ing. Fabian Salinas
   *  \date:  23/11/2015
   *  \date modified: dd/mm/aaaa
   *  \modified by: 
   *  \param: $mNumPlacax  String  Numero de la placa
   *  \param: $mNumViaje  String  Numero del viaje
   *  \return: Matriz
   */
  private function getDataDespacCorona( $mNumPlacax = null,  $mNumViaje = null)
  {
    $mSql = " SELECT a.num_despac, b.cod_rutasx, b.cod_transp
                     FROM   ".BASE_DATOS.".tab_despac_vehige b 
                            INNER JOIN  ".BASE_DATOS.".tab_despac_despac a ON b.num_despac = a.num_despac 
                            INNER JOIN  ".BASE_DATOS.".tab_despac_corona c ON a.num_despac = c.num_dessat
                    WHERE a.num_despac = b.num_despac
                      AND a.fec_salida IS NOT NULL 
                      AND a.fec_salida <= NOW() 
                      AND (a.fec_llegad IS NULL OR a.fec_llegad = '0000-00-00 00:00:00')
                      AND a.ind_planru = 'S' 
                      AND a.ind_anulad = 'R'
                      AND b.ind_activo = 'S' 
                      AND b.num_placax LIKE '". $mNumPlacax ."'
                      AND c.num_despac = '". $mNumViaje ."'  
                      AND b.cod_transp = '".$this -> cNitCorona."'";  
    $mConsult = new Consulta( $mSql, $this -> conexion );
    return $mResult = $mConsult -> ret_matriz();
  }
  
  function insertNovedad( $row )
  {
    include_once("../".DIR_APLICA_CENTRAL."/despac/InsertNovedad.inc");
    include_once("../".DIR_APLICA_CENTRAL."/lib/general/functions.inc");

    $mDataDespac = self::getDataDespacCorona( $row[1], $row[0] );

    $mDataContro = getNextPC($this->conexion, $mDataDespac[0]['num_despac']);
   
    $mSql = "SELECT MAX(a.cod_consec) AS maximo
               FROM ".BASE_DATOS.".tab_despac_contro a,  
                    ".BASE_DATOS.".tab_despac_vehige b
              WHERE a.num_despac = b.num_despac AND
                    b.num_despac = '".$mDataDespac[0]['num_despac']."'";
    $consulta = new Consulta( $mSql, $this -> conexion );
    $mNewConsec = $consulta -> ret_matriz();
    
    /*** DATOS GENERALES ************************************/
    $mCodTransp = $mDataDespac[0]['cod_transp'];
    $mNumDespac = $mDataDespac[0]['num_despac'];
    $mCodRutasx = $mDataDespac[0]['cod_rutasx'];
    $mCodConsec = $mNewConsec[0][0] + 1;
    $mCodContro = $mDataContro['cod_contro'];
    $mObsContro = $row[5];
    $mFecContro = $row[3];
    $mCodNoveda = $row[2];
    $mTiemDurac = 0;
    $mCodSitiox = $this -> getCodSitiox( $row[4] );
    $mNomSitiox = $row[4];
    $mFecContro = $row[3];
    $mValRetras = 0;
    $mIndSitiox = 1;
    $mUsrCreaci = $_SESSION['datos_usuario']['cod_usuari'];
    $mFecCreaci = $row[3];
    /********************************************************/
    
    /**** INSERCION DE LA NOVEDAD DIRECTAMENTE AL METODO PARA QUE NOTIFIQUE A LA MATRIZ DE COMUNICACION */
    $mSelect = "SELECT cod_protoc
                  FROM ".BASE_DATOS.".tab_noveda_protoc
                 WHERE cod_transp = '".$mCodTransp."'
                   AND cod_noveda = '".$mCodNoveda."'";

    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_PROTOC = $consulta -> ret_matriz();
    /****************************************************************************************************/
    $_REQUEST['ind_protoc']   = sizeof( $_PROTOC ) > 0 ? 'yes' : 'no';
    $_REQUEST['tot_protoc_']  = 1;
    $_REQUEST['ind_activo_']  = 'S';
    $_REQUEST['obs_protoc0_'] = $mObsContro;
    $_REQUEST['protoc0_'] = $_PROTOC[0][0];
    
    $regist["despac"] = $mNumDespac;
    $regist["contro"] = $mCodContro;
    $regist["noveda"] = $mCodNoveda;
    $regist["tieadi"] = 0;
    $regist["fecact"] = date('Y-m-d H:i:s');
    #$regist["fecnov"] = $mFecContro;
    $regist["fecnov"] = date('Y-m-d H:i:s');
    $regist["usuari"] = $mUsrCreaci;
    $regist["nittra"] = $mCodTransp;
    $regist["indsit"] = "1";
    $regist["sitio"]  = $mNomSitiox;
    $regist["tie_ultnov"] = 0;
    $regist["tiem"]   = 0;
    $regist["observ"] = $mObsContro;
    $regist["rutax"]  = $mCodRutasx;
    $regist["wsdl"]   = "NO";

    $transac_nov = new InsertNovedad( $_REQUEST['cod_servic'], $_REQUEST['opcion'], $_SESSION['codigo'], $this -> conexion );
    $RESPON = $transac_nov -> InsertarNovedadNC( BASE_DATOS, $regist, 0 );
    /****************************************************************************************************/
  }
  
  function getCodSitiox( $nom_sitiox )
  {
    $mSelect = "SELECT cod_sitiox 
                  FROM ".BASE_DATOS.".tab_despac_sitio 
                 WHERE nom_sitiox = '".$nom_sitiox."'";
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $mSitiox = $consulta -> ret_matriz();
    
    if( sizeof( $mSitiox ) > 0 )
    {
      $ret = $mSitiox[0][0]; 
    }
    else
    {
      $mSelect = "SELECT MAX( cod_sitiox ) FROM ".BASE_DATOS.".tab_despac_sitio";
      $consulta = new Consulta( $mSelect, $this -> conexion );
      $mConsec = $consulta -> ret_matriz();
      $mNumConsec = $mConsec[0][0] + 1;
      
      $mInsert = "INSERT INTO ".BASE_DATOS.".tab_despac_sitio
                            ( cod_sitiox, nom_sitiox )
                      VALUES( '".$mNumConsec."',  '".$nom_sitiox."' )";
      if( $consulta = new Consulta( $mInsert, $this -> conexion ) )
        $ret = $mNumConsec;
      else
        $ret = NULL;
    }
    return $ret;
  }
  
  function VerifyData( $mData, $_DATA )
  {
    $_ERROR = array();
    
    $this -> field = $_DATA[0];

    $e = 0;

    for ( $r = 1; $r < sizeof( $_DATA ); $r++ ) 
    { 
      //------------------------
      $this -> row = $_DATA[$r];
      //------------------------
      if($this -> row[0] == "" && $this -> row[1] == "" && $this -> row[2] == "" && $this -> row[3] == "" && $this -> row[4] == "" && $this -> row[5] == ""){continue;}
      for ( $c = 0; $c < sizeof( $this -> row ); $c++ ) 
      { 
        //------------------------
        $item = $this -> row[$c];
        //------------------------
        switch ( $c ) { 
          /*case  0 :  //@Nit de la transportadora 
            if ( $item == NULL ) { 
              $this -> SetError( $e, $c, $r, 'EL NIT DE LA EMPRESA ES REQUERIDO' );
              $e ++;
            }
            if ( $item && strlen( $item ) > 11 ) { 
              $this -> SetError( $e, $c, $r, 'EL NIT DE LA EMPRESA DEBE CONTENER M&Aacute;XIMO 11 D&Iacute;GITOS' );
              $e ++;
            }
            if ( $item && strlen( $item ) < 6 ) { 
              $this -> SetError( $e, $c, $r, 'EL NIT DE LA EMPRESA DEBE CONTENER M&Iacute;NIMO 6 D&Iacute;GITOS' );
              $e ++;
            }
            if ( $item && !is_numeric( $item ) ) { 
              $this -> SetError( $e, $c, $r, 'EL NIT DE LA EMPRESA DEBE SER DE TIPO NUM&Eacute;RICO' );
              $e ++;
            }   
            if ( $item && !$this -> VerifyTransport( $item ) ) { 
              $this -> SetError( $e, $c, $r, 'EL NIT DE LA EMPRESA NO EXISTE' );
              $e ++;
            }   
          break;*/
          
          case  0 :  //@Placa en Viaje
            if ( $item == NULL ) { 
                $this -> SetError( $e, $c, $r, 'EL NUMERO DE VIAJE ES REQUERIDO' );
                $e ++;
            }
            if( $item && @ereg( "^([/V/])([/J-S/])-([0-9]{6})", $item ) === FALSE ){
              $this -> SetError( $e, $c, $r, 'VERIFIQUE EL FORMATO DEL VIAJE DEBE SER (VJ-123456) O (VS-123456)' );
              $e ++;
            }
            if( $item && !$this -> VerifyViaje($this -> row[1], $item ) ){
              $this -> SetError( $e, $c, $r, 'EL NUMERO DE VIAJE NO SE ENCUENTRA EN RUTA' );
              $e ++;
            }
          break;

          case  1 :  //@Placa en Viaje
            if ( $item == NULL ) { 
                $this -> SetError( $e, $c, $r, 'LA PLACA DEL VEH&Iacute;CULO ES REQUERIDA' );
                $e ++;
            }
            if( $item && !$this -> VerifyEnRuta( $item ) ){
              $this -> SetError( $e, $c, $r, 'LA PLACA DEL VEH&Iacute;CULO NO SE ENCUENTRA EN RUTA' );
              $e ++;
            }
          break;

          case  2 :  //@Codigo de la novedad
            if ( $item == NULL ) { 
                $this -> SetError( $e, $c, $r, 'EL C&Oacute;DIGO DE LA PNOVEDAD ES REQUERIDO' );
                $e ++;
            }
            if( $item && !$this -> VerifyNoveda( $item ) ){
              $this -> SetError( $e, $c, $r, 'LA NOVEDAD NO EXISTE, VERIFIQUE EN LA PLANTILLA' );
              $e ++;
            }
          break;  
          
          case  3 :  //@Fecha de la novedad
            if ( $item == NULL ) { 
                $this -> SetError( $e, $c, $r, 'LA FECHA DE LA PNOVEDAD ES REQUERIDA' );
                $e ++;
            }
            if( $item && @ereg( "^([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})", $item ) === FALSE ){
              $this -> SetError( $e, $c, $r, 'VERIFIQUE EL FORMATO DE LA FECHA (AAAA-MM-DD Hh:mm:ss)' );
              $e ++;
            }
            if( $item && !$this -> VerifyFecha( $item ) ){
              $this -> SetError( $e, $c, $r, 'LA FECHA DE LA NOVEDAD NO PUEDE SER MAYOR A LA FECHA ACTUAL' );
              $e ++;
            }
          break;
          
          case  4 :  //@Sitio de la novedad
            if ( $item == NULL ) { 
                $this -> SetError( $e, $c, $r, 'EL SITIO DE LA NOVEDAD ES REQUERIDO' );
                $e ++;
            }
          break;
          
          case  5 :  //@Observaciones de la novedad
            if ( $item == NULL ) { 
                $this -> SetError( $e, $c, $r, 'LAS OBSERVACIONES DE LA NOVEDAD ES REQUERIDO' );
                $e ++;
            }
          break;
        }
      }
      if( $this -> verifyInsertNoveda( $this -> row ) )
      {
        $this -> SetError( $e, NULL, $r, 'LA NOVEDAD YA FUE INSERTADA' );
        $e ++;
      }
      if( !$this -> verifyDespacTransp( $this -> row ) )
      {
        $this -> SetError( $e, NULL, $r, 'EL DESPACHO NO SE ENCUENTRA ASIGNADO A LA TRANSPORTADORA' );
        $e ++;
      }
      
    }
    return $this -> error;
  }
  
  function verifyDespacTransp( $row )
  {
    $mSql = "SELECT 1
               FROM ".BASE_DATOS.".tab_despac_despac a,
                    ".BASE_DATOS.".tab_despac_vehige b
              WHERE a.num_despac = b.num_despac
                AND a.fec_salida IS NOT NULL  
                AND a.fec_llegad IS NULL 
                AND a.ind_planru = 'S' 
                AND a.ind_anulad = 'R'
                AND b.num_placax = '". $row[1] ."' 
                AND b.cod_transp = '".$this -> cNitCorona."'";   
    $consulta = new Consulta( $mSql, $this -> conexion );
    $mTItemx = $consulta -> ret_matriz();
    
    if( $mTItemx )
     return TRUE;
    else
     return FALSE;
  }
  
  function verifyInsertNoveda( $row )
  {
    $mSql = "SELECT a.num_despac, b.cod_rutasx
               FROM ".BASE_DATOS.".tab_despac_despac a,
                    ".BASE_DATOS.".tab_despac_vehige b
              WHERE a.num_despac = b.num_despac
                AND a.fec_salida IS NOT NULL  
                AND a.fec_llegad IS NULL 
                AND a.ind_planru = 'S' 
                AND a.ind_anulad = 'R'
                AND b.num_placax = '". $row[1] ."' 
                AND b.cod_transp = '".$this -> cNitCorona."'";    
    $consulta = new Consulta( $mSql, $this -> conexion );
    $mDataDespac = $consulta -> ret_matriz();
    
    $mSql = "SELECT cod_contro, val_duraci
               FROM ".BASE_DATOS.".tab_genera_rutcon
              WHERE cod_rutasx = '".$mDataDespac[0]['cod_rutasx']."' 
              ORDER BY val_duraci ASC";
    $consulta = new Consulta( $mSql, $this -> conexion );
    $mDataContro = $consulta -> ret_matriz();
    
    /*** DATOS GENERALES ************************/
    $mNumDespac = $mDataDespac[0]['num_despac'];
    $mCodRutasx = $mDataDespac[0]['cod_rutasx'];
    $mFecCreaci = $row[3];
    $mCodContro = $mDataContro[1]['cod_contro'];
    /********************************************/
    
    $mSelect = "SELECT 1 
                  FROM ".BASE_DATOS.".tab_despac_contro
                 WHERE num_despac = '".$mNumDespac."'
                   AND cod_contro = '".$mCodContro."'
                   AND cod_rutasx = '".$mCodRutasx."'
                   AND fec_creaci = '".$mFecCreaci."' ";
                   
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $mTItemx = $consulta -> ret_matriz();
    
    if( $mTItemx )
     return TRUE;
    else
     return FALSE;
  }
  
  function SetError( $e, $c, $r, $des ) 
  { 
    $this -> error[$e][0] = $r;
    $this -> error[$e][1] = $c != NULL ? $c+1:'REGISTRO';
    $this -> error[$e][2] = $c != NULL ? $this -> row[$c] : '- - -';
    $this -> error[$e][3] = $des;
  }

  function VerifyFecha( $item )
  {
    $fec_actual = strtotime( '+10 minute' , strtotime( date( 'Y-m-d H:i:s' ) ) );
    
    if( strtotime( $item ) < $fec_actual )
     return TRUE;
    else
     return FALSE;
  }
  
  function VerifyNoveda( $item )
  {
    $mSql = "SELECT 1
               FROM ".BASE_DATOS.".tab_genera_noveda a
              WHERE a.cod_noveda = '". $item ."' ";    
    $consulta = new Consulta( $mSql, $this -> conexion );
    $mTItemx = $consulta -> ret_matriz();
    
    if( $mTItemx )
     return TRUE;
    else
     return FALSE;
  }
  
  /*! \fn: VerifyEnRuta
   *  \brief: Verifica si la placa esta en ruta
   *  \author: 
   *  \date: dd/mm/aaaa
   *  \date modified: 23/11/2015
   *  \modified by: Ing. Fabian Salinas
   *  \param: $mNumPlacax  String  Numero de la placa
   *  \return: Boolean
   */
  function VerifyEnRuta( $mNumPlacax )
  {
    $mTItemx = self::getDataDespac( $mNumPlacax );
    
    if( sizeof($mTItemx) > 0 )
     return TRUE;
    else
     return FALSE;
  }
  
  function VerifyTransport( $item )
  {
    $mSql = "SELECT 1
               FROM ".BASE_DATOS.".tab_tercer_emptra 
              WHERE cod_tercer = '". $item ."' ";    
    $consulta = new Consulta( $mSql, $this -> conexion );
    $mTItemx = $consulta -> ret_matriz();
    
    if( $mTItemx )
     return TRUE;
    else
     return FALSE;
  }

   /*! \fn: VerifyViaje
   *  \brief: Verifica si el viaje se encuente en ruta
   *  \author: 
   *  \date: dd/mm/aaaa
   *  \date modified: 30/06/2017
   *  \modified by: Edward Serrano
   *  \param: $mNumViaje  String  Numero del viaje 
   *  \return: Boolean
   */
  function VerifyViaje( $mNumPlacax, $mNumViaje )
  {
    $mTItemx = self::getDataDespacCorona( $mNumPlacax, $mNumViaje );
    
    if( sizeof($mTItemx) > 0 )
     return TRUE;
    else
     return FALSE;
  }

  /*! \fn: getDataDespViaje
   *  \brief: Trae la Data del Despacho segun el viaje
   *  \author: Edward Serrano
   *  \date:  30/06/2017
   *  \date modified: dd/mm/aaaa
   *  \modified by: 
   *  \param: $mNumViaje  String   Numero del viaje 
   *  \return: Matriz
   */
  function getDataDespViaje( $mNumViaje = null )
  {
    $mSql = " SELECT a.num_despac, b.cod_rutasx, b.cod_transp
                     FROM ".BASE_DATOS.".tab_despac_vehige b 
                          INNER JOIN ".BASE_DATOS.".tab_despac_despac a ON b.num_despac = a.num_despac
                          LEFT  JOIN ".BASE_DATOS.".tab_despac_corona c ON a.num_despac = c.num_dessat
                    WHERE a.fec_salida IS NOT NULL 
                      AND a.fec_salida <= NOW() 
                      AND (a.fec_llegad IS NULL OR a.fec_llegad = '0000-00-00 00:00:00')
                      AND a.ind_planru = 'S' 
                      AND a.ind_anulad = 'R'
                      AND b.ind_activo = 'S' 
                      AND c.num_despac = '". $mNumViaje ."'";  
    $mConsult = new Consulta( $mSql, $this -> conexion );
    return $mResult = $mConsult -> ret_matriz();
  }

  function GetFileData() 
  {
    if ( $_FILES['rut_archiv']['name'] != NULL ) {
      $file_temp = $_FILES['rut_archiv']['tmp_name'];
      $file_move = 'import/'.$_FILES['rut_archiv']['name'];
      move_uploaded_file( $file_temp, $file_move );
    }

    $_FILE = file( $file_move );
    $size_file = sizeof( $_FILE );
    $_DATA = array();
    
    for ( $f = 0; $f < $size_file; $f++ ) {
      $_ROW = explode( ';', $_FILE[$f]);
      for ( $r = 0; $r < sizeof( $_ROW ); $r++ ) { 
        if ( !$_ROW[0] )
          break;
        $_DATA[$f][$r] = trim( $_ROW[$r] );
      }
    }
    return $_DATA;
  }
  
  function MainForm( $mData )
  {
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/min.js' ></script>\n";
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/jquery.js' ></script>\n";
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/es.js' ></script>\n";
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/time.js' ></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/LoadAsignaDestin.js' ></script>\n";
    
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>";
    
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";
    
    echo "<script>
          function ValidateIt( rut_archiv )
          {
            var patt=/\.csv$/g;
            
            if( !patt.test( rut_archiv.val() ) )
            {
              $('#errorID').hide();
              $('#errorID').removeClass( 'error' );
              $('#errorID').addClass( 'alert' );
              $('#errorID').html('<span>La Extensi&oacute;n del Archivo es Incorrecta</span>');
              $('#errorID').show('slow');
              rut_archiv.val('');
              return false;
            }
          }
          
          function Validator()
          {
            if( $('#rut_archiv').val() == '' )
            {
              $('#errorID').hide();
              $('#errorID').removeClass( 'alert' );
              $('#errorID').addClass( 'error' );
              $('#errorID').html('<span>Por favor Seleccione un Archivo.</span>');
              $('#errorID').show('slow');
            }
            else
            {
              $('#formID').submit();
            }
          }
          </script>";
    
    $formulario = new Formulario ( "index.php", "post", "IMPORTAR NOVEDADES", "form\" enctype=\"multipart/form-data\" id=\"formID" );
    
    $mHtml = '</tr><tr><td><center>';
    $mHtml .= '<div class="StyleDIV" align="center">';
      $mHtml .= '<table width="98%" cellpadding="0" cellspacing="0">';
        $mHtml .= '<tr>';
          $mHtml .= '<td colspan="4" style="padding:15px;" class="CellHead">En esta opci&oacute;n usted podr&aacute; Importar Novedades desde un archivo plano para Despachos en Ruta.<br> Si no conoce la funcionalidad por favor descargue el <i>Instructivo</i>. Si desea realizar la Importaci&oacute;n, descargue la <i>Plantilla</i>.</td>';
        $mHtml .= '</tr>';
        
        $mHtml .= '<tr>';
          $mHtml .= '<td class="cellInfo1" width="24%">&nbsp;</td>';
          $mHtml .= '<td class="cellInfo1" width="25%" align="center"><a href="../'.DIR_APLICA_CENTRAL.'/desnew/instructivo.pdf" target="_blank"><img src="../'.DIR_APLICA_CENTRAL.'/imagenes/pdf.jpg" width="30" height="30" /></a><br>INSTRUCTIVO</td>';
          $mHtml .= '<td class="cellInfo1" width="25%" align="center"><a href="../'.DIR_APLICA_CENTRAL.'/desnew/plantilla.xlsx" target="_blank"><img src="../'.DIR_APLICA_CENTRAL.'/imagenes/xls.gif" width="30" height="30" /></a><br>PLANTILLA</td>';
          $mHtml .= '<td class="cellInfo1" width="24%">&nbsp;</td>';
        $mHtml .= '</tr>';
        
        $mHtml .= '<tr>';
          $mHtml .= '<td class="cellInfo2" align="right" width="24%">* Archivo:&nbsp;&nbsp;&nbsp;&nbsp;</td>';
          $mHtml .= '<td class="cellInfo2" width="25%" align="left">&nbsp;&nbsp;&nbsp;&nbsp;<input type="file" name="rut_archiv" id="rut_archiv" size="50" maxlength="255" onchange="ValidateIt( $(this) )"/></td>';
          $mHtml .= '<td class="cellInfo2" width="25%" align="right"><input class="crmButton small save" type="button" onclick="Validator();" value="Enviar" name="Enviar"></td>';
          $mHtml .= '<td class="cellInfo2" width="24%">&nbsp;</td>';
        $mHtml .= '</tr>';
      
      $mHtml .= '</table>';
    $mHtml .= '</div>';
    
    $mHtml .= '<div id="errorID">';
    $mHtml .= '</div>';
    
    $mHtml .= '<center></td>';
    
    echo $mHtml;
    
    $formulario -> nueva_tabla();
   
    $formulario -> nueva_tabla();
    $formulario->oculto("Standa\" id=\"StandaID\"", DIR_APLICA_CENTRAL, 0);
    $formulario->oculto("filter\" id=\"filterID\"", COD_FILTRO_EMPTRA, 0);
    $formulario->oculto("window\" id=\"windowID\"", 'central', 0);
    $formulario->oculto("cod_servic\" id=\"cod_servicID\"", $mData['cod_servic'], 0);
    $formulario->oculto("opcion\" id=\"opcionID\"", 99, 0);
    $formulario->oculto("filter\" id=\"filterID\"", COD_FILTRO_EMPTRA, 0);
    $formulario -> cerrar();
  }
}

$_IMPORT = new ImportNovedad( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );

?>