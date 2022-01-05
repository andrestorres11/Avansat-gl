<?php

/*
 * @author : Ing. Christiam Barrera
 * @file   : functions.inc
 * @desc   : Funciones
 */

 
function GetUniqueCol( $mCol, $mMatrix )
{
  for ( $i = 0, $mRows = sizeof( $mMatrix ); $i < $mRows; $i++ )
  {
    if ( !array_key_exists( $mCol, $mMatrix[$i] ) )
    {
      return array();
    }
    $mTemp[] = $mMatrix[$i][$mCol];
  }
  $mTemp = array_unique( $mTemp );
  foreach( $mTemp as $mItem )
  {
    $mFinal[] = $mItem;
  }
  return $mFinal;
}

/*
 * Función que da formato de moneda a una cadena númerica. Exp. ->2000000, ->2.000.000
 */
function MoneyFormat( $num )
{
    return str_replace( ",", ".", number_format( $num, "." ) );
}


function ReturnDate( $date = NULL )
{
  if ( !$date )
  {
    $date = date( "Y-m-d" );
  }
  $time = explode( "-", $date );
  switch( $time[1] )
  {
    case '01' : $month = "Enero";      break;
    case '02' : $month = "Febrero";    break;
    case '03' : $month = "Marzo";      break;
    case '04' : $month = "Abril";      break;
    case '05' : $month = "Mayo";       break;
    case '06' : $month = "Junio";      break;
    case '07' : $month = "Julio";      break;
    case '08' : $month = "Agosto";     break;
    case '09' : $month = "Septiembre"; break;
    case '10' : $month = "Octubre";    break;
    case '11' : $month = "Noviembre";  break;
    case '12' : $month = "Diciembre";  break;
  }
  switch ( date( "w", strtotime($date) ) )
  {
    case '0' : $day = "Domingo";    break;
    case '1' : $day = "Lunes";      break;
    case '2' : $day = "Martes";     break;
    case '3' : $day = "Miércoles";  break;
    case '4' : $day = "Jueves";     break;
    case '5' : $day = "Viernes";    break;
    case '6' : $day = "Sábado";     break;
  }
  return $day.", ".$time[2]." de ".$month." de ".$time[0];
}


function IncludeJS( $mFile, $mHref = NULL )  
{
    if ( !$mHref )
    {
        $mHref = "../".DIR_APLICA_CENTRAL."/js/";
    }
    echo "\n    ";
    echo '<script language="javascript" src="'.$mHref.$mFile.'"></script>';
}


function ValidateRanges( $min, $act, $max )
{
    while( strlen( $min ) < 15 )
        $min = "0".$min;
    while( strlen( $act ) < 15 )
        $act = "0".$act;
    while( strlen( $max ) < 15 )
        $max = "0".$max;
    if ( $min <= $act && $act <= $max )
        return true;
    else
        return false;
}


function CalculateNext( $mCode )
{
    for ( $s = 0, $size = strlen( $mCode ); $s < $size; $s++ )
    {
        $c = $mCode[$s];
        if ( $c=="0"||$c=="1"||$c=="2"||$c=="3"||$c=="4"||$c=="5"||$c=="6"||$c=="7"||$c=="8"||$c=="9" )
        {
            $num .= $c;
        }
        else
        {
            $str .= $c;
        }
    } 
    $zeros = "";
    for ( $n = 0; $n < strlen( $num ); $n++ )
    {
        if ( $num[$n] != 0 )
            break;
        else
            $zeros .= $num[$n];
    }

    $new = $str.$zeros.( $num + 1 );
    if ( strlen( $new ) > strlen( $mCode ) )
    {
        return substr( $new, 1 );
    }
    else
    {
        return $new;
    }
}


function ShowOk( $message )
{
    $html  = "\n        ";
    $html .= '<td align="right" width="40%">';
    $html .= "\n          ";
    $html .= '<img src="../'.DIR_APLICA_CENTRAL.'/imagenes/general/ok.png" />';
    $html .= "\n        ";
    $html .= '</td">';  
    $html .= "\n        ";
    $html .= '<td align="left" width="60%">';
    $html .= "\n          ";
    $html .= '<hr width="60%" />';
    $html .= "\n          ";
    $html .= '<span style="font-size:20px; font-weight:bold; text-align:left; font-family:Palatino Linotype">';
    $html .= "\n            ";
    $html .= $message;
    $html .= "\n          ";         
    $html .= '</span>';
    $html .= "\n        ";
    $html .= '<hr width="60%" />';
    $html .= "\n      ";
    $html .= '</td">';
    echo $html;
}

function ShowError( $message )
{
    $html  = "\n        ";
    $html .= '<td align="right" width="40%">';
    $html .= "\n          ";
    $html .= '<img src="../'.DIR_APLICA_CENTRAL.'/imagenes/general/error.png" />';
    $html .= "\n        ";
    $html .= '</td">';  
    $html .= "\n        ";
    $html .= '<td align="left" width="60%">';
    $html .= "\n          ";
    $html .= '<hr width="60%" />';
    $html .= "\n          ";
    $html .= '<span style="font-size:20px; font-weight:bold; text-align:left; font-family:Palatino Linotype">';
    $html .= "\n            ";
    $html .= $message;
    $html .= "\n          ";         
    $html .= '</span>';
    $html .= "\n        ";
    $html .= '<hr width="60%" />';
    $html .= "\n      ";
    $html .= '</td">';
    echo $html;
}

function ShowMessage($type, $title, $message, $die = false) {

    switch ($type) {
        case 'a':
            $labeltype = 'warningbox';
            $imagetype = 'warning.png';
            break;
        case 's':
            $labeltype = 'successbox';
            $imagetype = 'check2.png';
            break;
        case 'i':
            $labeltype = 'infobox';
            $imagetype = 'info.png';
            break;
        default:
            $labeltype = 'errorbox';
            $imagetype = 'error2.png';
            break;
    }


    echo '<div class="notify ' . $labeltype . '">
            <h1>' . $title . '!</h1>
              <span class="alerticon">
                <img src="../' . DIR_APLICA_CENTRAL . '/imagenes/' . $imagetype . '" style="width: 32px;">
              </span>
              <p>' . $message . '</p>
          </div>';

    if($die == true){
        die();
    }
}


function ArrayUnique( $mArray )
{
    $mRes = array();
    foreach( $mArray as $item )
    {
        if ( !in_array( $item, $mRes ) )
        {
            $mRes[] = $item;
        }
    }
    return $mRes;
}

//--------------------------------------------------------
//@OBTIENE UNA COLUMNA EN ARREGLO DESDE UNA MATRIZ
function GetColumnFromMatrix( $mMatrix, $mColumn )  
{
    $mLength = sizeof( $mMatrix );
    for ( $x = 0; $x < $mLength; $x++ )
        $mAux[] = $mMatrix[$x][$mColumn];
    return $mAux;
}

//--------------------------------------------------------
//@RETORNA UNA MATRIZ ORDENADA POR LA COLUMNA ESPECIFICADA
function SortMatrix( $mMatrix, $mColumn, $mOrder = "ASC" )  
{
    $mColumn = GetColumnFromMatrix( $mMatrix, $mColumn );
    natsort( $mColumn );
    reset( $mColumn );
    while ( list ( $key ) = each( $mColumn ) )  
        $mAux[] = $mMatrix[$key];
    if ( strtoupper( $mOrder ) == "DESC" )
        return array_reverse( $mAux );
    else
        return $mAux;
}

/*
$a[] = array( 'cod_tercer' => '98765430', 'nom_tercer' => 'Mauricio Cúbides', 'age_tercer' => '28' );
$a[] = array( 'cod_tercer' => '56457567', 'nom_tercer' => 'Ricardo Delgado', 'age_tercer' => '22' );
$a[] = array( 'cod_tercer' => '21343543', 'nom_tercer' => 'Yahira Soquer', 'age_tercer' => '28' );
$a[] = array( 'cod_tercer' => '96775626', 'nom_tercer' => 'Christiam Barrera', 'age_tercer' => '25' );
$a[] = array( 'cod_tercer' => '45468223', 'nom_tercer' => 'Charlie Mock', 'age_tercer' => '25' );

echo "<pre>";
print_r( $a );
echo "</pre>";

echo "<pre>";
print_r( SortMatrix( $a, 'nom_tercer' ) );
echo "</pre>";
*/

//RDJDM00123
//$temp=124
function GetConsec( $mNumber )
{
  if ( $mNumber == '0' )
    return '1';
  $reverse = strrev( $mNumber );
  $length  = strlen( $mNumber );
  for ( $i = 0; $i < $length; $i++ )
  {
    if ( !is_numeric( $reverse[$i] ) )
    {
      break;
    }
  }
  $n = $i;
  $number  = NULL;
  for ( $i = 0; $i < $n; $i++ )
  {
    $number .= $reverse[$i];
  }
  $number = strrev( $number );
  $zero = NULL;
  $numb = NULL;
  for ( $i = 0; $i < strlen( $number ); $i++ )
  {
    if ( $number[$i] == '0' )
    {
      $zero .= '0';
    }
    else
    {
      break;
    }
  }
  $temp = $number + 1;
  $temp = $zero . $temp;
  while ( strlen( $temp ) < strlen( $number ) )
  {
    $temp = $temp.'0';
  }
  $number = $temp;
  $string  = NULL;
  for ( $i = $n; $i < $length; $i++ )
  {
    $string .= $reverse[$i];
  }
  $string = strrev( $string );
  return $string.$number;
}

//------------------------------------------------------------------------------------------
//@REEMPLAZA TODAS LAS APARICIONES DEL VALOR BUSCADO POR UN NUEVO VALOR DENTRO DE UNA MATRIZ 
//@PASADA POR REFERENCIA DE MEMORIA SIN UTILIZAR UNA MATRIZ TEMPORAL DE RETORNO
function ReplaceInMatrix( &$matrix, $old, $new )
{
  for ( $i = 0, $size = sizeof( $matrix ); $i < $size; $i++ )
  {
    $keys = array_keys( $matrix[$i] );
    for ( $j = 0, $row_size = sizeof( $matrix[$i] ); $j < $row_size; $j++ )
    {
      $matrix[$i][$keys[$j]] = $matrix[$i][$keys[$j]] == $old ? $new : $matrix[$i][$keys[$j]];
    }
  }
}

//---------------------------------------------------------------
//@FORMATEA LOS CAMPOS DE CUALQUIER CONSULTA
function field_format( &$mKey, $mTrim = NULL, $mChars = NULL )
{
  if ( $mChars === NULL )
  {
    $mKey = $mTrim === NULL ? str_replace( ' ', NULL, $mKey ) : trim( $mKey );
  }
    else
    {
        foreach ( $mChars as $mChar )
        {
            $mKey = str_replace( $mChar, NULL, $mKey );
        }
    }
}



function in_matrix( $item, $matrix )
{
  foreach ( $matrix as $array )
  {
    if ( in_array( $item, $array ) )
      return TRUE;
  }
  return FALSE;
}



function get_position_matrix( $item, $matrix )
{
  $length = sizeof( $matrix );
  for ( $i = 0; $i < $length; $i++ )
  {
    if ( in_array( $item, $matrix[$i] ) )
      return $i;
  }
  return -1;
}


function sumarMinutosFecha($FechaStr, $MinASumar) {
  $FechaStr = str_replace("-", " ", $FechaStr);
  $FechaStr = str_replace(":", " ", $FechaStr);
  $FechaOrigen = explode(" ", $FechaStr);
  $Dia = $FechaOrigen[2];
  $Mes = $FechaOrigen[1];
  $Ano = $FechaOrigen[0];
  $Horas = $FechaOrigen[3];
  $Minutos = $FechaOrigen[4];
  $Segundos = $FechaOrigen[5];
  // Sumo los minutos
  $Minutos = ((int)$Minutos) + ((int)$MinASumar); 
  // Asigno la fecha modificada a una nueva variable
  $FechaNueva = date("Y-m-d H:i:s",mktime($Horas,$Minutos,$Segundos,$Mes,$Dia,$Ano));
  return $FechaNueva;
}

/*! \fn: getDiffTime
 *  \brief: Trae la diferencia en minutos de dos fechas
 *  \author: Ing. Fabian Salinas
 *  \date: 02/06/2015
 *  \date modified: dia/mes/año
 *  \param: $mFec1 date 
 *  \param: $mFec2 date
 *  \return: Diferencia en Minutos
 */
function getDiffTime($mFec1, $mFec2)
{
  if($mFec1 != 0)
    return $mMin = round(( strtotime($mFec2) - strtotime($mFec1) )/60);
  else
    return '0';
}

/*! \fn: getControDespac
 *  \brief: Trae los puestos de control del plan de ruta del despacho
 *  \author: Ing. Fabian Salinas
 *  \date: 01/06/2015
 *  \date modified: dia/mes/año
 *  \param: $mConexion   Conexión BD
 *  \param: $mDespac     Integer  Número Despacho
 *  \param: $mTipReturn  String   sql: Retorna String Query; 1PC: Retorna primer PC
 *  \return: Sql o Matriz
 */
function getControDespac(&$mConexion, $mDespac, $mTipReturn = NULL )
{
  $mSql = " SELECT a.cod_rutasx 
              FROM ".BASE_DATOS.".tab_despac_seguim a 
             WHERE a.num_despac = '{$mDespac}' 
          GROUP BY a.cod_rutasx 
          ORDER BY a.fec_creaci ";
  $mConsult = new Consulta( $mSql, $mConexion );
  $mRutasDespac = $mConsult -> ret_matrix('i');

  //$mOrder = sizeof($mRutasDespac) < 2 ? " c.val_duraci " : " b.num_consec ";
  $mOrder = sizeof($mRutasDespac) < 2 ? " c.val_duraci " : " c.val_duraci ";
  
  $mSql = " SELECT c.cod_contro, d.ind_urbano, d.ind_virtua, 
                   UPPER(d.nom_contro) AS nom_contro, 
                   IF( d.cod_colorx IS NULL, '336600', d.cod_colorx ) AS cod_colorx,
                   b.fec_planea AS fec_progra,
                   b.fec_alarma AS fec_planea, 
                   {$mOrder} AS ind_ordenx, 
                   b.cod_rutasx  
              FROM ".BASE_DATOS.".tab_despac_seguim b 
        INNER JOIN ".BASE_DATOS.".tab_genera_rutcon c 
                ON b.cod_contro = c.cod_contro 
               AND b.cod_rutasx = c.cod_rutasx 
        INNER JOIN ".BASE_DATOS.".tab_genera_contro d 
                ON c.cod_contro = d.cod_contro 
             WHERE b.num_despac = '{$mDespac}'
               AND c.ind_estado = '1'
               AND b.ind_estado != '2'
             ORDER BY ".$mOrder;

  $mSql .= $mTipReturn == '1PC' ? " LIMIT 1 " : "";

  if( $mTipReturn == 'sql' )
    return $mSql;
  else{
    $mConsult = new Consulta( $mSql, $mConexion );
    return $mResult = $mConsult -> ret_matrix('a'); 
  }
}

/*! \fn: getNovedadesDespac
 *  \brief: Trae los PC del despacho y las novedades correspondientes por PC 
 *  \author: Ing. Fabian Salinas
 *  \date: 23/06/2015
 *  \date modified: dia/mes/año
 *  \param: $mConexion  Conexión BD
 *  \param: $mNumDespac  Integer  Número del Despacho
 *  \param: $mIndReturn  Integer  (0:PC con/sin Novedades; 1:PC con Novedades; 2:Ultimo PC con Novedad)
 *  \param: $mFiltro  String  sql Where Inyectado Inicia con AND
 *  \return: Matriz
 */
function getNovedadesDespac( &$mConexion, $mNumDespac, $mIndReturn, $mFiltro = NULL )
{
  $mSqlRuta = getControDespac( $mConexion, $mNumDespac, 'sql' );

  $mSql = "SELECT z.cod_contro, z.cod_noveda, z.fec_noveda, z.fec_crenov, 
                  z.ind_ensiti, z.fec_planea, z.ind_fuepla, z.tiem_duraci, 
                  z.nom_noveda, z.nom_contro, z.ind_limpio, z.ind_manala, 
                  z.fec_progra, z.ind_virtua, z.cod_rutasx, 
                  IF(z.nom_sitiox = '', z.nom_contro, z.nom_sitiox) AS nom_sitiox, z.obs_noveda, z.nov_especi, z.ind_alarma,
                  z.num_despac

            FROM (
                         SELECT y.cod_contro, y.ind_urbano, y.ind_virtua, 
                                y.nom_contro, y.cod_colorx, y.fec_progra, 
                                y.fec_planea, y.ind_ordenx, x.fec_noveda, 
                                x.nom_noveda, x.usr_creaci, x.fec_creaci AS fec_crenov, 
                                x.nom_sitiox, x.obs_noveda, x.nov_especi, 
                                x.cod_noveda, x.tab_origen, x.cod_consec, 
                                x.ind_ensiti, x.ind_fuepla, x.tiem_duraci, 
                                x.cod_etapax, x.ind_limpio, x.ind_manala, 
                                y.cod_rutasx, x.ind_alarma,
                                x.num_despac

                           FROM (
                                  (
                                         SELECT a.fec_contro AS fec_noveda, 
                                                UPPER(b.nom_noveda) AS nom_noveda, 
                                                a.usr_creaci, a.fec_creaci, 
                                                UPPER(c.nom_sitiox) AS nom_sitiox, 
                                                a.obs_contro AS obs_noveda, 
                                                b.nov_especi, a.cod_noveda, 
                                                '1' AS tab_origen, a.cod_consec, 
                                                a.cod_contro, '0' AS ind_ensiti, 
                                                b.ind_fuepla, a.tiem_duraci, 
                                                b.cod_etapax, b.ind_limpio, 
                                                b.ind_manala, b.ind_alarma,
                                                a.num_despac
                                           FROM ".BASE_DATOS.".tab_despac_contro a 
                                     INNER JOIN ".BASE_DATOS.".tab_genera_noveda b 
                                             ON a.cod_noveda = b.cod_noveda 
                                     INNER JOIN ".BASE_DATOS.".tab_despac_sitio c 
                                             ON a.cod_sitiox = c.cod_sitiox
                                          WHERE a.num_despac = '{$mNumDespac}' 
                                  )
                                  UNION 
                                  (
                                         SELECT a.fec_noveda, 
                                                UPPER(b.nom_noveda) AS nom_noveda,
                                                a.usr_creaci, 
                                                a.fec_creaci,  
                                                '' AS nom_sitiox, 
                                                a.des_noveda AS obs_noveda, 
                                                b.nov_especi, a.cod_noveda, 
                                                '2' AS tab_origen, '1' AS cod_consec, 
                                                a.cod_contro, '1' AS ind_ensiti, 
                                                b.ind_fuepla, a.tiem_duraci, 
                                                b.cod_etapax, b.ind_limpio, 
                                                b.ind_manala, b.ind_alarma,
                                                a.num_despac
                                           FROM ".BASE_DATOS.".tab_despac_noveda a 
                                     INNER JOIN ".BASE_DATOS.".tab_genera_noveda b 
                                             ON a.cod_noveda = b.cod_noveda 
                                          WHERE a.num_despac = '{$mNumDespac}' 
                                  )
                                  UNION
                                  (
                                         SELECT a.fec_solici AS fec_noveda, 
                                                c.nom_noveda,
                                                a.usr_solici AS usr_creaci, 
                                                a.fec_solici AS fec_creaci, 
                                                UPPER(b.nom_contro) AS nom_sitiox, 
                                                GROUP_CONCAT( d.tex_encabe ) AS obs_noveda, 
                                                '0' AS nov_especi, a.cod_noveda, 
                                                '3' AS tab_origen, d.cod_consec, 
                                                a.cod_contro, '0' AS ind_ensiti, 
                                                c.ind_fuepla, '' AS tiem_duraci, 
                                                c.cod_etapax, '0' AS ind_limpio, 
                                                c.ind_manala, c.ind_alarma,
                                                a.num_despac
                                           FROM ".BASE_DATOS.".tab_recome_asigna a 
                                     INNER JOIN ".BASE_DATOS.".tab_genera_contro b 
                                             ON a.cod_contro = b.cod_contro 
                                     INNER JOIN ".BASE_DATOS.".tab_genera_noveda c 
                                             ON a.cod_noveda = c.cod_noveda 
                                     INNER JOIN ".BASE_DATOS.".tab_genera_recome d 
                                             ON a.cod_recome = d.cod_consec 
                                          WHERE a.num_despac = '{$mNumDespac}' 
                                       GROUP BY a.cod_contro 
                                  )
                                  UNION
                                  (
                                         SELECT a.fec_ejecut AS fec_noveda, 
                                                'SOLUCION RECOMENDACION' AS nom_noveda, 
                                                a.usr_ejecut AS usr_creaci,
                                                a.fec_ejecut AS fec_creaci, 
                                                UPPER(b.nom_contro) AS nom_sitiox, 
                                                GROUP_CONCAT( CONCAT(d.tex_encabe, ': ', a.obs_ejecuc) ) AS obs_noveda, 
                                                '0' AS nov_especi, a.cod_noveda, 
                                                '4' AS tab_origen, d.cod_consec, 
                                                a.cod_contro, '1' AS ind_ensiti, 
                                                c.ind_fuepla, '' AS tiem_duraci, 
                                                c.cod_etapax, '1' AS ind_limpio, 
                                                c.ind_manala, c.ind_alarma,
                                                a.num_despac
                                           FROM ".BASE_DATOS.".tab_recome_asigna a 
                                     INNER JOIN ".BASE_DATOS.".tab_genera_contro b 
                                             ON a.cod_contro = b.cod_contro 
                                     INNER JOIN ".BASE_DATOS.".tab_genera_noveda c 
                                             ON a.cod_noveda = c.cod_noveda 
                                     INNER JOIN ".BASE_DATOS.".tab_genera_recome d 
                                             ON a.cod_recome = d.cod_consec 
                                          WHERE a.num_despac = '{$mNumDespac}' 
                                            AND a.obs_ejecuc IS NOT NULL 
                                       GROUP BY a.cod_contro 
                                  )
                                ) x 
                     RIGHT JOIN ( {$mSqlRuta} ) y 
                             ON x.cod_contro = y.cod_contro 
                  ) z 
            WHERE 1=1 
                  ".$mFiltro;

  if( $mIndReturn == 1 || $mIndReturn == 2 )
    $mSql .= " AND z.cod_noveda IS NOT NULL ";

  $mSql .= " ORDER BY z.ind_ordenx, z.ind_ensiti, z.fec_crenov ";

  $mConsult = new Consulta( $mSql, $mConexion );
  $mResult = $mConsult -> ret_matrix('a');

  if( $mIndReturn != 2 )
    return $mResult;
  else
  {
    $mCant = sizeof($mResult);
    return $mResult = $mCant < 1 ? false : $mResult[($mCant-1)];
  }
}


/*! \fn: getNextPC
 *  \brief: Trae el siguiente PC del plan de Ruta
 *  \author: Ing. Fabian Salinas
 *  \date: 03/07/2015
 *  \date modified: dia/mes/año
 *  \param: $mConexion  Conexión BD
 *  \param: $mNumDespac  Integer  Número del Despacho
 *  \return: Array
 */
function getNextPC( &$mConexion, $mNumDespac )
{
  $mUlPCNov = getNovedadesDespac( $mConexion, $mNumDespac, 2 ); #Ultimo PC con novedad

  if( $mUlPCNov == false ){ #Despacho sin Novedades 
    $mRutaDes = getControDespac( $mConexion, $mNumDespac, '1PC' ); #Primer PC del Despacho 
    return $mRutaDes[0];
  }elseif( $mUlPCNov[ind_ensiti] == '0' ){ #Si la ultima novedad es Antes de Sitio
    return $mUlPCNov;
  }else{ #Ultima novedad es en Sitio

    $mRutaDes = getControDespac( $mConexion, $mNumDespac ); #Ruta del Despacho
    if( $mRutaDes[( (sizeof($mRutaDes))-1 )][cod_contro] == $mUlPCNov[cod_contro] ){ #Si el ultimo PC con novedad en sitio es el ultimo PC del Plan de ruta
      $mUlPCNov[ind_finrut] = '1';
      return $mUlPCNov;
    }else{
      for ($i=0; $i < sizeof($mRutaDes); $i++)
      {
        if( $mRutaDes[$i][cod_contro] == $mUlPCNov[cod_contro] )
          return $mRutaDes[($i+1)];
      }
    }
    
  }
}

/*! \fn: getTranspTipser
 *  \brief: Trae el ultimo registro por transportadora de tab_transp_tipser
 *  \author: Ing. Fabian Salinas
 *  \date: 17/07/2015
 *  \date modified: dia/mes/año
 *  \param: mConexion  Conexión BD
 *  \param: mSqlWhere   string   sql Where Inyectado Inicia con AND
 *  \param: mNomCampo   Array    Compos a retornar
 *  \return: Matriz
 */
function getTransTipser( &$mConexion, $mSqlWhere = NULL, $mNomCampo = NULL )
{
  if( $mNomCampo != NULL ){
    $mCampos = implode(', ', $mNomCampo);
  }else{
    $mCampos = "a.* ";
  }

  $mSql = " SELECT $mCampos 
            FROM (
                      SELECT b.*
                      FROM (
                                  SELECT c.ind_segcar, c.ind_segtra, c.ind_segdes, 
                                         c.cod_transp, c.num_consec, d.nom_tipser, 
                                         UPPER(e.abr_tercer) AS nom_transp, 
                                         c.tie_contro AS tie_nacion, c.tie_traexp, 
                                         c.tie_traimp, c.tie_tratr1, c.tie_tratr2, 
                                         c.tie_conurb AS tie_urbano, c.tie_desurb, 
                                         c.tie_desnac, c.tie_desimp, c.tie_desexp, 
                                         c.tie_destr1, c.tie_destr2, c.cod_tipser, 
                                         c.ind_llegad, c.ind_salaut, c.cod_server, 
                                         c.ind_estado, c.ind_notage, c.tie_trazab, 
                                         c.tip_factur, c.ind_calcon, c.tie_carurb, 
                                         c.tie_carnac, c.tie_carimp, c.tie_carexp, 
                                         c.tie_cartr1, c.tie_cartr2, c.ind_excala, 
                                         c.ind_camrut, c.val_regist, c.fec_creaci, 
                                         c.usr_creaci, c.can_llaurb, c.can_llanac, 
                                         c.can_llaimp, c.can_llaexp, c.can_llatr1, 
                                         c.can_llatr2, c.ind_conper, c.hor_pe1urb, 
                                         c.hor_pe2urb, c.hor_pe1nac, c.hor_pe2nac, 
                                         c.hor_pe1imp, c.hor_pe2imp, c.hor_pe1exp, 
                                         c.hor_pe2exp, c.hor_pe1tr1, c.hor_pe2tr1, 
                                         c.hor_pe1tr2, c.hor_pe2tr2 
                                    FROM ".BASE_DATOS.".tab_transp_tipser c 
                              INNER JOIN ".BASE_DATOS.".tab_genera_tipser d 
                                      ON c.cod_tipser = d.cod_tipser 
                              INNER JOIN ".BASE_DATOS.".tab_tercer_tercer e 
                                          ON c.cod_transp = e.cod_tercer 
                                      INNER JOIN (    SELECT cod_transp , MAX(num_consec) AS num_consec 
                                                FROM ".BASE_DATOS.".tab_transp_tipser  
                                              GROUP BY cod_transp 
                                             ) f ON c.cod_transp = f.cod_transp AND c.num_consec = f.num_consec
                                        GROUP BY c.cod_transp
                           ) b
                      GROUP BY b.cod_transp 
                  ) a 
            WHERE 1=1 "; 
  $mSql .= $mSqlWhere ? $mSqlWhere : "";

  $mConsult = new Consulta( $mSql, $mConexion );
  return $mResult = $mConsult -> ret_matrix('a');
}

/*! \fn: diffHours
 *  \brief: Retorna la diferencia en minutos de dos horas (H:i:s)
 *  \author: Ing. Fabian Salinas
 *  \date:  01/10/2015
 *  \date modified: dd/mm/aaaa
 *  \modified by: 
 *  \param: mInicia  Time    Hora Inicial (H:i:s)
 *  \param: mFinalx  Time    Hora Final (H:i:s)
 *  \return: Time
 */
function diffHours( $mInicia, $mFinalx ) 
{
  return $mResult = round( abs(strtotime($mFinalx) - strtotime($mInicia)) / 60, 0);
}


# Funciones para que funke la nueva libreria de formulario
/* ********************************* funciones para el parceo de datos ********************** */

/*! \fn GetAttributes( $fAttributes = NULL )
 *  \author Christiam barrera:
 *  \author christiam.barrera@intrared.net
 *  \brief Funcion para parcear los atributos de un metodo o tag en el framework.
 *  \param $fAttributes: string/array cadena o arreglo a parcear segun standar del framework.
 *  \return arreglo asociativo validado segun standar del framework.
 */
function GetAttributes( $mAttributes = NULL )
{
  $mTags = 0;
  if( !$mAttributes )
  {
    if( !isset( $mAttributes["name"] ) )
    {
      $mAttributes["name"] = "tag".$mTags;
      $mTags++;
    }

    if( !isset( $mAttributes["id"] ) )
      $mAttributes["id"] = $mAttributes["name"]."ID";

    if( isset( $mAttributes["multiple"] ) )
      $mAttributes["name"] .= "[]";

    return $mAttributes;
  }
  elseif( is_array( $mAttributes ) )
  {
    if( !isset( $mAttributes["name"] ) )
    {
      $mAttributes["name"] = "tag".$mTags;
      $mTags++;
    }

    if( !isset( $mAttributes["id"] ) )
      $mAttributes["id"] = $mAttributes["name"]."ID";

    if( isset( $mAttributes["multiple"] ) )
      $mAttributes["name"] .= "[]";

    $mTemp = array();
    $mKeys = array_keys( $mAttributes );
    $c = 0;
    foreach( $mAttributes as $mItem )     
    {
      $mTemp[strtolower( $mKeys[$c] )] = $mItem;
      $c++;
    }     
    return $mTemp;
  }
  else
  {
    $mArray = explode( "; ", $mAttributes );
    $mTemp = array();
    foreach( $mArray as $mRow )
    {
      $mAux = explode( ":", $mRow );
      $mTemp[strtolower( trim( $mAux[0] ) )] = $mAux[1];
    }
    if( !isset( $mTemp["name"] ) )
    {
      $mTemp["name"] = "tag".$mTags;
      $mTags++;
    }

    if( !isset( $mTemp["id"] ) )
      $mTemp["id"] = $mTemp["name"]."ID";

    if( isset( $mTemp["multiple"] ) )
      $mTemp["name"] .= "[]";

    unset( $mArray, $mAux  );
    return $mTemp;
  }
}

/*! \fn GetAttributes( $fAttributes = NULL )
 *  \author Carlos A. Mock-kow M.:
 *  \author carlos.mock@intrared.net
 *  \brief Funcion para obtener los datos via get o post.
 *  \param $fNomdata: string nombre del campo.
 *  \param $fDefault: string valor por defecto.
 *  \return valor del compo o el valor por defecto.
 */
function GetData( $fNomData = NULL, $fDefault = NULL )
{
  if( $fNomData != NULL )
  {
    if( array_key_exists( $fNomData, $_POST ) )
    {
      $retorno = $_POST[ $fNomData ];
    }
    elseif( array_key_exists( $fNomData, $_GET ) )
    {
      $retorno = $_GET[ $fNomData ];
    }
    else
    {
      $retorno = $fDefault;
    }
  }
  else
  {
      $retorno = FALSE;
  }
   return $retorno;
}

/*! \fn DataMethod()
 *  \author Carlos A. Mock-kow:
 *  \author carlos.mock@intrared.net
 *  \brief Funcion obtener el metodo por el cual se enviaron los datos.
 *  \return metodo por el cual se enviaron los datos.
 */
function DataMethod()
{
  if( isset( $_GET ) )
  {
      return "get";
  }
  elseif( isset( $_POST ) )
  {
      return "post";
  }
  else
    return FALSE;
}

/*! \fn: lista
   *  \brief: Crea una lista desplegable para el formulario
   *  \author: Ing. Fabian Salinas
   *  \date: 14/07/2015
   *  \date modified: dia/mes/año
   *  \param: mTitulo  String  Titulo del Campo
   *  \param: mNomSel  String  Nombre del Select
   *  \param: mMatriz  Matriz  Matriz con las opciones
   *  \param: mClass  String  Nombre de la clase para el <td>
   *  \param: mObliga  boolean  Si el campo es obligatorio agrega *
   *  \return: 
   */
  function lista( $mTitulo, $mNomSel, $mMatriz, $mClass, $mObliga = 0 )
  {
    $mHtml = '<td class="'.$mClass.'" align="right">'.( $mObliga ? '*' : '' ).$mTitulo.'</td>';

    $mHtml .= '<td class="'.$mClass.'">'; 
    $mHtml .= '<select name="'.$mNomSel.'" id="'.$mNomSel.'ID" onKeypress=buscar_op(this)>';

    $n = sizeof($mMatriz);
    for($i = 0; $i < $n; $i++){
      $mHtml .= '<option value="'.$mMatriz[$i][0].'">'.$mMatriz[$i][1].'</option>';
    }

    $mHtml .= '</select>';
    $mHtml .= '</td>';

    return $mHtml;
  }

/*! \fn: getRealIP
 *  \brief: Develve la dirección real IP de la ubicación del computador cliente con el cual se accede a AVANSAT
 *  \author: TERCEROS (https://ejemplocodigo.com/ejemplo-php-obtener-la-ip-real-de-una-visita/)
 *  \date: 18/09/2014
 *  \return Una cadena con la dirección IP (4 números separados por puntos)
 */
function getRealIP()
{
    if (isset($_SERVER["HTTP_CLIENT_IP"])) {
        return $_SERVER["HTTP_CLIENT_IP"];
    } elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
        return $_SERVER["HTTP_X_FORWARDED_FOR"];
    } elseif (isset($_SERVER["HTTP_X_FORWARDED"])) {
        return $_SERVER["HTTP_X_FORWARDED"];
    } elseif (isset($_SERVER["HTTP_FORWARDED_FOR"])) {
        return $_SERVER["HTTP_FORWARDED_FOR"];
    } elseif (isset($_SERVER["HTTP_FORWARDED"])) {
        return $_SERVER["HTTP_FORWARDED"];
    } else {
        return $_SERVER["REMOTE_ADDR"];
    }
}

/*! \fn: guardarInformacionAcceso
 *  \brief: Permite guardar un registro en la tabla de accesos
 *  \author: Ing. David Rincón
 *  \date: 14/02/2020
 *  \param: 
 *  \return Una cadena con la dirección IP (4 números separados por puntos)
 */
function guardarInformacionAcceso($cod_usuari, $cod_perfil, $conexion) {
    $direccionIP = getRealIP();
    $query = "INSERT INTO ".BASE_DATOS.".tab_bitaco_acceso
                    (cod_usuari, cod_perfil, fec_ingres, url_acceso) 
                VALUES 
                    ('".$cod_usuari."', '".$cod_perfil."', NOW(), '".$direccionIP."')";
    $query = str_replace("''", "NULL", $query);
    $consulta = new Consulta ($query, $conexion);
}



/*! \fn: getTranspPerfil
    *  \brief: Funcion que retorna la empresa transportadora segun el perfil
    *  \author: Cristian Andrés Torres
    *  \date: 2021-08-16
    *  \param:  mConexion = conexion a bd;
    *  \return: Array informacion de la empresa transportadora (consulta)
    */
    function getTranspPerfil(&$mConexion, $cod_perfil){
      $mSql = " SELECT b.cod_tercer, b.nom_tercer, b.dir_domici 
                          FROM ".BASE_DATOS.".tab_aplica_filtro_perfil a
                    INNER JOIN ".BASE_DATOS.".tab_tercer_tercer b
                      ON a.clv_filtro = b.cod_tercer
                where a.cod_perfil = '".$cod_perfil."' AND 
                      a.cod_filtro = '".COD_FILTRO_EMPTRA."'";
      $mConsult = new Consulta( $mSql, $mConexion );
      $mResult = $mConsult -> ret_arreglo();
      return $mResult;
    }

?>