<?php
/***********************************************************************************************************
 * @brief Clase para gestión Asíncrona del Modulo de Notas Contables                                       *
 * @class Ajax                                                                                             *
 * @version 0.1                                                                                            *
 * @ultima_modificacion 28 de Enero de 2010                                                                *
 * @author Christiam Barrera Arango                                                                        *
 * @company Intrared.net LTDA                                                                              *
 ***********************************************************************************************************/
class Ajax
{
  var $conexion      = NULL;
  var $cNull         = array( array( NULL, "--" ) );
  var $cPerfil       = NULL;


  /************************************************************************************************************
   * Metodo Publico Inicializa las variables de la clase y setea los casos de petición                        *               
   * @fn Ajax                                                                                                 *
   * @brief Inicializa las variables de la clase e invoca las acciones provenientes de las opciones de modulo *
   ************************************************************************************************************/
  function Ajax()
  {
    include( "../lib/ajax.inc" );
    $this -> conexion = $AjaxConnection;
    $this -> cPerfil  = $_SESSION["COD_PERFIL"];
    
    switch( $_AJAX["Case"] )
    {
      case 'draw_list' :
        //--------------------------------------
        $_MANIFI = $this -> GetManifi( $_AJAX );
        $_PLACAS = $this -> GetPlacas( $_AJAX );
        $_ORIGEN = $this -> GetOrigen( $_AJAX );
        $_DESTIN = $this -> GetDestin( $_AJAX );
        $_AGEDES = $this -> GetAgedes( $_AJAX );
        //--------------------------------------
        $size = sizeof( $_MANIFI );
        //--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        $h_style  = 'background:#BB0000; color:#ffffff; font-family:Arial; font-size:11px; padding-left:10px; padding-right:10px; border-left:1px solid #ffffff; border-top:1px solid #ffffff;';
        $i_style  = 'background:#D8D8D8; color:#222222; font-family:Arial; font-size:11px; padding-left:10px; padding-right:10px; border-left:1px solid #ffffff; border-top:1px solid #ffffff;';
        $s_style  = 'background:#BB0000; color:#ffffff; font-family:Arial; font-size:11px; font-weight:bold;';
        $c_style1 = 'background:#f2f2f2; color:#333333; font-family:Arial; font-size:11px; padding:3px; border-left:1px solid #ffffff; border-top:1px solid #ffffff;';
        $c_style2 = 'background:#e9e9e9; color:#333333; font-family:Arial; font-size:11px; padding:3px; border-left:1px solid #ffffff; border-top:1px solid #ffffff;';
        //--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        $html  = NULL;
        $html .= '<div align="center" id="ContainerDIV">';
        
        $total = $size == 1 ? 'Se encontró 1 Manifiesto para calificar' : 'Se encontraron ' . $size . ' Manifiestos para calificar';
        $html .= '<div align="left" style="color:#222222; font-family:Arial; font-size:11px; font-weight:bold; padding:5px;">' . $total . '</div>';
        
        $html .= '<table align="center" cellspacing="0" cellpadding="1" width="2300">';
       
        $html .= '<tr>';
        
        $html .= '<th style="'.$h_style.'" align="center" width="50">#</th>';
        $html .= '<th style="'.$h_style.'" align="center" width="85">Manifiesto</th>';
        $html .= '<th style="'.$h_style.'" align="center" width="85" title="Placa">';
        $html .= '<select style="'.$s_style.'" name="num_placax" id="num_placaxID" onchange="DrawList();">';
        $html .= '<option value="">Placa</option>';
        foreach ( $_PLACAS as $ele )
        {
          $selected = $ele['num_placax'] == $_AJAX['num_placax'] ? 'selected="selected"' : NULL;
          $html .= '<option value="'.$ele['num_placax'].'" '.$selected.'>'.$ele['num_placax'].'</option>';
        }
        $html .= '</select>';
        $html .= '</th>';
        $html .= '<th style="'.$h_style.'" align="center" width="100" title="Origen">';
        $html .= '<select style="'.$s_style.'" name="cod_ciuori" id="cod_ciuoriID" onchange="DrawList();">';
        $html .= '<option value="">Origen</option>';
        foreach ( $_ORIGEN as $ele )
        {
          $selected = $ele['cod_ciuori'] == $_AJAX['cod_ciuori'] ? 'selected="selected"' : NULL;
          $html .= '<option value="'.$ele['cod_ciuori'].'" '.$selected.'>'.$ele['nom_ciuori'].'</option>';
        }
        $html .= '</select>';
        $html .= '</th>';
        $html .= '<th style="'.$h_style.'" align="center" width="100" title="Destino">';
        $html .= '<select style="'.$s_style.'" name="cod_ciudes" id="cod_ciudesID" onchange="DrawList();">';
        $html .= '<option value="">Destino</option>';
        foreach ( $_DESTIN as $ele )
        {
          $selected = $ele['cod_ciudes'] == $_AJAX['cod_ciudes'] ? 'selected="selected"' : NULL;
          $html .= '<option value="'.$ele['cod_ciudes'].'" '.$selected.'>'.$ele['nom_ciudes'].'</option>';
        }
        $html .= '</select>';
        $html .= '</th>';
        $html .= '<th style="'.$h_style.'" align="center" width="100" title="Agencia">';
        $html .= '<select style="'.$s_style.'" name="cod_agedes" id="cod_agedesID" onchange="DrawList();">';
        $html .= '<option value="">Agencia</option>';
        foreach ( $_AGEDES as $ele )
        {
          $selected = $ele['cod_agedes'] == $_AJAX['cod_agedes'] ? 'selected="selected"' : NULL;
          $html .= '<option value="'.$ele['cod_agedes'].'" '.$selected.'>'.$ele['nom_agedes'].'</option>';
        }
        $html .= '</select>';
        $html .= '</th>';
        $html .= '<th style="'.$h_style.'" align="center" width="100">Fecha Emisión</th>';
        $html .= '<th style="'.$h_style.'" align="center" width="100">Despacho</th>';
        $html .= '<th style="'.$h_style.'" align="center" width="130">Fecha Llegada</th>';
        $html .= '<th style="'.$h_style.'" align="center" width="250">Conductor</th>';
        $html .= '<th style="'.$h_style.'" align="center" width="100">Celular</th>';
        $html .= '<th style="'.$h_style.'" align="center" width="200">Estado</th>';
        $html .= '<th style="'.$h_style.'" align="center" width="250">Poseedor</th>';
        $html .= '<th style="'.$h_style.'" align="center" width="200">Dirección</th>';
        $html .= '<th style="'.$h_style.'" align="center" width="150">Ciudad</th>';
        $html .= '<th style="'.$h_style.'" align="center" width="100">Teléfono</th>';
        $html .= '<th style="'.$h_style.'" align="center" width="100">Celular</th>';
        
        $html .= '</tr>';
        
        
        for ( $i = 0; $i < $size; $i++ )
        {
          $row = $_MANIFI[$i];
          $row['nom_estado'] = $this -> GetEstado( $row );
          $c_style = $i % 2 == 0 ? $c_style1 : $c_style2;
          
          $html .= '<tr>';
          $html .= '<th style="'.$i_style.'" align="center">'.( $i + 1 ).'</th>';
          $html .= '<th style="'.$c_style.' cursor:pointer; color:#BB0000;" align="center" title="Seleccionar Manifiesto Nro. '.$row['cod_manifi'].'" onclick="SendManifi( this )">'.$row['cod_manifi'].'</th>';
          $html .= '<td style="'.$c_style.'" align="center">'.$row['num_placax'].'</td>';
          $html .= '<td style="'.$c_style.'" align="left"  >'.$row['nom_origen'].'</td>';
          $html .= '<td style="'.$c_style.'" align="left"  >'.$row['nom_destin'].'</td>';
          $html .= '<td style="'.$c_style.'" align="left"  >'.$row['nom_agenci'].'</td>';
          $html .= '<td style="'.$c_style.'" align="center">'.$row['fec_emisio'].'</td>';
          $html .= '<td style="'.$c_style.'" align="center">'.$row['num_despac'].'</td>';
          $html .= '<td style="'.$c_style.'" align="center">'.$row['fec_llegad'].'</td>';
          $html .= '<td style="'.$c_style.'" align="left"  >'.$row['nom_tercer'].'</td>';
          $html .= '<td style="'.$c_style.'" align="center">'.$row['num_telmov'].'</td>';
          $html .= '<td style="'.$c_style.'" align="left"  >'.$row['nom_estado'].'</td>';
          $html .= '<td style="'.$c_style.'" align="left"  >'.$row['nom_tenedo'].'</td>';
          $html .= '<td style="'.$c_style.'" align="left"  >'.$row['dir_domici'].'</td>';
          $html .= '<td style="'.$c_style.'" align="left"  >'.$row['ciu_tenedo'].'</td>';
          $html .= '<td style="'.$c_style.'" align="center">'.$row['num_telef1'].'</td>';
          $html .= '<td style="'.$c_style.'" align="center">'.$row['num_telmov'].'</td>';
          
          
          $html .= '</tr>';
        }
        

        
        $html .= '</table>';

        $html .= '</div>';
        
        echo rawurlencode( $html );
      
      break;
      
      case 'draw_noveda' :
        $i = $_AJAX['i'];
        $c = $_AJAX['c'];
        $_NOVEDA = $this -> GetNovedad( $_AJAX['cod_remesa'], $_AJAX['cod_remisi'] );

        //--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        $h_style  = 'background:#b4b4b4; color:#ffffff; font-family:Arial; font-size:11px; padding:1px; border-left:1px solid #ffffff; border-top:1px solid #ffffff; text-align:center;';
        $c_style1 = 'background:#f2f2f2; color:#222222; font-family:Arial; font-size:11px; padding-left:3px; padding-right:3px; border-left:1px solid #ffffff; border-top:1px solid #ffffff;';
        $c_style2 = 'background:#e8e8e8; color:#222222; font-family:Arial; font-size:11px; padding-left:3px; padding-right:3px; border-left:1px solid #ffffff; border-top:1px solid #ffffff;';
        $i_style  = 'height:19px; font-family:Arial; font-size:11px; color:#222222;';
        $m_style  = 'height:19px; font-family:Arial; font-size:11px; color:#222222; text-align:right';
        $n_style  = 'background:#eaf9ff; color:#222222; font-family:Arial; font-size:11px; padding-left:5px; border-left:1px solid #ffffff; border-top:1px solid #ffffff;';
        
        $z_style  = 'background:#d9d9d9; color:#222222; font-family:Arial; font-size:11px; padding:1px; border-left:1px solid #ffffff; border-top:1px solid #ffffff; text-align:center;';
        $b_style  = 'background:#e9e9e9; color:#333333; font-family:Arial; font-size:11px; padding-left:3px; padding-right:3px; border-left:1px solid #ffffff; border-top:1px solid #ffffff;';
        //$b_style  = 'background:#f0f0f0; color:#333333; font-family:Arial; font-size:11px; padding-left:3px; padding-right:3px; border-left:1px solid #ffffff; border-top:1px solid #ffffff;';
        //--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

        $html .= '<table align="center" width="99%" cellspacing="0" cellpadding="0" style="border: 1px solid #333333;">'; 
          $html .= '<tr>';
            $html .= '<th style="'.$z_style.'" align="center" width="4%" >#</th>';
            $html .= '<th style="'.$z_style.'" align="center" width="20%">Novedad Reportada</th>';
            $html .= '<th style="'.$z_style.'" align="center" width="20%">Valor a Descontar</th>';
            $html .= '<th style="'.$z_style.'" align="center" width="50%">Descripción</th>';
            $html .= '<th style="'.$z_style.'" align="center" width="6%" colspan="2">Acciones</th>';
          $html .= '</tr>';
            
          for ( $f = 0; $f < $_AJAX['size_noveda']; $f++ )
          {
            $html .= '<tr>';
              $html .= '<th style="'.$b_style.'" align="center">'.( $c + 1 ).'.'.( $f + 1 ).'</th>';
              
              $html .= '<td style="'.$b_style.'" align="left">';
              $html .= '<select name="cod_novcum'.$c.'-'.$f.'" id="cod_novcum'.$c.'-'.$f.'ID" style="font-family:Arial; font-size:10px"; color:#333333;"  onchange="OnchangeNoveda( \''.$c.'\' )">';
              $html .= '<option value="">--</option>';
              foreach( $_NOVEDA as $option )
              {
                $selected = $_AJAX['cod_novcum'.$c.'-'.$f] == $option['cod_novcum'] ? 'selected="selected"' : NULL;
                $html .= '<option value="'.$option['cod_novcum'].'" '.$selected.'>'.$option['nom_novcum'].'</option>';
              }
              $html .= '</select>';
              $html .= '</td>';
              
              $html .= '<td style="'.$b_style.'" align="center">';
              $html .= '<input type="text" name="val_novcum'.$c.'-'.$f.'" id="val_novcum'.$c.'-'.$f.'ID" style="'.$m_style.'" maxlength="12" size="10" onkeyup="puntos( this,this.value.charAt( this.value.length-1 ) );" onblur="BlurMoney( this );" value="'.$_AJAX['val_novcum'.$c.'-'.$f].'" />';
              $html .= '</td>';
              
              $html .= '<td style="'.$b_style.'" align="left">';
              $html .= '<input type="text" name="des_novcum'.$c.'-'.$f.'" id="des_novcum'.$c.'-'.$f.'ID" style="'.$i_style.'" maxlength="255" size="70" value="'.$_AJAX['des_novcum'.$c.'-'.$f].'" />';
              $html .= '</td>';
              
              if ( $_AJAX['size_noveda'] > 1 )
              {
                $html .= '<th style="'.$b_style.'" align="center"><img src="../'.DIR_APLICA_CENTRAL.'/conduc/grid_drop.gif" style="cursor:pointer" title="Eliminar Novedad Nro. '.( $c + 1 ).'.'.( $f + 1 ).'" onclick="DrawNoveda( \''.$i.'\',  \''.$c.'\', \''.$f.'\' )" /></th>';
              }
              else
              {
                $html .= '<th style="'.$b_style.'" align="center"><input type="checkbox" disabled="disabled" checked="checked" /></th>';
              }
              if ( $_AJAX['size_noveda'] > $f + 1  )
              {
                $html .= '<th id="act_novcum'.$c.'-'.$f.'ID" style="'.$b_style.'" align="center"><input type="checkbox" disabled="disabled" checked="checked" /></th>';
              }
              else
              {
                $html .= '<th id="act_novcum'.$c.'-'.$f.'ID" style="'.$b_style.'" align="center"><img src="../'.DIR_APLICA_CENTRAL.'/conduc/grid_add.gif" style="cursor:pointer" title="Nueva Novedad para Remisión Nro. '.( $c + 1 ).'" onclick="DrawNoveda( \''.$i.'\',  \''.$c.'\', \'-1\' )" /></th>';
              }
            $html .= '</tr>';
          }
          
        $html .= '</table>';
          
        $html .= '<input type="hidden" name="size_noveda'.$c.'" id="size_noveda'.$c.'ID" value="'.$_AJAX['size_noveda'].'" />';
        
        echo rawurlencode( $html );
        
      break;
 
    }
  }
  

  function GetNovedad( $cod_remesa, $cod_remisi )
  {
    $mSql  = "SELECT a.cod_novcum, a.nom_novcum
                FROM ".BASE_DATOS.".tab_genera_novcum a LEFT JOIN 
                     ".BASE_DATOS.".tab_noveda_cumpli b ON a.cod_novcum = b.cod_novcum 
                                                       AND b.cod_remesa = '".$cod_remesa."'
                                                       AND b.cod_remisi = '".$cod_remisi."'
               WHERE a.cod_novcum != '0' 
                 AND a.cod_novcum NOT IN ( '30', '31' )
                 AND b.cod_novcum IS NULL
               GROUP BY 1 ORDER BY 2 ";
    $mSql = new Consulta( $mSql, $this -> conexion );
    return $mSql -> ret_matriz( "a" );
  }
  

  /************************************************************************************************************
   * Metodo Privado consulta las cuentas del PUC                                                              *
   * @fn ListCuenta                                                                                           *
   * @param $row : String -> Cadena con la Fila de la Grilla                                                  *
   * @return  : array Matriz de datos de cuenta                                                               *
   ************************************************************************************************************/
  function ListCuenta( $row )
  {
    $mSql  = "SELECT CONCAT( a.cod_clasec, a.cod_grupoc, a.cod_cuenta, a.cod_subcue, a.cod_auxili ) AS num_cuenta, 
                     a.nom_cuenta, 
                     IF( a.ind_natura = '1', 'Crédito', 'Débito' ),
                     IF( a.ind_aplter = '1', 'Aplica', 'No Aplica' ), 
                     IF( a.ind_aplcen = '1', 'Aplica', 'No Aplica' ), 
                     IF( a.ind_apldoc = '1', 'Aplica', 'No Aplica' ), 
                     IF( a.ind_aplret = '1', 'Aplica', 'No Aplica' ), 
                     IF( a.ind_apliva = '1', 'Aplica', 'No Aplica' ), 
                     IF( a.ind_aplica = '1', 'Aplica', 'No Aplica' ), 
                     
                     IF( a.ind_natura = '1', '1', '0' ) AS ind_natura, 
                     IF( a.ind_aplter = '1', '1', '0' ) AS ind_aplter, 
                     IF( a.ind_aplcen = '1', '1', '0' ) AS ind_aplcen, 
                     IF( a.ind_apldoc = '1', '1', '0' ) AS ind_apldoc, 
                     IF( a.ind_aplret = '1', '1', '0' ) AS ind_aplret, 
                     IF( a.ind_apliva = '1', '1', '0' ) AS ind_apliva, 
                     IF( a.ind_aplica = '1', '1', '0' ) AS ind_aplica  

                FROM ".C_CONSULTOR.".tab_genera_plancu a 
               WHERE 1 = 1 
                 AND a.cod_auxili <> '00' ";
      if ( $row < 2 )
      {
        $mSql .= " AND a.ind_aplret = '0' 
                   AND a.ind_apliva = '0' 
                   AND a.ind_aplica = '0' ";
      }
      else
      {
        $mSql .= " AND ( a.ind_aplret = '1' OR a.ind_apliva = '1' OR a.ind_aplica = '1' ) ";
      }
      return $mSql;
  }
  
  
  /************************************************************************************************************
   * Metodo Privado consulta la información de una cuenta del PUC                                             *
   * @fn ListCuentas                                                                                          *
   * @param $num_cuenta : String -> Cadena con el Número de la Cuenta                                         *
   * @param $row        : String -> Cadena con el Número de la Fila de la Grilla                              *
   * @return  : array Matriz de datos de cuenta                                                               *
   ************************************************************************************************************/
  function GetCuenta( $num_cuenta, $row )
  {
    $mSql  = "SELECT CONCAT( a.cod_clasec, a.cod_grupoc, a.cod_cuenta, a.cod_subcue, a.cod_auxili ) AS num_cuenta, 
                     a.nom_cuenta, 
                     IF( a.ind_natura = '1', '1', '0' ) AS ind_natura, 
                     IF( a.ind_natura = '1', 'Crédito', 'Débito' ) AS lab_natura,
                     IF( a.ind_aplret = '1', '1', '0' ) AS ind_aplret, 
                     IF( a.ind_apliva = '1', '1', '0' ) AS ind_apliva, 
                     IF( a.ind_aplica = '1', '1', '0' ) AS ind_aplica  
                FROM ".C_CONSULTOR.".tab_genera_plancu a 
               WHERE 1 = 1 ";

    $mSql .= " AND CONCAT( a.cod_clasec, a.cod_grupoc, a.cod_cuenta, a.cod_subcue, a.cod_auxili ) = '$num_cuenta' ";
    $mSql .= " AND a.cod_auxili <> '00' ";
    if ( $row < 2 )
    {
      $mSql .= " AND a.ind_aplret = '0' 
                 AND a.ind_apliva = '0' 
                 AND a.ind_aplica = '0' ";
    }
    else
    {
      $mSql .= " AND ( a.ind_aplret = '1' OR a.ind_apliva = '1' OR a.ind_aplica = '1' ) ";
    }
    $mSql = new Consulta( $mSql, $this -> conexion );
    $mSql = $mSql -> ret_matriz( "a" );
    return $mSql[0];
  }
  
  
  /************************************************************************************************************
   * Metodo Privado que consulta la existencia de un registro unico parametrizado                             *
   * @fn GetRetefu                                                                                            *
   * @return: array -> Matrix Asociativa de Retenciones en la Fuente                                          *
   * @brief                                                                                                   *
   ************************************************************************************************************/
  function GetRetefu()
  {
    $mSql = "SELECT a.cod_retefu, a.nom_retefu, CONCAT( a.val_porret, '%' ) AS val_porret 
               FROM ".C_CONSULTOR.".tab_genera_retefu a
              WHERE a.ano_retefu = '".date( 'Y' )."' ORDER BY 2 ";
    $mSql = new Consulta( $mSql, $this -> conexion );
    return $mSql -> ret_matriz( "a" );
  }
  
  
  /************************************************************************************************************
   * Metodo Privado que consulta la existencia de un registro unico parametrizado                             *
   * @fn GetRetiva                                                                                            *
   * @return: array -> Matrix Asociativa de Retenciones de ICA                                                *
   * @brief                                                                                                   *
   ************************************************************************************************************/
  function GetRetiva()
  {
    $mSql = "SELECT a.cod_retiva, a.nom_retiva, CONCAT( a.val_poriva, '%' ) AS val_poriva 
               FROM ".C_CONSULTOR.".tab_genera_retiva a
              WHERE a.ano_vigiva = '".date( 'Y' )."' ORDER BY 2 ";
    $mSql = new Consulta( $mSql, $this -> conexion );
    return $mSql -> ret_matriz( "a" );
  }
  
  
  /************************************************************************************************************
   * Metodo Privado que consulta la existencia de un registro unico parametrizado                             *
   * @fn GetRetica                                                                                            *
   * @return: array -> Matrix Asociativa de Retenciones de ICA                                                *
   * @brief                                                                                                   *
   ************************************************************************************************************/
  function GetRetica()
  {
    $mSql = "SELECT a.cod_retica, a.nom_retica, CONCAT( a.val_porica, '%' ) AS val_porica 
               FROM ".C_CONSULTOR.".tab_genera_retica a
              WHERE a.ano_retica = '".date( 'Y' )."' ORDER BY 2 ";
    $mSql = new Consulta( $mSql, $this -> conexion );
    return $mSql -> ret_matriz( "a" );
  }
  
  
  /************************************************************************************************************
   * Metodo Privado que consulta la existencia de un registro único parametrizado                             *
   * @fn GetParameNotcon                                                                                      *
   * @param : $mData, arreglo $_POST                                                                          *
   * @return: array -> Matrix Asociativa con la Información de una Parametrización de Nota Contable           *
   * @brief                                                                                                   *
   ************************************************************************************************************/       
  function GetParameNotcon( $mData )
  {
    $mData['cod_bancox'] = '0';
    $mSql = "SELECT CONCAT( a.cod_clasec, a.cod_grupoc, a.cod_cuenta, a.cod_subcue, a.cod_auxili ) AS num_cuenta, 
                    a.ind_nattra, a.cod_retefu, a.cod_retiva, a.cod_retica,
                    b.nom_cuenta, b.ind_natura, b.ind_aplret, b.ind_apliva, b.ind_aplica, 
                    IF ( b.ind_natura = '0', 'Débito', 'Crédito' ) AS nom_natura, 
                    IF ( b.ind_natura = '0', 'D', 'C' ) AS lab_natura,
                    CONCAT( c.val_porret, '%' ) AS val_porret,
                    CONCAT( d.val_poriva, '%' ) AS val_poriva, 
                    CONCAT( e.val_porica, '%' ) AS val_porica 
               FROM ".BASE_DATOS. ".tab_compro_cuenta a, 
                    ".C_CONSULTOR.".tab_genera_plancu b LEFT JOIN
                    ".C_CONSULTOR.".tab_genera_retefu c ON ( a.cod_retefu = c.cod_retefu ) LEFT JOIN 
                    ".C_CONSULTOR.".tab_genera_retiva d ON ( a.cod_retiva = d.cod_retiva ) LEFT JOIN 
                    ".C_CONSULTOR.".tab_genera_retica e ON ( a.cod_retica = e.cod_retica ) 
              WHERE a.cod_tiptra = '".$mData['cod_tiptra']."' 
                AND a.cod_tipcom = '".$mData['cod_tipcom']."' 
                AND a.cod_notcon = '".$mData['cod_notcon']."' 
                AND a.cod_bancox = '".$mData['cod_bancox']."' 
                AND a.cod_agenci = '".$mData['cod_agenci']."' 
                AND a.cod_clasec = b.cod_clasec 
                AND a.cod_grupoc = b.cod_grupoc 
                AND a.cod_cuenta = b.cod_cuenta 
                AND a.cod_subcue = b.cod_subcue 
                AND a.cod_auxili = b.cod_auxili 
              GROUP BY CONCAT( a.cod_clasec, a.cod_grupoc, a.cod_cuenta, a.cod_subcue, a.cod_auxili ) 
              ORDER BY a.num_consec 
                ";
    $mSql = new Consulta( $mSql, $this -> conexion );
    return $mSql -> ret_matriz( 'a' );
  }
  
  
  
  
  /************************************************************************************************************
   * Metodo Privado que retorna el SQL de Consulta de los Manifiestos por Cumplir                             *
   * @fn GetManifi                                                                                            *
   * @return String : Cadena SQL                                                                              *
   * @brief realiza las relaciones entre las tablas involucradas para detallar la información del listado     *
   ************************************************************************************************************/
  function GetManifi( $mData = NULL )
  {
    $mSql  = "SELECT a.cod_manifi, a.num_placax, b.abr_ciudad AS nom_origen, 
                     c.abr_ciudad AS nom_destin, j.nom_agenci, 
                     DATE_FORMAT( a.fec_emisio, '%Y-%m-%d' ) AS fec_emisio, 
                     d.num_despac, d.fec_llegad, 
                     f.abr_tercer AS nom_tercer, f.num_telmov, g.nom_agenci AS abr_agenci, 
                     h.abr_tercer AS nom_tenedo, 
                     h.dir_domici, i.abr_ciudad AS ciu_tenedo, h.num_telef1, h.num_telmov, 
                     DATE_FORMAT( d.fec_llegad, '%Y:%m:%d' ) AS fec_llegad1,
                     DATE_FORMAT( d.fec_llegad, '%H:%i:%s' ) AS fec_llegad2  
                FROM ".BASE_DATOS.".tab_manifi_cargax a, 
                     ".BASE_DATOS.".tab_despac_despac d, 
                     ".BASE_DATOS.".tab_genera_ciudad b, 
                     ".BASE_DATOS.".tab_genera_ciudad c, 
                     ".BASE_DATOS.".tab_vehicu_vehicu e, 
                     ".BASE_DATOS.".tab_tercer_tercer f, 
                     ".BASE_DATOS.".tab_genera_agenci g, 
                     ".BASE_DATOS.".tab_tercer_tercer h, 
                     ".BASE_DATOS.".tab_genera_ciudad i,
                     ".BASE_DATOS.".tab_genera_agenci j  
               WHERE a.cod_manifi = d.cod_manifi 
                 AND a.cod_ciuori = b.cod_ciudad 
                 AND a.cod_ciudes = c.cod_ciudad 
                 AND a.num_placax = e.num_placax 
                 AND a.cod_conduc = f.cod_tercer 
                 AND a.cod_agenci = g.cod_agenci 
                 AND a.cod_tenedo = h.cod_tercer 
                 AND h.cod_ciudad = i.cod_ciudad 
                 AND a.cod_agedes = j.cod_agenci 
                 AND a.ind_anulad <> 'S' 
                 /*AND a.ind_cumpli = 'N' */
                 AND a.ind_califi = 0
                 AND d.ind_dester = '1' 
                 AND d.fec_salida IS NOT NULL 
                 AND d.fec_llegad IS NOT NULL ";
    $mSql .= $mData['num_placax'] == NULL ? NULL : " AND a.num_placax = '".$mData['num_placax']."' ";
    $mSql .= $mData['cod_ciuori'] == NULL ? NULL : " AND a.cod_ciuori = '".$mData['cod_ciuori']."' ";
    $mSql .= $mData['cod_ciudes'] == NULL ? NULL : " AND a.cod_ciudes = '".$mData['cod_ciudes']."' ";
    $mSql .= $mData['cod_agedes'] == NULL ? NULL : " AND a.cod_agedes = '".$mData['cod_agedes']."' ";
    $mSql = new Consulta( $mSql, $this -> conexion );
    return $mSql -> ret_matriz( 'a' );
  }
  
  
  function GetPlacas( $mData = NULL )
  {
    $mSql  = "SELECT a.num_placax, a.num_placax
                FROM ".BASE_DATOS.".tab_manifi_cargax a, 
                     ".BASE_DATOS.".tab_despac_despac d 
               WHERE a.cod_manifi = d.cod_manifi 
                 /*AND a.ind_cumpli = 'N'*/ 
                 AND a.ind_anulad <> 'S' 
                 AND d.ind_dester = '1' 
                 AND d.fec_salida IS NOT NULL 
                 AND d.fec_llegad IS NOT NULL ";
    $mSql .= $mData['cod_ciuori'] == NULL ? NULL : " AND a.cod_ciuori = '".$mData['cod_ciuori']."' ";
    $mSql .= $mData['cod_ciudes'] == NULL ? NULL : " AND a.cod_ciudes = '".$mData['cod_ciudes']."' ";
    $mSql .= $mData['cod_agedes'] == NULL ? NULL : " AND a.cod_agedes = '".$mData['cod_agedes']."' ";
    $mSql .= " GROUP BY 1, 2 ORDER BY 1 ";
    $mSql = new Consulta( $mSql, $this -> conexion );
    return $mSql -> ret_matriz( 'a' );
  }
  
  
  function GetOrigen( $mData = NULL )
  {
    $mSql  = "SELECT b.cod_ciudad AS cod_ciuori, b.nom_ciudad AS nom_ciuori 
                FROM ".BASE_DATOS.".tab_manifi_cargax a, 
                     ".BASE_DATOS.".tab_genera_ciudad b,
                     ".BASE_DATOS.".tab_despac_despac d 
               WHERE a.cod_manifi = d.cod_manifi 
                 AND a.cod_ciuori = b.cod_ciudad 
                 /*AND a.ind_cumpli = 'N' */
                 AND d.ind_dester = '1' 
                 AND a.ind_anulad <> 'S' 
                 AND d.fec_salida IS NOT NULL 
                 AND d.fec_llegad IS NOT NULL ";
    $mSql .= $mData['num_placax'] == NULL ? NULL : " AND a.num_placax = '".$mData['num_placax']."' ";
    $mSql .= $mData['cod_ciudes'] == NULL ? NULL : " AND a.cod_ciudes = '".$mData['cod_ciudes']."' ";
    $mSql .= $mData['cod_agedes'] == NULL ? NULL : " AND a.cod_agedes = '".$mData['cod_agedes']."' ";
    $mSql .= " GROUP BY 1, 2 ORDER BY 1 ";
    $mSql = new Consulta( $mSql, $this -> conexion );
    return $mSql -> ret_matriz( 'a' );
  }
  
  
  function GetDestin( $mData = NULL )
  {
    $mSql  = "SELECT b.cod_ciudad AS cod_ciudes, b.nom_ciudad AS nom_ciudes 
                FROM ".BASE_DATOS.".tab_manifi_cargax a, 
                     ".BASE_DATOS.".tab_genera_ciudad b,
                     ".BASE_DATOS.".tab_despac_despac d 
               WHERE a.cod_manifi = d.cod_manifi 
                 AND a.cod_ciudes = b.cod_ciudad 
                 /*AND a.ind_cumpli = 'N' */
                 AND d.ind_dester = '1' 
                 AND a.ind_anulad <> 'S' 
                 AND d.fec_salida IS NOT NULL 
                 AND d.fec_llegad IS NOT NULL ";
    $mSql .= $mData['num_placax'] == NULL ? NULL : " AND a.num_placax = '".$mData['num_placax']."' ";
    $mSql .= $mData['cod_ciuori'] == NULL ? NULL : " AND a.cod_ciuori = '".$mData['cod_ciuori']."' ";
    $mSql .= $mData['cod_agedes'] == NULL ? NULL : " AND a.cod_agedes = '".$mData['cod_agedes']."' ";
    $mSql .= " GROUP BY 1, 2 ORDER BY 1 ";
    $mSql = new Consulta( $mSql, $this -> conexion );
    return $mSql -> ret_matriz( 'a' );
  }
  
  
  function GetAgedes( $mData = NULL )
  {
    $mSql  = "SELECT b.cod_agenci AS cod_agedes, b.nom_agenci AS nom_agedes 
                FROM ".BASE_DATOS.".tab_manifi_cargax a,
                     ".BASE_DATOS.".tab_despac_despac d,
                     ".BASE_DATOS.".tab_genera_agenci b
               WHERE a.cod_manifi = d.cod_manifi 
                 AND a.cod_agenci = b.cod_agenci 
                 /*AND a.ind_cumpli = 'N' */
                 AND a.ind_anulad <> 'S' 
                 AND d.ind_dester = '1' 
                 AND d.fec_salida IS NOT NULL 
                 AND d.fec_llegad IS NOT NULL ";
    $mSql .= $mData['num_placax'] == NULL ? NULL : " AND a.num_placax = '".$mData['num_placax']."' ";
    $mSql .= $mData['cod_ciuori'] == NULL ? NULL : " AND a.cod_ciuori = '".$mData['cod_ciuori']."' ";
    $mSql .= $mData['cod_ciudes'] == NULL ? NULL : " AND a.cod_ciudes = '".$mData['cod_ciudes']."' ";
    $mSql .= " GROUP BY 1, 2 ORDER BY 1 ";
    $mSql = new Consulta( $mSql, $this -> conexion );
    return $mSql -> ret_matriz( 'a' );
  }
  
  
  function GetEstado( $mData )
  {
    $fec_actual = date( "Y:m:d" );
    $tie_actual = date( "H:i:s" );
  
    if ( $mData['fec_llegad1'] < $fec_actual )
    {
      $mSql = " SELECT ( DATEDIFF( '".$fec_actual."', '".$mData['fec_llegad1']."' ) ) AS diferencia ";
    }
    else
    {
      $mSql = " SELECT ( DATEDIFF( '".$mData['fec_llegad1']."', '".$fec_actual."' ) ) AS diferencia ";
    }
    $consulta = new Consulta( $mSql, $this -> conexion );
    $diaven = $consulta -> ret_matriz( 'a' );
    $diaven = $diaven[0];

    $mSql = " SELECT TIMEDIFF('".$tie_actual."', '".$mData['fec_llegad2']."' ) AS tiempo ";
    $consulta = new Consulta( $mSql, $this -> conexion );
    $horven = $consulta -> ret_matriz( 'a' );
    $horven = $horven[0];
    
    if( $mData['fec_llegad1'] < $fec_actual )
    {
      return '<font color="#BB0000">' . $diaven['diferencia'] . " día(s) con " . $horven['tiempo'] . '</font>';
    }
    else
    {
      return $diaven['diferencia'] . " día(s) con " . $horven['tiempo'];
    }
  }
  
  
  
  
  
}

$ajax = new Ajax();
?>