<?php
/*! \file: ajax_recome_recome.php
 *  \brief: Ajax para los procesos de las recomendaciones
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 25/05/2015
 *  \bug: 
 *  \warning: 
 */

ini_set('display_errors', false);
session_start();

/*! \class: AjaxRecomend
 *  \brief: Clase para los procesos de las recomendaciones
 */
class AjaxRecomend
{
  var $conexion;

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

  /*! \fn: Style
   *  \brief: Estilos para las tablas
   *  \author: Ing. Fabian Salinas
   *  \date: 25/05/2015
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return:
   */
  private function Style()
  {
    echo '
        <style>
        .CellHead
        {
          font-family:Trebuchet MS, Verdana, Arial;
          font-size:13px;
          background-color: #35650F;
          color:#FFFFFF;
          padding: 4px;
        }
        
        .CellHead2
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
        
        .cellInfo
        {
          font-family:Trebuchet MS, Verdana, Arial;
          font-size:11px;
          background-color: #FFFFFF;
          padding: 2px;
        }
        
        tr.row:hover  td
        {
          background-color: #9ad9ae;
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
            background-color: rgb(255, 255, 255);
            border: 1px solid rgb(201, 201, 201); 
            padding: 5px; width: 95%; 
            min-height: 50px; 
            border-radius: 5px 5px 5px 5px;
          }
          .TRform
          {
            padding-right:3px; 
            padding-top:15px; 
            font-family:Trebuchet MS, Verdana, Arial; 
            font-size:12px;
          }
        </style>';
  }

  /*! \fn: MainLoad
   *  \brief: 
   *  \author: 
   *  \date: dia/mes/año
   *  \date modified: dia/mes/año
   *  \param: Data
   *  \return:
   */
  protected function MainLoad( $mData )
  {
    echo "<link rel=\"stylesheet\" href=\"../satt_standa/estilos/dinamic_list.css\" type=\"text/css\">";
    echo "<script language=\"JavaScript\" src=\"../satt_standa/js/dinamic_list.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../satt_standa/js/new_ajax.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../satt_standa/js/functions.js\"></script>\n";

    $this -> Style();
      
    $mHtml  = '<center>';

      $mHtml  .= '<table width="100%" cellspacing="2px" cellpadding="0">';
        $mHtml .= '<tr>';
          $mHtml .= '<td colspan="4" style=" text-align:center;border-bottom:1px solid #000000; font-family:Trebuchet MS, Verdana, Arial; font-size:18px; font-weight:bold; padding-bottom:5px; color: #285C00;" ><i>Nuevo Recomendado</i></td>';
        $mHtml .= '</tr>';
      $mHtml .= '</table>';
      
      $mHtml .= '<br><div class="Style2DIV">';
        $mHtml .= '<table width="100%" cellspacing="1" cellpadding="0">';
          $mHtml .= '<tr>';
          $mHtml .= '<td class="CellHead" width="20%" align="center" style="padding:5px;">TIPO</td>';
          $mHtml .= '<td class="CellHead" width="35%" align="center" style="padding:5px;">ENCABEZADO</td>';
          $mHtml .= '<td class="CellHead" width="10%" align="center" style="padding:5px;">&#191;ES REQUERIDO?</td>';
          $mHtml .= '<td class="CellHead" width="35%" align="center" style="padding:5px;">TEXTO</td>';
          $mHtml .= '</tr>';

          $mSelect = "SELECT num_consec, nom_config 
                        FROM ".BASE_DATOS.".tab_config_subcau 
                        ORDER BY 2";

          $consulta = new Consulta( $mSelect, $this -> conexion );
          $mConfig = $consulta -> ret_matriz();

          $fSelect =  '<select name="cod_tipoxx" id="cod_tipoxxID" onchange="SetForm( $(this) );" >';
          $fSelect .= '<option value="">-Seleccione-</option>';
          foreach( $mConfig as $row )
            $fSelect .= '<option value="'.$row['num_consec'].'">'.$row['nom_config'].'</option>'; 
          $fSelect .= '</select>';

          $mHtml .= '<tr>';
          $mHtml .= '<td class="cellInfo1" width="20%" align="center">'.$fSelect.'</td>';
          $mHtml .= '<td class="cellInfo1" width="35%" align="center"><input type="text" onkeyup="setEncBoceto( $( this ) );" style="display:none;text-transform:uppercase;" maxlenght="200" size="50" name="tex_encabe" id="tex_encabeID" /></td>';
          $mHtml .= '<td class="cellInfo1" width="10%" align="center"><input type="checkbox" onchange="setReqBoceto( $( this ) );" style="display:none;" name="ind_requer" id="ind_requerID" value="1" /></td>';
          $mHtml .= '<td class="cellInfo1" width="35%" align="center"><textarea style="display:none;text-transform:uppercase;" name="des_textox" rows="2" cols="50" id="des_textoxID"></textarea></td>';
          $mHtml .= '</tr>';

          $mHtml .= '<tr>';
          $mHtml .= '<td class="cellInfo1" colspan="4" align="center"><div style="display:none;" id="formHtmlID" class="Style2DIV"></div></td>';
          $mHtml .= '</tr>';

        $mHtml .= '</table>';
      $mHtml .= '</div>';
      
      $mHtml .= '<br><br><br><span style="font-family:Trebuchet MS, Verdana, Arial; font-size:12px; font-weight:bold; padding-bottom:5px; color: #285C00;"><b>ATENCI&Oacute;N:</b>&nbsp;<i>PARA EDITAR O ELIMINAR UN RECOMENDADO HAGA CLIC SOBRE EL CONSECUTIVO</i></span><div id="DynamicID">';

      $mSql = "SELECT a.cod_consec, b.nom_config, a.tex_encabe, 
                      IF( a.ind_requer = '1' ,'SI', 'NO'), a.des_texto
                 FROM ". BASE_DATOS .".tab_genera_recome a,
                      ". BASE_DATOS .".tab_config_subcau b
                WHERE a.cod_tipoxx = b.num_consec ";
      
      $_SESSION["queryXLS"] = $mSql;
      $list = new DinamicList( $this -> conexion, $mSql, 1 );
      $list->SetClose('no');
      $list->SetHeader("Consecutivo", "field:a.cod_consec; width:1%; type:link; onclick:EditorRecome( $(this) )");
      $list->SetHeader("Tipo", "field:b.nom_config; width:1%");
      $list->SetHeader("Encabezado", "field:a.tex_encabe; width:1%");
      $list->SetHeader("Requerido", "field:IF( a.ind_requer = '1' ,'SI', 'NO'); width:1%" );
      $list->SetHeader("Descripcion","field:a.des_texto; width:1%");

      $list->Display($this->conexion);

      $_SESSION["DINAMIC_LIST"] = $list;

      $mHtml .= $list -> GetHtml();
      
      $mHtml .= '</div>';

    $mHtml .= '</center>';

    echo $mHtml; 
  }

  /*! \fn: DelRecomend
   *  \brief: Elimina las recomendaciones
   *  \author: Ing. Fabian Salinas
   *  \date: 25/05/2015
   *  \date modified: dia/mes/año
   *  \param: Data
   *  \return:
   */
  protected function DelRecomend( $mData )
  {
    $mDelete = "DELETE FROM ".BASE_DATOS.".tab_genera_recome WHERE cod_consec = '".$mData['num_consec']."' ";
    
    if( $consulta = new Consulta( $mDelete, $this -> conexion ) )
    {
      echo "1000";
    }
    else
    {
      echo "9999";
    }
  }

  /*! \fn: UpdRecomend
   *  \brief: Actualiza las recomendaciones
   *  \author: Ing. Fabian Salinas
   *  \date: 25/05/2015
   *  \date modified: dia/mes/año
   *  \param: Data
   *  \return:
   */
  protected function UpdRecomend( $mData )
  {
    $mUpdate = "UPDATE ".BASE_DATOS.".tab_genera_recome 
                   SET tex_encabe = '".$mData[tex_encabe]."', 
                       ind_requer = '".$mData[ind_requer]."', 
                       des_texto  = '".$mData[des_texto]."', 
                       usr_modifi = '".$_SESSION[datos_usuario][cod_usuari]."', 
                       fec_modifi = NOW() 
                 WHERE cod_consec = '".$mData[num_consec]."'
               ";
    if( $consulta = new Consulta( $mUpdate, $this -> conexion ) )
      echo "1000";
    else
      echo "9999";
  }

  /*! \fn: EditorRecome
   *  \brief: Formulario editor de recomendaciones
   *  \author: Ing. Fabian Salinas
   *  \date: 25/05/2015
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return:
   */
  protected function EditorRecome()
  {
    $this -> Style();

    $mLink  = '<span onclick="UpdRecomend('.$_REQUEST[num_consec].')"><img border="0" src="../satt_standa/imagenes/editar.png" width="25px"></span>';
    $mLink .= '<span onclick="DelRecomend('.$_REQUEST[num_consec].')"><img border="0" src="../satt_standa/imagenes/eliminar.png" width="25px"></span>';

    $mArrayTitu = array('Consecutivo', 'Tipo', 'Encabezado', 'Requerido', 'Descripcion', 'Edicion');

    $mSql = "SELECT a.cod_consec, b.nom_config, a.tex_encabe, 
                    a.ind_requer, a.des_texto
               FROM ". BASE_DATOS .".tab_genera_recome a 
         INNER JOIN ". BASE_DATOS .".tab_config_subcau b 
                 ON a.cod_tipoxx = b.num_consec 
              WHERE a.cod_consec = '".$_REQUEST[num_consec]."' ";
    $mConsult = new Consulta($mSql, $this -> conexion);
    $mArrayData = $mConsult -> ret_matrix('i'); 

    $mCheck = $mArrayData[0][3] == 1 ? "checked" : "" ;

    $mHtml  = '<form action="index.php" method="post" name="formulario">';
    $mHtml .= '<table width="100%" cellspacing="1" cellpadding="0" border="0">';

    $mHtml .=   '<tr>';
    foreach ($mArrayTitu as $value) {
      $mHtml .=   '<th class="CellHead">'.$value.'</th>';
    }
    $mHtml .=   '</tr>';

    $mHtml .=   '<tr>';
    $mHtml .=     '<td class="cellInfo">'.$mArrayData[0][0].'&nbsp;</td>';
    $mHtml .=     '<td class="cellInfo">'.$mArrayData[0][1].'&nbsp;</td>';
    $mHtml .=     '<td class="cellInfo"><input type="text" name="texEncbe" id="texEncbeID" size="50" maxlenght="200" value="'.$mArrayData[0][2].'"></td>';
    $mHtml .=     '<td class="cellInfo"><input type="checkbox" name="indRequ" id="indRequID" '.$mCheck.'></td>';
    $mHtml .=     '<td class="cellInfo"><textarea name="des_texto" id="des_textoID" cols="50" rows="2" style="text-transform: uppercase;">'.$mArrayData[0][4].'</textarea></td>';
    $mHtml .=     '<td class="cellInfo" align="center">'.$mLink.'</td>';
    $mHtml .=   '</tr>';
    $mHtml .= '</table>';

    $mHtml .= '</form>';  

    echo $mHtml;
  }

  /*! \fn: InsertEvento
   *  \brief: 
   *  \author: 
   *  \date: dia/mes/año
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return:
   */
  protected function InsertEvento( $mData )
  {
    $mSelect = "SELECT MAX( cod_consec ) FROM ".BASE_DATOS.".tab_genera_recome";

    $consulta = new Consulta( $mSelect, $this -> conexion );
    $mMaximo = $consulta -> ret_matriz();
    
    $mConsecutivo = (int)$mMaximo[0][0] + 1;
    $mTipoxx = strtoupper( str_replace( '/-/', '&nbsp;', $mData['cod_tipoxx'] ) );
    $mEncabe = strtoupper( str_replace( '/-/', '&nbsp;', $mData['tex_encabe'] ) );
    $mRequer = strtoupper( str_replace( '/-/', '&nbsp;', $mData['ind_requer'] ) );
    $mTextox = strtoupper( str_replace( '/-/', '&nbsp;', $mData['des_textox'] ) );
    $mConfig = str_replace( '_consec_', $mConsecutivo, str_replace( '/-/', '&nbsp;', $mData['Html'] ) );

    $mInsert = "INSERT INTO ".BASE_DATOS.".tab_genera_recome
                          ( cod_consec, cod_tipoxx, tex_encabe, 
                            ind_requer, des_texto, htm_config, 
                            usr_creaci, fec_creaci
                 ) VALUES ( '".$mConsecutivo."', '".$mTipoxx."', '".$mEncabe."', 
                            '".$mRequer."', '".$mTextox."', '".$mConfig."',
                            '".$_SESSION['datos_usuario']['cod_usuari']."', NOW() )";
    
    if( $consulta = new Consulta( $mInsert, $this -> conexion ) )
    {
      echo "1000";
    }
    else
    {
      echo "9999";
    }
  }

  /*! \fn: SetForm
   *  \brief: 
   *  \author: 
   *  \date: dia/mes/año
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return:
   */
  protected function SetForm( $mData )
  {
    echo "<script>$('input[type=button], button').button();</script>";
    
    $mHtml .= '<center>';
    switch( (int)$mData['sel'] )
    {
      case 1:
      
        $mHtml  .= '<table width="100%" cellspacing="2px" cellpadding="0">';
          $mHtml .= '<tr>';
            $mHtml .= '<td colspan="4" style=" text-align:center;border-bottom:1px solid #000000; font-family:Trebuchet MS, Verdana, Arial; font-size:12px; font-weight:bold; padding-bottom:5px; color: #285C00;" ><i>Boceto del Elemento dentro del Formulario</i></td>';
          $mHtml .= '</tr>';
        $mHtml .= '</table><br><br>';
          
        $mHtml .= '<div id="bocetoID">';
        $mHtml .= '<label for="val_encabe_consec_ID" id="val_requir_consec_ID">&nbsp;</label>
                   <label for="val_itemxx_consec_ID" id="val_encabe_consec_ID">&nbsp;</label>
                   <div id="ToRegID"><input type="text" name="val_itemxx_consec_" id="val_itemxx_consec_ID" style="text-transform:uppercase;" maxlenght="255" size="20" /></div>'; 
        $mHtml .= '</div>';
      break;
    
      case 2:
        $mHtml  .= '<table width="100%" cellspacing="2px" cellpadding="0">';
          $mHtml .= '<tr>';
            $mHtml .= '<td colspan="4" style=" text-align:center;border-bottom:1px solid #000000; font-family:Trebuchet MS, Verdana, Arial; font-size:12px; font-weight:bold; padding-bottom:5px; color: #285C00;" ><i>Datos a Ingresar</i></td>';
          $mHtml .= '</tr>';
        $mHtml .= '</table><br><br>';

        $mHtml .= '<div id="PreBocetoID">';
        $mHtml .= '<input type="text" style="text-transform:uppercase;" name="par_insert" id="par_insertID" />&nbsp;&nbsp;
                   <input type="button" value="OK" onclick="InsertParame();" />'; 
        $mHtml .= '</div><br><br>';

        $mHtml  .= '<table width="100%" cellspacing="2px" cellpadding="0">';
          $mHtml .= '<tr>';
            $mHtml .= '<td colspan="4" style=" text-align:center;border-bottom:1px solid #000000; font-family:Trebuchet MS, Verdana, Arial; font-size:12px; font-weight:bold; padding-bottom:5px; color: #285C00;" ><i>Boceto del Elemento dentro del Formulario</i></td>';
          $mHtml .= '</tr>';
        $mHtml .= '</table><br><br>';
          
        $mHtml .= '<div id="bocetoID">';
        $mHtml .= '<label for="val_encabe_consec_ID" id="val_requir_consec_ID">&nbsp;</label>
                   <label for="val_itemxx_consec_ID" id="val_encabe_consec_ID">&nbsp;</label>
                   <div id="ToRegID"><select name="val_itemxx_consec_" id="val_itemxx_consec_ID"><option value="">-Seleccione-</option></select></div>'; 
        $mHtml .= '</div>';
      break;
    
      case 3:
        $mHtml  .= '<table width="100%" cellspacing="2px" cellpadding="0">';
          $mHtml .= '<tr>';
            $mHtml .= '<td colspan="4" style=" text-align:center;border-bottom:1px solid #000000; font-family:Trebuchet MS, Verdana, Arial; font-size:12px; font-weight:bold; padding-bottom:5px; color: #285C00;" ><i>Unidad de Medida</i></td>';
          $mHtml .= '</tr>';
        $mHtml .= '</table><br><br>';

        $mHtml .= '<div id="PreBocetoID">';
        $mHtml .= '<input type="text" style="text-transform:uppercase;" name="med_rangox" id="med_rangoxID" />&nbsp;&nbsp;
                   <input type="button" value="OK" onclick="InsertRango();" />'; 
        $mHtml .= '</div><br><br>';

        $mHtml  .= '<table width="100%" cellspacing="2px" cellpadding="0">';
          $mHtml .= '<tr>';
            $mHtml .= '<td colspan="4" style=" text-align:center;border-bottom:1px solid #000000; font-family:Trebuchet MS, Verdana, Arial; font-size:12px; font-weight:bold; padding-bottom:5px; color: #285C00;" ><i>Boceto del Elemento dentro del Formulario</i></td>';
          $mHtml .= '</tr>';
        $mHtml .= '</table><br><br>';
          
        $mHtml .= '<div id="bocetoID">';
        $mHtml .= '<label for="val_encabe_consec_ID" id="val_requir_consec_ID">&nbsp;</label>
                   <label for="val_itemxx_consec_ID" id="val_encabe_consec_ID">&nbsp;</label>
                   <div id="ToRegID"><input type="text" size="10" name="val_itemxx_consec_" id="val_itemxx_consec_ID" />&nbsp;<span id="rango_consec_ID">&nbsp;</span></div>'; 
        $mHtml .= '</div>';
      break;
    
      case 4:
        $mHtml  .= '<table width="100%" cellspacing="2px" cellpadding="0">';
          $mHtml .= '<tr>';
            $mHtml .= '<td colspan="4" style=" text-align:center;border-bottom:1px solid #000000; font-family:Trebuchet MS, Verdana, Arial; font-size:12px; font-weight:bold; padding-bottom:5px; color: #285C00;" ><i>Boceto del Elemento dentro del Formulario</i></td>';
          $mHtml .= '</tr>';
        $mHtml .= '</table><br><br>';
          
        $mHtml .= '<div id="bocetoID">';
        $mHtml .= '<label for="val_encabe_consec_ID" id="val_requir_consec_ID">&nbsp;</label>
                   <label for="val_itemxx_consec_ID" id="val_encabe_consec_ID">&nbsp;</label>&nbsp;&nbsp;
                   <div id="ToRegID">SI&nbsp;<input type="radio" name="val_itemxx_consec_" id="val_itemxx_consec_ID" value="S">&nbsp;&nbsp;NO&nbsp;<input type="radio" name="val_itemxx_consec_" value="N">&nbsp;&nbsp;</div>'; 
        $mHtml .= '</div>';
      break;
    }
    
    $mHtml .= '<br><br><br><hr><div>';
    $mHtml .= '<input type="button" onclick="InsertEvento(\''.$mData['sel'].'\');" value="REGISTRAR" />';
    $mHtml .= '</div>';
    
    $mHtml .= '</center>';
    
    echo $mHtml;
  }
}

$proceso = new AjaxRecomend();

?>