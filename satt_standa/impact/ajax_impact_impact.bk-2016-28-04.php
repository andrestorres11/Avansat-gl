<?php

class AjaxInsertImpacto
{
  var $conexion;
  public function __construct()
  {
    $_AJAX = $_REQUEST;
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
  
  protected function MainForm( $_AJAX )
  {
    $this -> Style();
    
    echo "<script>
          $('#colorpicker').ColorPicker({
            onSubmit: function(hsb, hex, rgb, el)
            {
              $(el).val(hex);
              $('#muestraID').css('backgroundColor', '#' + hex);
              $(el).ColorPickerHide();
            },
            onBeforeShow: function () 
            {
              $(this).ColorPickerSetColor(this.value);
            }
          })
          .bind('keyup', function(){
            $(this).ColorPickerSetColor(this.value);
          });
          </script>";

    $mHtml  = '<table width="100%" border="0" cellpadding="0" cellspacing="0">'; 
    $mHtml .= '<tr>';
      $mHtml .= '<td align="center" width="100%" colspan="4" style="color:#257038; font-family:Trebuchet MS, Verdana, Arial; font-size:20px;" >TABLA DE IMPACTOS<hr></td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '<tr>';
      $mHtml .= '<td align="right" width="20%" class="TRform" ><b> * Descripci&oacute;n:&nbsp;&nbsp;</b></td>';
      $mHtml .= '<td align="left"  width="20%" class="TRform" ><input type="text" name="des_impact" id="des_impactID" size="45" /></td>';
      $mHtml .= '<td align="right" width="20%" class="TRform" ><b> * Color:&nbsp;&nbsp;</b></td>';
      $mHtml .= '<td align="left"  width="40%" class="TRform" ><input type="text" maxlength="6" size="7" id="colorpicker" value="ffffff" readonly />&nbsp;<span id="muestraID" style="border:1px #000000 solid; background:#FFFFFF none repeat scroll 0 0;">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>';
    $mHtml .= '</tr>';    

    $mHtml .= '<tr>';
      $mHtml .= '<td align="right" width="20%" class="TRform" ><b> * Rango Inicial:&nbsp;&nbsp;</b></td>';
      $mHtml .= '<td align="left"  width="20%" class="TRform" ><input name="ran_inicia" id="ran_iniciaID" maxlength="3" size="3" onkeypress="return NumericInput( event );" /></td>';
      $mHtml .= '<td align="right" width="20%" class="TRform" ><b> * Rango Final:&nbsp;&nbsp;</b></td>';
      $mHtml .= '<td align="left"  width="40%" class="TRform" ><input name="ran_finali" id="ran_finaliID" maxlength="3" size="3" onkeypress="return NumericInput( event );" /></td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '<tr>';
      $mHtml .= '<td align="center" width="100%" colspan="4" style="font-family:Trebuchet MS, Verdana, Arial;" ><br><input class="crmButton small save" type="button" onclick="SaveImpacto(\''.$_AJAX['cod_transp'].'\');" value="Registrar" name="Registrar"></td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '</table>';
    
    $_IMPACTO = $this -> getImpacto( $_AJAX['cod_transp'] );
    
    $mHtml .= '<br><div id="ImpactoID" class="Style2DIV">';
    
    $mHtml .= '<table width="80%" border="0" cellpadding="0" cellspacing="0">';
    if( sizeof( $_IMPACTO ) > 0 )
    {
      $mHtml .= '<tr>';
        $mHtml .= '<td align="center" width="50%" class="CellHead" >DESCRIPCI&Oacute;N</td>';
        $mHtml .= '<td align="center" width="5%" class="CellHead" >COLOR</td>';
        $mHtml .= '<td align="center" width="10%" class="CellHead" >RANGO M&Iacute;NIMO (Mins)</td>';
        $mHtml .= '<td align="center" width="10%" class="CellHead" >RANGO M&Aacute;XIMO (Mins)</td>';
        $mHtml .= '<td align="center" width="25%" class="CellHead" >OPCIONES</td>';
      $mHtml .= '</tr>';
      
      $i = 0;
      foreach( $_IMPACTO as $row )
      {
        $class = $i % 2 == 0 ? 'cellInfo1' : 'cellInfo2';
        $mHtml .= '<tr>';
          $mHtml .= '<td align="center" width="50%" class="'.$class.'" >'.utf8_encode( $row['des_impact'] ).'</td>';
          $mHtml .= '<td align="center" width="5%" style="border:1px #000000 solid;background-color:#'.$row['cod_colorx'].'" class="'.$class.'" >&nbsp</td>';
          $mHtml .= '<td align="center" width="10%" class="'.$class.'" >'.$row['min_ranini'].'</td>';
          $mHtml .= '<td align="center" width="10%" class="'.$class.'" >'.$row['min_ranfin'].'</td>';
          $mHtml .= '<td align="center" width="25%" class="'.$class.'" ><a href="#" style="text-decoration:none;" onclick="EditImpacto( \''.$_AJAX['cod_transp'].'\', \''.$row['cod_impact'].'\');" >Editar</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="#" style="text-decoration:none;" onclick="DropImpacto(\''.$_AJAX['cod_transp'].'\', \''.$row['cod_impact'].'\');" >Eliminar</a></td>';
      $mHtml .= '</tr>';
        $i++;
      }
    }
    else
    {
      $mHtml .= '<tr>';
        $mHtml .= '<td align="center" width="100%" colspan="4" class="CellHead" >NO EXISTEN REGISTROS</td>';
      $mHtml .= '</tr>';
    }
    $mHtml .= '</table>';
    
    $mHtml .= '</div>';
    $mHtml .= '<br><div id="FormEditID" class="Style2DIV" style="display:none;">';
    $mHtml .= '</div>';
    
    /* MATRIZ DE COMUNICACION */
      
    $mSql = "SELECT num_consec, cod_ciuori, cod_ciudes, 
                    cod_produc, cod_tipdes, ema_conpri,
                    ema_otrcon 
               FROM ".BASE_DATOS.".tab_impact_matcom 
              WHERE cod_transp = '".$_AJAX['cod_transp']."' ORDER BY num_consec";
    
    $consulta = new Consulta( $mSql, $this -> conexion );
    $_CONTACTOS = $consulta -> ret_matriz();

    $mHtml .= '<div id="messageID" style="display:none;"></div>';
    $mHtml .= '<br><br><table id="matcomID" width="100%" border="0" cellpadding="0" cellspacing="0">';    
      
    $mHtml .= '<tr>'; 
      $mHtml .= '<td align="center" class="cellHead" width="100%" colspan="7">CONTACTOS ASIGNADOS&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a onclick="AddGrid();" style="text-decoration:none; cursor:pointer; color:#FFFFFF;">[Agregar Otro]</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a onclick="SaveMatcom(\''.$_AJAX['cod_transp'].'\');" style="text-decoration:none; cursor:pointer; color:#E7E975;"><b>[GUARDAR CAMBIOS]</b></a></td>';
    $mHtml .= '</tr>'; 
      
    if( sizeof( $_CONTACTOS ) > 0 )
    {
      $_AJAX['counter'] = 0;
      foreach( $_CONTACTOS as $row )
      {
        $_AJAX['counter']++;
        $mHtml .= $this -> generateDynamicGrid( $_AJAX, $row );
      }
    }
    else
    {
       $_AJAX['counter'] = 1;
       $mHtml .= $this -> generateDynamicGrid( $_AJAX );
    }
    
    $mHtml .= '</table>';
    $mHtml .= '<input type="hidden" name="counter" id="counterID" value="'.$_AJAX['counter'].'" />';
    echo $mHtml;
  }
  
  private function VerifyContacto( $cod_transp, $cod_ciuori, $cod_ciudes, $cod_produc, $cod_tipdes )
  {
    $mSelect = "SELECT 1 FROM tab_impact_matcom WHERE cod_tipdes = '".$cod_tipdes."' AND cod_transp = '".$cod_transp."'";
    
    if( $cod_ciuori == '' )
      $mSelect .= " AND cod_ciuori = '0' ";
    else
      $mSelect .= " AND cod_ciuori = '".$cod_ciuori."'";
  
    if( $cod_ciudes == '' )
      $mSelect .= " AND cod_ciudes = '0' ";
    else
      $mSelect .= " AND cod_ciudes = '".$cod_ciudes."'";
  
    if( $cod_produc == '' )
      $mSelect .= " AND cod_produc = '' ";
    else
      $mSelect .= " AND cod_produc = '".$cod_produc."'";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $MATCOM = $consulta -> ret_matriz();
    
    if( sizeof( $MATCOM ) > 0 )
      return false;
    else
      return true;
  }
  
  protected function SaveMatcom( $_AJAX )
  {
    // echo "<pre>";
    // print_r( $_AJAX );
    // echo "</pre>";
    
    $mDelete = "DELETE FROM ". BASE_DATOS .".tab_impact_matcom WHERE cod_transp = '".$_AJAX['cod_transp']."'";
    $consulta = new Consulta( $mDelete, $this -> conexion, "BR" );
    
    for( $i = 0; $i < $_AJAX['counter']; $i++ )
    {
      if( $this -> VerifyContacto( $_AJAX['cod_transp'], $_AJAX['cod_ciuori'.$i], $_AJAX['cod_ciudes'.$i], $_AJAX['cod_produc'.$i], $_AJAX['cod_tipdes'.$i] ) )
      {
        $mSelect = "SELECT MAX(num_consec) FROM ". BASE_DATOS .".tab_impact_matcom WHERE cod_transp = '".$_AJAX['cod_transp']."'";
        $consulta = new Consulta( $mSelect, $this -> conexion, "R" );
        $consec = $consulta -> ret_matriz();
        $cod_produc = str_replace( '/-/', '&', $_AJAX['cod_produc'.$i] );
        
        $mInsert = "INSERT INTO ". BASE_DATOS .".tab_impact_matcom
                              ( cod_transp, num_consec, cod_ciuori, cod_ciudes,
                                cod_produc, cod_tipdes, ema_conpri, ema_otrcon,
                                usr_creaci, fec_creaci
                              )
                        VALUES( '".$_AJAX['cod_transp']."', '".($consec[0][0]+1)."', '".$_AJAX['cod_ciuori'.$i]."', '".$_AJAX['cod_ciudes'.$i]."',  
                                '".$cod_produc."', '".$_AJAX['cod_tipdes'.$i]."', '".$_AJAX['ema_conpri'.$i]."', '".$_AJAX['ema_otrcon'.$i]."', 
                                '".$_SESSION['datos_usuario']['cod_usuari']."', NOW()
                              )";
        $consulta = new Consulta( $mInsert, $this -> conexion, "R" );
      }
    }
    
    if(  $insercion = new Consulta( "COMMIT", $this -> conexion ) )
    {
      echo "y";
    }
    else
    {
      echo "n";
    }
  
  }
  
  private function generateDynamicGrid( $_AJAX, $values = NULL )
  {
    $ciuori = $this -> getCiudad( );
    $ciudes = $this -> getCiudad( );
    $produc = $this -> getProduc( );
    $tipdes = $this -> getTipdes( );
    
    $_AJAX['counter'] -= 1; 
    $style = $_AJAX['counter'] % 2 == 0 ? 'cellInfo1' : 'cellInfo2' ;
    $mHtml = '<tr>'; 
    $mHtml .= '<td align="center" class="'.$style.'">Origen:<br>'.$this -> GenerateSelect( $ciuori, 'cod_ciuori'.$_AJAX['counter'], $values['cod_ciuori'], NULL ).'</td>';
    $mHtml .= '<td align="center" class="'.$style.'">Destino:<br>'.$this -> GenerateSelect( $ciudes, 'cod_ciudes'.$_AJAX['counter'], $values['cod_ciudes'], NULL ).'</td>';
    $mHtml .= '<td align="center" class="'.$style.'">Producto:<br>'.$this -> GenerateSelect( $produc, 'cod_produc'.$_AJAX['counter'], $values['cod_produc'], NULL ).'</td>';
    $mHtml .= '<td align="center" class="'.$style.'">* Tipo Despacho:<br>'.$this -> GenerateSelect( $tipdes, 'cod_tipdes'.$_AJAX['counter'], $values['cod_tipdes'], NULL ).'</td>';
    $mHtml .= '<td align="center" class="'.$style.'">Contacto Principal:<br><textarea name="ema_conpri'.$_AJAX['counter'].'" id="ema_conpri'.$_AJAX['counter'].'ID" cols="15" rows="1" >'.$values['ema_conpri'].'</textarea></td>';
    $mHtml .= '<td align="center" class="'.$style.'">Otros Contactos:<br><textarea name="ema_otrcon'.$_AJAX['counter'].'" id="ema_otrcon'.$_AJAX['counter'].'ID" cols="15" rows="1" >'.$values['ema_otrcon'].'</textarea></td>';
    $mHtml .= '</tr>'; 
    
    if( $_AJAX['ind_ajax'] == 'yes' )
      echo $mHtml;
    else
      return $mHtml;
  }
  
  private function GenerateSelect( $arr_select, $name, $key = NULL, $events = NULL  )
  {
    $mHtml  = '<select style="font-family:Trebuchet MS, Verdana, Arial;font-size:10px;" name="'.$name.'" id="'.$name.'ID" '.$events.'>';
    $mHtml .= '<option value="">- Seleccione -</option>';
    foreach( $arr_select as $row )
    {
      $selected = '';
      if( $row[0] == $key )
        $selected = 'selected="selected"';
      
      $mHtml .= '<option value="'.$row[0].'" '.$selected.'>'.utf8_encode( $row[1] ).'</option>';
    }
    $mHtml .= '</select>';
    return $mHtml;
  }
  
  private function getCiudad( $cod_ciudad = NULL )
  {
    $mSql = "SELECT a.cod_ciudad, CONCAT( UPPER( a.nom_ciudad ), '- (', LEFT(b.abr_depart, 4), ')' ) AS nom_ciudad
               FROM ".BASE_DATOS.".tab_genera_ciudad a,
                    ".BASE_DATOS.".tab_genera_depart b
              WHERE a.ind_estado = '1'
                AND a.cod_depart  = b.cod_depart 
              ";
    if( $cod_ciudad != NULL && $cod_ciudad != '' )
    {
      $mSql .= " AND cod_ciudad = ".$cod_ciudad;      
    }
    $mSql .= " ORDER BY 2";
    $consulta = new Consulta( $mSql, $this -> conexion );
		return $consulta -> ret_matriz();
  } 
  
  private function getProduc( $cod_produc = NULL )
  {
    $mSql = "SELECT cod_produc, UPPER( nom_produc ) AS nom_produc
               FROM ".BASE_DATOS.".tab_genera_produc 
              WHERE ind_estado = '1'";
    if( $cod_produc != NULL && $cod_produc != '' )
    {
      $mSql .= " AND cod_produc = '".$cod_produc."'";      
    }
    $mSql .= " ORDER BY 2";
    $consulta = new Consulta( $mSql, $this -> conexion );
		return $consulta -> ret_matriz();
  }
  
  private function getTipdes( $cod_tipdes = NULL )
  {
    $mSql = "SELECT cod_tipdes, UPPER( nom_tipdes ) AS nom_tipdes
               FROM ".BASE_DATOS.".tab_genera_tipdes 
              WHERE 1 = 1";
    if( $cod_tipdes != NULL && $cod_tipdes != '' )
    {
      $mSql .= " AND cod_tipdes = '".$cod_tipdes."'";      
    }
    $mSql .= " AND cod_tipdes != '5' ORDER BY 2";
    $consulta = new Consulta( $mSql, $this -> conexion );
		return $consulta -> ret_matriz();
  }
  
  
  protected function SendEditImpacto( $_AJAX )
  {
    
    $mUpdate = "UPDATE ".BASE_DATOS.".tab_genera_impact 
                   SET des_impact = '".$_AJAX['des_impact']."', 
                       cod_colorx = '".$_AJAX['cod_colorx']."', 
                       min_ranini = '".$_AJAX['ran_inicia']."', 
                       min_ranfin = '".$_AJAX['ran_finali']."', 
                       usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."', 
                       fec_modifi = NOW()
                 WHERE cod_transp = '".$_AJAX['cod_transp']."'
                   AND cod_impact = '".$_AJAX['cod_impacto']."'"; 
                            
    $consulta = new Consulta( $mUpdate, $this -> conexion );
    
  }
  
  protected function DropImpacto( $_AJAX )
  {
    $mDelete = "DELETE FROM ".BASE_DATOS.".tab_genera_impact WHERE cod_transp = '".$_AJAX['cod_transp']."' AND cod_impact = '".$_AJAX['cod_impacto']."'"; 
                            
    $consulta = new Consulta( $mDelete, $this -> conexion );
  }
  
  protected function EditImpacto( $_AJAX )
  {
    $_IMPACT = $this -> getImpacto( $_AJAX['cod_transp'], $_AJAX['cod_impacto'] );

    echo "<script>
          $('#colorpicker2').ColorPicker({
            onSubmit: function(hsb, hex, rgb, el)
            {
              $(el).val(hex);
              $('#muestra2ID').css('backgroundColor', '#' + hex);
              $(el).ColorPickerHide();
            },
            onBeforeShow: function () 
            {
              $(this).ColorPickerSetColor(this.value);
            }
          })
          .bind('keyup', function(){
            $(this).ColorPickerSetColor(this.value);
          });
          </script>";
    
    $this -> Style();

    $mHtml  = '<div id="FormEditID" class="StyleDIV">';
    $mHtml .= '<table width="100%" border="0" cellpadding="0" cellspacing="0">'; 
    $mHtml .= '<tr>';
      $mHtml .= '<td align="center" width="100%" colspan="4" style="color:#257038; font-family:Trebuchet MS, Verdana, Arial; font-size:20px;" >EDITAR IMPACTO<hr></td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '<tr>';
      $mHtml .= '<td align="right" width="20%" class="TRform" ><b> * Descripci&oacute;n:&nbsp;&nbsp;</b></td>';
      $mHtml .= '<td align="left"  width="20%" class="TRform" ><input type="text" name="des_impact_" id="des_impact_ID" size="45" value="'.utf8_encode( $_IMPACT[0]['des_impact'] ).'" /></td>';
      $mHtml .= '<td align="right" width="20%" class="TRform" ><b> * Color:&nbsp;&nbsp;</b></td>';
      $mHtml .= '<td align="left"  width="40%" class="TRform" ><input type="text" maxlength="6" size="7" id="colorpicker2" value="'.$_IMPACT[0]['cod_colorx'].'"  readonly />&nbsp;<span id="muestra2ID" style="border:1px #000000 solid; background:#'.$_IMPACT[0]['cod_colorx'].' none repeat scroll 0 0;">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>';
    $mHtml .= '</tr>';    

    $mHtml .= '<tr>';
      $mHtml .= '<td align="right" width="20%" class="TRform" ><b> * Rango Inicial:&nbsp;&nbsp;</b></td>';
      $mHtml .= '<td align="left"  width="20%" class="TRform" ><input name="ran_inicia_" id="ran_inicia_ID" value="'.$_IMPACT[0]['min_ranini'].'" maxlength="3" size="3" onkeypress="return NumericInput( event );" /></td>';
      $mHtml .= '<td align="right" width="20%" class="TRform" ><b> * Rango Final:&nbsp;&nbsp;</b></td>';
      $mHtml .= '<td align="left"  width="40%" class="TRform" ><input name="ran_finali_" id="ran_finali_ID" value="'.$_IMPACT[0]['min_ranfin'].'" maxlength="3" size="3" onkeypress="return NumericInput( event );" /></td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '<tr>';
      $mHtml .= '<td align="center" width="100%" colspan="4" style="font-family:Trebuchet MS, Verdana, Arial;" ><br><input class="crmButton small save" type="button" onclick="SendEditImpacto( \''.$_AJAX['cod_transp'].'\',\''.$_IMPACT[0]['cod_impact'].'\');" value="Editar" name="Editar"></td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '</table>';
    $mHtml .= '</div>';
    
    echo $mHtml;
  }
  
  private function SaveImpacto( $_AJAX )
  {
    // echo "<pre>";
    // print_r( $_AJAX );
    // echo "</pre>";
    // die();
    
    $mSelect = "SELECT MAX( cod_impact ) FROM ".BASE_DATOS.".tab_genera_impact WHERE cod_transp = '".$_AJAX['cod_transp']."'";
    $consulta = new Consulta( $mSelect, $this -> conexion );
		$_CONSEC = $consulta -> ret_matriz();
    
    $consecutivo = $_CONSEC[0][0] + 1;
    
    $mInsert = "INSERT INTO ".BASE_DATOS.".tab_genera_impact
                          ( cod_transp, cod_impact, des_impact, 
                            cod_colorx, min_ranini, min_ranfin, 
                            usr_creaci,fec_creaci 
                          )
                    VALUES( '".$_AJAX['cod_transp']."', '".$consecutivo."',         '".$_AJAX['des_impact']."', 
                            '".$_AJAX['cod_colorx']."', '".$_AJAX['ran_inicia']."', '".$_AJAX['ran_finali']."', 
                            '".$_SESSION['datos_usuario']['cod_usuari']."', NOW() 
                          )";
                            
    $consulta = new Consulta( $mInsert, $this -> conexion );
    
  }
  
  private function getImpacto( $cod_transp, $cod_impacto = NULL )
  {
    $mSelect = "SELECT cod_impact, des_impact, cod_colorx, 
                       min_ranini, min_ranfin
                  FROM ".BASE_DATOS.".tab_genera_impact
                 WHERE cod_transp = '".$cod_transp."'";
    
    if( $cod_impacto != NULL )
    {
      $mSelect .= " AND cod_impact = '".$cod_impacto."'";
    }
     
    $mSelect .= " ORDER BY 4";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
		return $consulta -> ret_matriz();
  }
  
  private function Style()
  {
    echo '
        <style>
        .ui-tabs-vertical { width: 75%; }
        .ui-tabs-vertical .ui-tabs-nav { padding: .2em .1em .2em .2em; float: left; width: 12em; }
        .ui-tabs-vertical .ui-tabs-nav li { clear: left; width: 100%; border-bottom-width: 1px !important; border-right-width: 0 !important; margin: 0 -1px .2em 0; }
        .ui-tabs-vertical .ui-tabs-nav li a { display:block; }
        .ui-tabs-vertical .ui-tabs-nav li.ui-tabs-active { padding-bottom: 0; padding-right: .1em; border-right-width: 1px; border-right-width: 1px; }
        .ui-tabs-vertical .ui-tabs-panel { padding: 1em; float: right; width: 75%;}
        #tabs li .ui-icon-close { float: right; margin: 0.4em 0.2em 0 0; cursor: pointer; }
        .CellHead
        {
          font-family:Trebuchet MS, Verdana, Arial;
          font-size:13px;
          background-color: #35650F;
          color:#FFFFFF;
          padding: 4px;
        }
        
        .cellInfo1
        {
          font-family:Trebuchet MS, Verdana, Arial;
          font-size:11px;
          background-color: #EBF8E2;
          padding: 2px;
        }
        
        .cellInfo2
        {
          font-family:Trebuchet MS, Verdana, Arial;
          font-size:11px;
          background-color: #DEDFDE;
          padding: 2px;
        }
        
        .StyleDIV
          {
            background-color: rgb(240, 240, 240);
            border: 1px solid rgb(201, 201, 201); 
            padding: 5px; width: 99%; 
            min-height: 50px; 
            border-radius: 5px 5px 5px 5px;
          }
          .Style2DIV
          {
            background-color: #FFFFFF;
            border: 1px solid rgb(201, 201, 201); 
            padding: 5px; width: 96%; 
            min-height: 50px; 
            border-radius: 5px 5px 5px 5px;
          }
          .TRform
          {
            padding-right:3px; 
            padding-top:5px; 
            font-family:Trebuchet MS, Verdana, Arial; 
            font-size:12px;
          }
        </style>';
  }
}

$proceso = new AjaxInsertImpacto();

?>