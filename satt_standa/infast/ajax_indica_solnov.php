<?php
ini_set('display_errors', false);
session_start();

class AjaxParametrizar
{
  var $conexion;
  var $Week = array(
                      'L' => 'Lunes',
                      'M' => 'Martes',
                      'X' => 'Mi&eacute;rcoles',
                      'J' => 'Jueves',
                      'V' => 'Viernes',
                      'S' => 'S&aacute;bado',
                      'D' => 'Domingo'
                   );

  var $Year = array(
                      '01' => 'Enero',
                      '02' => 'Febrero',
                      '03' => 'Marzo',
                      '04' => 'Abril',
                      '05' => 'Mayo',
                      '06' => 'Junio',
                      '07' => 'Julio',
                      '08' => 'Agosto',
                      '09' => 'Septiembre',
                      '10' => 'Octubre',
                      '11' => 'Noviembre',
                      '12' => 'Diciembre'
                   );

  public function __construct()
  {
    $_AJAX = $_REQUEST;
    include_once('../lib/bd/seguridad/aplica_filtro_perfil_lib.inc');
    include_once('../lib/bd/seguridad/aplica_filtro_usuari_lib.inc');
    include_once('../lib/ajax.inc');
    include_once('../lib/general/constantes.inc');
    $this -> conexion = $AjaxConnection;
    $this -> $_AJAX['option']( $_AJAX );
  }
  
  

  protected function ValidateTransp( $_AJAX )
  {
    $mSql = "SELECT 1
               FROM ".BASE_DATOS.".tab_tercer_tercer a,
                    ".BASE_DATOS.".tab_tercer_activi b
              WHERE a.cod_tercer = b.cod_tercer AND
                    b.cod_activi = ".$_AJAX['filter']." AND
                    a.cod_tercer = '". trim($_AJAX['cod_transp']) ."'";
    
    $consulta = new Consulta( $mSql, $this -> conexion );
    $transpor = $consulta -> ret_matriz();
    if( sizeof( $transpor ) > 0 )
      echo 'y';
    else
      echo 'n';
  }

  protected function getTransp( $_AJAX )
  {
    $mSql = "SELECT a.cod_tercer, UPPER( a.abr_tercer ) as abr_tercer
               FROM ".BASE_DATOS.".tab_tercer_tercer a,
                    ".BASE_DATOS.".tab_tercer_activi b
              WHERE a.cod_tercer = b.cod_tercer AND
                    b.cod_activi = ".$_AJAX['filter']." AND
                    CONCAT( a.cod_tercer ,' - ', UPPER( a.abr_tercer ) ) LIKE '%". $_AJAX['term'] ."%'
           ORDER BY 2 
              LIMIT 10";
    
    $consulta = new Consulta( $mSql, $this -> conexion );
    $transpor = $consulta -> ret_matriz();
    
    $data = array();
    for($i=0, $len = count($transpor); $i<$len; $i++){
       $data [] = '{"label":"'.$transpor[$i][0].' - '.$transpor[$i][1].'","value":"'. $transpor[$i][0].' - '.$transpor[$i][1].'"}'; 
    }
    echo '['.join(', ',$data).']'; 
  }

  protected function CreateConfig( $mData )
  {
    $mSelect = "SELECT com_diasxx 
                  FROM ".BASE_DATOS.".tab_config_horlab
                 WHERE cod_tercer = '".$mData['cod_tercer']."' 
                   AND ind_config = '".$mData['ind_config']."' 
                   AND cod_ciudad = '".$mData['cod_ciudad']."' ";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_CONFIG = $consulta -> ret_matriz();

    $mActual = '';
    foreach( $_CONFIG as $row )
      $mActual .= $mActual != '' ? '|'.$row['com_diasxx'] : $row['com_diasxx'];
  
    $mArrayActual = array();
    foreach( explode( '|', $mActual ) as $llave )
      $mArrayActual[] = $llave;

    echo '<script>
          $( "#hor_ingresID, #hor_salidaID" ).timepicker();
          
          $.mask.definitions["H"]="[012]";
          $.mask.definitions["N"]="[012345]";
          $.mask.definitions["n"]="[0123456789]";
          
          $( "#hor_ingresID, #hor_salidaID" ).mask("Hn:Nn:Nn");
          </script>';

    $mHtml =  '<center><div class="StyleDIV">';
      $mHtml .= '<table id="weekTable" width="50%" cellspacing="0" cellpadding="0">';
        $mHtml .= '<tr>';
          $mHtml .= '<td colspan="4" style="border:1px solid #35650F;" class="CellHead" align="center"><b>DIAS DE LA SEMANA AUN NO PARAMETRIZADOS</b></td>';
        $mHtml .= '</tr>';
        
        $count = 0;
        $contador = 0;
        foreach( $this -> Week as $mDay => $mDia )
        {
          if( !in_array( $mDay, $mArrayActual ) )
          {
            if( $count == 0 )
              $mHtml .= '<tr>';
            
            $mStyleCP = $contador % 2 == 0 ? '' : 'border-right:1px solid #35650F;';  
            $mStyleCS = $contador % 2 == 0 ? 'border-left:1px solid #35650F;' : '';  
            
            $mHtml .= '<td width="40%" style="'.$mStyleCS.'" class="cellInfo1">'.$this -> Week[$mDay].'</td>';
            $mHtml .= '<td width="10%" style="'.$mStyleCP.'" class="cellInfo1" align="center"><input type="checkbox" name="nom_diasxx" value="'.$mDay.'" /></td>';
            $count++;
            $contador++;
            
            if( $count > 1 )
            {
              $mHtml .= '</tr>'; 
              $count = 0;
            }
          }
        }
        if( $contador % 2 == 1 )
        {
          $mHtml .= '<td width="40%" style="border-bottom:1px solid #35650F;" class="cellInfo1">&nbsp;</td>';
          $mHtml .= '<td width="10%" style="border-right:1px solid #35650F;border-bottom:1px solid #35650F; "class="cellInfo1" align="center">&nbsp;</td>';
        }
      $mHtml .= '</table>';
      
      $mHtml .= '<br><br>';
      
      $mHtml .= '<table width="50%" cellspacing="0" cellpadding="0">';
        $mHtml .= '<tr>';
          $mHtml .= '<td style="border:1px solid #35650F;" class="CellHead" align="center"><b>HORARIO DE ENTRADA</b></td>';
          $mHtml .= '<td style="border:1px solid #35650F;" class="CellHead" align="center"><b>HORARIO DE SALIDA</b></td>';
        $mHtml .= '</tr>';
        
        $mHtml .= '<tr>';
          $mHtml .= '<td style="border-left:1px solid #35650F; border-bottom:1px solid #35650F;" class="cellInfo1" align="center"><input type="text" name="hor_ingres" id="hor_ingresID" value="07:00:00" /></td>';
          $mHtml .= '<td style="border-right:1px solid #35650F;border-bottom:1px solid #35650F;" class="cellInfo1" align="center"><input type="text" name="hor_salida" id="hor_salidaID" value="12:00:00" /></td>';
        $mHtml .= '</tr>';
      $mHtml .= '</table>';
      $mHtml .= '<input type="hidden" id="cod_usuariID" name="cod_usuari" value="'.$mData['cod_tercer'].'" />';
    $mHtml .= '</div></center>';

    echo $mHtml;
    echo "<script>
          $('#weekTable tr:last').find('td').each (function() {
            $(this).css({
              'border-bottom': '1px solid #35650F'
            });
          }); 
          </script>";
  }

  protected function VerifyEstado( $cod_tercer, $ind_config, $cod_ciudad )
  {
    $mSelect = "SELECT com_diasxx 
                  FROM ".BASE_DATOS.".tab_config_horlab
                 WHERE cod_tercer = '".$cod_tercer."' 
                   AND ind_config = '".$ind_config."' 
                   AND cod_ciudad = '".$cod_ciudad."' ";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_CONFIG = $consulta -> ret_matriz();

    $mActual = '';
    foreach( $_CONFIG as $row )
      $mActual .= $mActual != '' ? '|'.$row['com_diasxx'] : $row['com_diasxx'];
  
    $mArrayActual = array();
    foreach( explode( '|', $mActual ) as $llave )
      $mArrayActual[] = $llave;
    
    $verifica = false;
    foreach( $this -> Week as $mDay => $mDia )
    {
      if( !in_array( $mDay, $mArrayActual ) )
      {
        $verifica = true;
      }
    }
    return $verifica;
  }

  protected function NewParametrizacion( $mData )
  {
    $mInsert = "INSERT INTO ".BASE_DATOS.".tab_config_horlab
                          ( cod_tercer, com_diasxx, hor_ingres, 
                            hor_salida, usr_creaci, fec_creaci, 
                            ind_config, cod_ciudad
                   )VALUES( '".$mData['cod_usuari']."', '".$mData['nue_combin']."', '".$mData['hor_ingedi']."', 
                            '".$mData['hor_saledi']."', '".$_SESSION['datos_usuario']['cod_usuari']."', NOW(),
                            '".$mData['ind_config']."', '".$mData['cod_ciudad']."' 
                          )";
    
    if( $consulta = new Consulta( $mInsert, $this -> conexion ) )
    {
      echo "1000";
    }
    else
    {
      echo "9999";
    }
  }

  protected function MainForm( $mData )
  {
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";
    $mHtml  .= '<center>';
      $mHtml  .= '<table width="100%" cellspacing="2px" cellpadding="0">';
    
      $mHtml .= '<tr>';
        $mHtml .= '<td colspan="4" style=" text-align:center;border-bottom:1px solid #000000; font-family:Trebuchet MS, Verdana, Arial; font-size:18px; font-weight:bold; padding-bottom:5px; color: #285C00;" ><i>'.str_replace('/-/','&',utf8_decode($mData['nom_transp'])).'</i></td>';
      $mHtml .= '</tr>';
    
      $mHtml .= '</table>';
      $mHtml .= '</center><br><div id="AlarmaID" style="display:none;"></div>';

    $mHtml  .= '<center>';
      $mHtml  .= '<br><div id="filtrosID"><table width="100%" cellspacing="1" cellpadding="0">';
        
        $comp = $this -> VerifyEstado( $mData['cod_transp'], $_REQUEST['ind_config'], $_REQUEST['cod_ciudad'] ) ? '&nbsp;&nbsp;&nbsp;&nbsp;<a onclick="CreateConfig( \''.$mData['cod_transp'].'\', \''.$mData['ind_config'].'\', \''.$mData['cod_ciudad'].'\' )" style="cursor:pointer;text-decoration:none;color:#FFFFFF;">[ Nueva Configuraci&oacute;n ]</a>' : '';
        $mHtml  .= '<tr>';
        $mHtml  .= '<td align="center" colspan="4" class="CellHead" style="padding:5px;"><b>HORARIOS LABORALES</b>'.$comp.'</td>';  
        $mHtml  .= '</tr>';
        
        $mSelect = "SELECT com_diasxx, hor_ingres, hor_salida, cod_tercer
                      FROM ".BASE_DATOS.".tab_config_horlab 
                     WHERE cod_tercer = '".$mData['cod_transp']."' 
                       AND ind_config = '".$mData['ind_config']."' 
                       AND cod_ciudad = '".$mData['cod_ciudad']."' "; 
        
        $consulta = new Consulta( $mSelect, $this -> conexion );
        $_CONFIG = $consulta -> ret_matriz();

        if( sizeof( $_CONFIG ) > 0 )
        {
          $mHtml  .= '<tr>';
            $mHtml  .= '<td class="CellHead" align="center">D&iacute;as</td>';  
            $mHtml  .= '<td class="CellHead" align="center">Hora Inicial</td>';  
            $mHtml  .= '<td class="CellHead" align="center">Hora Final</td>';  
            $mHtml  .= '<td class="CellHead" align="center">Opciones</td>';  
          $mHtml  .= '</tr>';
          
          $count = 0;
          foreach( $_CONFIG as $row )
          {
            
            $mDiasxx = '';
            foreach( explode( '|', $row['com_diasxx'] ) as $nameWeek )
              $mDiasxx .= $mDiasxx != ''? ', '.$this -> Week[$nameWeek] : $this -> Week[$nameWeek];
            
            $mStyle = $count % 2 == 0 ? 'cellInfo1' : 'cellInfo2'; 
            $mHtml  .= '<tr>';
              $mHtml  .= '<td class="'.$mStyle.'" align="center">'.$mDiasxx.'</td>';  
              $mHtml  .= '<td class="'.$mStyle.'" align="center">'.$this -> HoraLegible( $row['hor_ingres'] ).'</td>';  
              $mHtml  .= '<td class="'.$mStyle.'" align="center">'.$this -> HoraLegible( $row['hor_salida'] ).'</td>';  
              $mHtml  .= '<td class="'.$mStyle.'" align="center"><a onclick="EditConfig( \''.$row['cod_tercer'].'\', \''.$row['com_diasxx'].'\', \''.$mData['ind_config'].'\', \''.$mData['cod_ciudad'].'\' )" style="cursor:pointer;text-decoration:none;">GESTIONAR</a></td>';    
            $mHtml  .= '</tr>';
            $count++;
          }
        }
        else
        {
          $mHtml  .= '<tr>';
            $mHtml  .= '<td class="cellInfo1" align="center">ACTUALMENTE LA EMPRESA NO TIENE UNA CONFIGURACI&Oacute;N PARAMETRIZADA.</td>';  
          $mHtml  .= '</tr>';
        }
      
        $mHtml  .= '</table><br><br>';
        
        $mHtml  .= '<table width="100%" cellspacing="2px" cellpadding="0">';
          $mHtml  .= '<tr>';
            $comp = '';
            $mHtml  .= '<td align="center" colspan="4" class="CellHead" style="padding:5px;"><b>FESTIVOS POR A&Ntilde;O</b>'.$comp.'</td>';  
          $mHtml  .= '</tr>';
          
          $mSelect =  '<select name="sel_yearxx" id="sel_yearxxID" onchange="SetFestivos( $(this) );">';
          $mSelect .= '<option value="">-Seleccione-</option>';
          $mYear = date('Y');
          for( $i = $mYear; $i < ($mYear+5); $i++)
            $mSelect .= '<option value="'.$i.'">'.$i.'</option>';
          
          $mSelect .= '</select>';
          
          $mHtml  .= '<tr>';
            $mHtml  .= '<td align="center" colspan="4" class="cellInfo1">Seleccione el a&ntilde;o:&nbsp;&nbsp;&nbsp;&nbsp;'.$mSelect.'</td>';  
            $mHtml  .= "<input type='hidden' name='ind_config' id='ind_configID' value='".$mData['ind_config']."'>";
            $mHtml  .= "<input type='hidden' name='cod_ciudad' id='cod_ciudadID' value='".$mData['cod_ciudad']."'>";
          $mHtml  .= '</tr>';

          $mHtml  .= '<tr>';
          $mHtml  .= '<td align="center" colspan="4" class="cellInfo1"><div id="FestivosID" class="Style2DIV" style="display:none;">&nbsp;</div></td>';  
          $mHtml  .= '</tr>';
        
        $mHtml  .= '</table>';

        $mHtml  .= '</div>';
    $mHtml  .= '</center>';
    
    echo $mHtml;
  }

  protected function getFestivos( $mData )
  {
    echo '<script>
          $( "#fec_insertID" ).datepicker();
          
          $.mask.definitions["A"]="[12]";
          $.mask.definitions["M"]="[01]";
          $.mask.definitions["D"]="[0123]";
          
          $.mask.definitions["H"]="[012]";
          $.mask.definitions["N"]="[012345]";
          $.mask.definitions["n"]="[0123456789]";
          
          $( "#fec_insertID" ).mask("Annn-Mn-Dn");

          $( "input[type=button]").button();
          </script>';

    $mHtml  = '<center>';
      $mHtml  .= '<table width="20%" cellspacing="2px" cellpadding="0">';
    
      $mHtml .= '<tr>';
        $mHtml .= '<td><input type="text" name="fec_insert" id="fec_insertID" size="10"/></td>';
        $mHtml .= '<td><input type="button" name="Registrar" value="Registrar" onclick="InsertFestivo( \''.$mData['cod_transp'].'\', \''.$mData['ind_config'].'\', \''.$mData['cod_ciudad'].'\' );"/></td>';
      $mHtml .= '</tr>';
    
      $mHtml .= '</table>';

    $mSelect = "SELECT fec_festiv
                  FROM ".BASE_DATOS.".tab_config_festiv 
                 WHERE cod_tercer = '".$mData['cod_transp']."' 
                   AND ind_config = '".$mData['ind_config']."' 
                   AND cod_ciudad = '".$mData['cod_ciudad']."' 
                   AND YEAR( fec_festiv ) = '".$mData['sel_yearxx']."' ";

    $consulta = new Consulta( $mSelect, $this -> conexion );
    $mFechas = $consulta -> ret_matriz();

    $mHtml  .= '<table width="100%" cellspacing="1" cellpadding="0">';

    if( sizeof( $mFechas ) > 0 )
    {
      $mMesesConfig = array();
      foreach( $mFechas as $row )
      {
        $eFecha = explode( '-', $row['fec_festiv'] );
        $mMesesConfig[ $eFecha[1] ] .= $mMesesConfig[ $eFecha[1] ] != '' ? '|'.$eFecha[2]: $eFecha[2]; 
      }
      
      $mHtml .= '<tr>';
        $mHtml .= '<td colspan="6" align="center" class="CellHead">FESTIVOS PARAMETRIZADOS DE A&Ntilde;O '.$mData['sel_yearxx'].'<br>Para Eliminar un Festivo Haga Click sobre el dia dentro del Calendario</td>';
      $mHtml .= '</tr>';

      $count = 0;
      $contador = 0;

      foreach( $this -> Year as $numAno => $nomAno )
      {
        $mDayofMonth = array();
        if( $count == 0 )
        {
          $mHtml .= '<tr>';
          $mConten = '';
          $mConten .= '<tr>';
        }
        
        $count++;
        $contador++;
        
        $mEdit = '';
        $MInter = '<br>';
        if( sizeof( $mMesesConfig[ $numAno ] ) > 0 )
        {
          $mDayofMonth = explode( '|', $mMesesConfig[ $numAno ] );
        }



        $MInter = '<table width="100%" style="padding-top:10px;padding-left:5px;padding-right:5px;padding-bottom:10px;" cellpadding="0" cellspacing="1">';
          $MInter .= '<tr>';
           $MInter .= '<td width="14%" style="border-bottom:2px solid #000000;" align="center"><b>L</b></td>';
           $MInter .= '<td width="14%" style="border-bottom:2px solid #000000;" align="center"><b>M</b></td>';
           $MInter .= '<td width="14%" style="border-bottom:2px solid #000000;" align="center"><b>X</b></td>';
           $MInter .= '<td width="14%" style="border-bottom:2px solid #000000;" align="center"><b>J</b></td>';
           $MInter .= '<td width="14%" style="border-bottom:2px solid #000000;" align="center"><b>V</b></td>';
           $MInter .= '<td width="14%" style="border-bottom:2px solid #000000;" align="center"><b>S</b></td>';
           $MInter .= '<td width="14%" style="border-bottom:2px solid #000000;" align="center"><b>D</b></td>';
          $MInter .= '</tr>';

        $mLimit = $this -> ValidateNumberofDays( $numAno, $mData['sel_yearxx'] );
        
        $count2 = 0;
        
        for( $i = 1, $consec = 1; $consec <= $mLimit; $i++ )
        {
          if( $count2 == 0 )
            $MInter .= '<tr>';

          $diaSemana = date( "N", mktime( 0, 0, 0, $numAno, $consec, $mData['sel_yearxx'] ) ); 
          
          if( $diaSemana > $i )
          {  
            $MInter .= '<td width="14%" style="padding-right:10px;padding-top:3px;" align="right">&nbsp;</td>';
          }  
          else
          { 
            $add = '';
            $link = $consec;
            $click = '';
            if( in_array( $consec, $mDayofMonth ) )
            {
              $add = 'cursor:pointer;color:#C00000;font-weight: bold;background-color:#FFD5D5;';
              $click = 'onclick="deleteFestivo(\''.$mData['cod_transp'].'\', \''.$mData['ind_config'].'\', \''.$mData['cod_ciudad'].'\', \''.$mData['sel_yearxx'].'\', \''.$numAno.'\', \''.$consec.'\');"';
            }
            $MInter .= '<td width="14%" '.$click.' style="'.$add.'border:1px solid #ADADAD;padding-right:10px;padding-top:3px;" align="right">'.$link.'</td>';
            $consec++;
          }
          
          $count2++;

          if( $count2 > 6 )
          {
            $MInter .= '</tr>';
            $count2 = 0;
          }       
        }
        $MInter .= '</table>';
           
        $mHtml .= '<td width="16%" style="border:1px solid #35650F;" align="center" class="CellHead">'.$nomAno.'&nbsp;'.$mEdit.'</td>';
        $mConten .= '<td valign="top" style="margin:0;padding:0;border:1px solid #35650F;">'.$MInter.'</td>';
             
        if( $count > 5 )
        {
          $mHtml .= '</tr>'; 
          $mConten .= '</tr>';
          $mHtml .= $mConten;
          $count = 0;
        }
      }
    }
    else
    {
      $mHtml .= '<tr>';
        $mHtml .= '<td colspan="6" align="center" class="CellHead">NO HAY FESTIVOS PARAMETRIZADOS PARA EL A&Ntilde;O '.$mData['sel_yearxx'].'</td>';
      $mHtml .= '</tr>';
    }
    $mHtml .= '</table>';
    $mHtml  .= '</center>';

    echo $mHtml;
  }

  protected function deleteFestivo( $mData )
  {
    if( strlen( $mData['dia'] ) == '1' )
      $mData['dia'] = '0'.$mData['dia'];

    $mFechaDelete = $mData['ano'].'-'.$mData['mes'].'-'.$mData['dia'];
    
    $mDelete = "DELETE FROM ".BASE_DATOS.".tab_config_festiv 
                 WHERE cod_tercer = '".$mData['cod_transp']."' 
                   AND ind_config = '".$mData['ind_config']."' 
                   AND cod_ciudad = '".$mData['cod_ciudad']."' 
                   AND fec_festiv = '".$mFechaDelete."'  ";
    
    if( $consulta = new Consulta( $mDelete, $this -> conexion ) )
    {
      echo "1000";
    }
    else
    {
      echo "9999";
    }
  }

  private function ValidateNumberofDays( $mMes, $mAno )
  {
    $mTotal = 0;
    switch ( (int)$mMes ) 
    {
      case 1:
      case 3:
      case 5:
      case 7:
      case 8:
      case 10:
      case 12:
        $mTotal = 31;
      break;
      
      case 4:
      case 6:
      case 9:
      case 11:
        $mTotal = 30;
      break;
        
      case 2:
      if( date( 'L', mktime( 1, 1, 1, 1, 1, $mAno ) ) == '1' )
        $mTotal = 29;
      else
        $mTotal = 28;
      break;
    }
    
    return $mTotal; 
  }

  protected function InsertFestivo( $mData )
  {
    $mSelect = "SELECT 1 
                  FROM ".BASE_DATOS.".tab_config_festiv
                 WHERE cod_tercer = '".$mData['cod_transp']."' 
                   AND ind_config = '".$mData['ind_config']."' 
                   AND cod_ciudad = '".$mData['cod_ciudad']."' 
                   AND fec_festiv = '".$mData['fec_insert']."' ";

    $consulta = new Consulta( $mSelect, $this -> conexion );
    $mExiste = $consulta -> ret_matriz();

    if( sizeof( $mExiste ) > 0 )
    {
      $result = '9999';
    }
    else
    {
      $mInsert = "INSERT INTO ".BASE_DATOS.".tab_config_festiv
               ( cod_tercer, fec_festiv, ind_config, cod_ciudad )
         VALUES( '".$mData['cod_transp']."', '".$mData['fec_insert']."', '".$mData['ind_config']."', '".$mData['cod_ciudad']."' ) ";
      if( $consulta = new Consulta( $mInsert, $this -> conexion ) )
        $result = '1000';
      else
        $result = '1991';
    }
    echo $result;
  }

  protected function EditForm( $mData )
  {
    echo '<script>
          $( "#hor_ingediID, #hor_salediID" ).timepicker();
          
          $.mask.definitions["H"]="[012]";
          $.mask.definitions["N"]="[012345]";
          $.mask.definitions["n"]="[0123456789]";
          
          $( "#hor_ingediID, #hor_salediID" ).mask("Hn:Nn:Nn");
          </script>';
    
    $mSelect = "SELECT com_diasxx, hor_ingres, hor_salida, cod_tercer
                      FROM ".BASE_DATOS.".tab_config_horlab 
                     WHERE cod_tercer = '".$mData['cod_tercer']."'
                       AND com_diasxx = '".$mData['cod_diasxx']."'  
                       AND ind_config = '".$mData['ind_config']."' 
                       AND cod_ciudad = '".$mData['cod_ciudad']."' "; 
        
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_CONFIG = $consulta -> ret_matriz();

    $mConfig = $_CONFIG[0];

    $mArrInd = array();
    foreach( explode( '|', $mData['cod_diasxx'] ) as $key )
      $mArrInd[] = $key;

    $mHtml =  '<center><div class="StyleDIV">';
      $mHtml .= '<table width="50%" cellspacing="0" cellpadding="0">';
        $mHtml .= '<tr>';
          $mHtml .= '<td colspan="4" style="border:1px solid #35650F;" class="CellHead" align="center"><b>DIAS DE LA SEMANA</b></td>';
        $mHtml .= '</tr>';
        
        $mHtml .= '<tr>';
          $mHtml .= '<td width="40%" style="border-left:1px solid #35650F;" class="cellInfo1">LUNES</td>';
          $checked = in_array( 'L', $mArrInd ) ? 'checked' : '';
          $mHtml .= '<td width="10%" class="cellInfo1" align="center"><input type="checkbox" name="nom_diaxxx" value="L" '.$checked.'/></td>';
          $mHtml .= '<td width="40%" class="cellInfo1">MARTES</td>';
          $checked = in_array( 'M', $mArrInd ) ? 'checked' : '';
          $mHtml .= '<td width="10%" style="border-right:1px solid #35650F;" class="cellInfo1" align="center"><input type="checkbox" name="nom_diaxxx" value="M" '.$checked.'/></td>';
        $mHtml .= '</tr>';
        
        $mHtml .= '<tr>';
          $mHtml .= '<td width="40%" style="border-left:1px solid #35650F;" class="cellInfo2">MI&Eacute;RCOLES</td>';
          $checked = in_array( 'X', $mArrInd ) ? 'checked' : '';
          $mHtml .= '<td width="10%" class="cellInfo2" align="center"><input type="checkbox" name="nom_diaxxx" value="X" '.$checked.'/></td>';
          $mHtml .= '<td width="40%" class="cellInfo2">JUEVES</td>';
          $checked = in_array( 'J', $mArrInd ) ? 'checked' : '';
          $mHtml .= '<td width="10%" style="border-right:1px solid #35650F;" class="cellInfo2" align="center"><input type="checkbox" name="nom_diaxxx" value="J" '.$checked.'/></td>';
        $mHtml .= '</tr>';
        
        $mHtml .= '<tr>';
          $mHtml .= '<td width="40%" style="border-left:1px solid #35650F;" class="cellInfo1">VIERNES</td>';
          $checked = in_array( 'V', $mArrInd ) ? 'checked' : '';
          $mHtml .= '<td width="10%" class="cellInfo1" align="center"><input type="checkbox" name="nom_diaxxx" value="V" '.$checked.'/></td>';
          $mHtml .= '<td width="40%" class="cellInfo1">S&Aacute;BADO</td>';
          $checked = in_array( 'S', $mArrInd ) ? 'checked' : '';
          $mHtml .= '<td width="10%" style="border-right:1px solid #35650F;"class="cellInfo1" align="center"><input type="checkbox" name="nom_diaxxx" value="S" '.$checked.'/></td>';
        $mHtml .= '</tr>';
        
        $mHtml .= '<tr>';
          $mHtml .= '<td width="40%" style="border-left:1px solid #35650F;border-bottom:1px solid #35650F;" class="cellInfo2">DOMINGO</td>';
          $checked = in_array( 'D', $mArrInd ) ? 'checked' : '';
          $mHtml .= '<td width="10%" style="border-bottom:1px solid #35650F;"class="cellInfo2" align="center"><input type="checkbox" name="nom_diaxxx" value="D" '.$checked.'/></td>';
          $mHtml .= '<td width="40%" style="border-bottom:1px solid #35650F;" class="cellInfo2">&nbsp;</td>';
          $mHtml .= '<td width="10%" style="border-right:1px solid #35650F;border-bottom:1px solid #35650F;"class="cellInfo2" align="center">&nbsp;</td>';
        $mHtml .= '</tr>';
      $mHtml .= '</table>';
      
      $mHtml .= '<br><br>';
      
      $mHtml .= '<table width="50%" cellspacing="0" cellpadding="0">';
        $mHtml .= '<tr>';
          $mHtml .= '<td style="border:1px solid #35650F;" class="CellHead" align="center"><b>HORARIO DE ENTRADA</b></td>';
          $mHtml .= '<td style="border:1px solid #35650F;" class="CellHead" align="center"><b>HORARIO DE SALIDA</b></td>';
        $mHtml .= '</tr>';
        
        $mHtml .= '<tr>';
          $mHtml .= '<td style="border-left:1px solid #35650F; border-bottom:1px solid #35650F;" class="cellInfo1" align="center"><input type="text" name="hor_ingedi" id="hor_ingediID" value="'.$mConfig['hor_ingres'].'" /></td>';
          $mHtml .= '<td style="border-right:1px solid #35650F;border-bottom:1px solid #35650F;" class="cellInfo1" align="center"><input type="text" name="hor_saledi" id="hor_salediID" value="'.$mConfig['hor_salida'].'" /></td>';
        $mHtml .= '</tr>';
      $mHtml .= '</table>';
      $mHtml .= '<input type="hidden" id="com_diasxxID" name="com_diasxx" value="'.$mConfig['com_diasxx'].'" />';
      $mHtml .= '<input type="hidden" id="cod_usuariID" name="cod_usuari" value="'.$mConfig['cod_tercer'].'" />';
    $mHtml .= '</div></center>';

    echo $mHtml; 
  }

  protected function DropParametrizacion( $mData )
  {
    $mDelete = "DELETE FROM ".BASE_DATOS.".tab_config_horlab 
                 WHERE cod_tercer = '".$mData['cod_usuari']."' 
                   AND com_diasxx = '".$mData['com_diasxx']."' 
                   AND ind_config = '".$mData['ind_config']."' 
                   AND cod_ciudad = '".$mData['cod_ciudad']."' ";
    
    if( $consulta = new Consulta( $mDelete, $this -> conexion ) )
    {
      echo "1000";
    }
    else
    {
      echo "9999";
    }
  }

  protected function ChangeParametrizacion( $mData )
  {
    $consulta = new Consulta("SELECT 1", $this -> conexion, "BR");

    $mDelete = "DELETE FROM ".BASE_DATOS.".tab_config_horlab 
                 WHERE cod_tercer = '".$mData['cod_usuari']."' 
                   AND com_diasxx = '".$mData['com_diasxx']."' 
                   AND ind_config = '".$mData['ind_config']."' 
                   AND cod_ciudad = '".$mData['cod_ciudad']."' ";
    $consulta = new Consulta( $mDelete, $this -> conexion, "R");

    $mInsert = "INSERT INTO ".BASE_DATOS.".tab_config_horlab
                          ( cod_tercer, com_diasxx, hor_ingres, 
                            hor_salida, usr_creaci, fec_creaci, 
                            ind_config, cod_ciudad 
                   )VALUES( '".$mData['cod_usuari']."', '".$mData['nue_combin']."', '".$mData['hor_ingedi']."', 
                            '".$mData['hor_saledi']."', '".$_SESSION['datos_usuario']['cod_usuari']."', NOW(),
                            '".$mData['ind_config']."', '".$mData['cod_ciudad']."' 
                          )";
    $consulta = new Consulta( $mInsert, $this -> conexion, "R" );
    
    if( $insercion = new Consulta( "COMMIT" , $this -> conexion ) )
    {
      echo "1000";
    }
    else
    {
      echo "9999";
    }
  }

  private function HoraLegible( $mHoraxx )
  {
    $mDetalle = explode( ':', $mHoraxx );
    
    $ind = 'am';
    if( (int)$mDetalle[0] > 12 )
    {
      $rem = '0'.(int)$mDetalle[0] - 12;
      $mDetalle[0] = $rem;
      $ind = 'pm';
    }
    return $mDetalle[0].':'.$mDetalle[1].':'.$mDetalle[2].' '.$ind;
  }

  /*! \fn: getCiudadTransp
   *  \brief: Trae las ciudades asociadas como origen a una transportadora
   *  \author: Ing. Fabian Salinas
   *  \date:  28/05/2015
   *  \date modified: dd/mm/aaaa
   *  \modified by: 
   *  \param: mData
   *  \return: Matriz
   */
  protected function getCiudadTransp( $mData )
  {
    $mSql = "SELECT a.cod_ciudad, 
                    CONCAT( UPPER(b.abr_ciudad), ' (', LEFT(c.nom_depart, 4), ') - ', LEFT(d.nom_paisxx, 3) ) AS nom_ciudad
               FROM ".BASE_DATOS.".tab_transp_origen a 
         INNER JOIN ".BASE_DATOS.".tab_genera_ciudad b 
                 ON a.cod_ciudad = b.cod_ciudad 
         INNER JOIN ".BASE_DATOS.".tab_genera_depart c 
                 ON b.cod_depart = c.cod_depart 
                AND b.cod_paisxx = c.cod_paisxx 
         INNER JOIN ".BASE_DATOS.".tab_genera_paises d 
                 ON b.cod_paisxx = d.cod_paisxx 
              WHERE a.cod_transp = '".$mData['cod_transp']."' 
           ORDER BY b.abr_ciudad ";
    $mConsult = new Consulta( $mSql, $this -> conexion );
    $mResult = $mConsult -> ret_matrix('a');

    if( $mData['Ajax'] == 'on' ){
      $mHtml  = "<select class='campo_texto' name='cod_ciudad' id='cod_ciudadID'>";
        $mHtml .= "<option value=''>---</option>";

        foreach ($mResult as $row)
          $mHtml .= "<option value='".$row['cod_ciudad']."'>".$row['nom_ciudad']."</option>";

      $mHtml .= "</select>";

      echo $mHtml;
    }
    else
      return $mResult;
  }
}

$proceso = new AjaxParametrizar();

?>