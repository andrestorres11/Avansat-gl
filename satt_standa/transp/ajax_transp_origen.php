<?php
class AjaxOrigenTransportadora
{
  var $conexion;
  var $ind_estado = array();
  var $ind_estado_ = array();
  
  public function __construct()
  {
    $this -> ind_estado_[0][0] = '';
    $this -> ind_estado_[0][1] = '--';
    $this -> ind_estado[1][0] = '2';
    $this -> ind_estado[1][1] = 'INACTIVO';
    $this -> ind_estado[2][0] = '1';
    $this -> ind_estado[2][1] = 'ACTIVO';
    
    $_AJAX = $_REQUEST;
    include('../lib/ajax.inc');
    include_once( "../lib/general/dinamic_list.inc" );

    $this -> conexion = $AjaxConnection;
    $this -> $_AJAX['option']( $_AJAX );
  }

  protected function getOrigen( $_AJAX )
  {
    $mSql = "SELECT b.cod_ciudad, CONCAT(b.abr_ciudad,' (',LEFT(d.abr_depart,4),') - ',LEFT(e.nom_paisxx,3)),
                    b.cod_depart
               FROM ".BASE_DATOS.".tab_genera_ciudad b, 
                    ".BASE_DATOS.".tab_genera_depart d,
                    ".BASE_DATOS.".tab_genera_paises e
              WHERE b.cod_depart = d.cod_depart AND
                    b.cod_paisxx = d.cod_paisxx AND
                    d.cod_paisxx = e.cod_paisxx AND
                    b.ind_estado = '1' AND
                    (b.cod_ciudad LIKE '%". $_AJAX['term'] ."%' OR CONCAT(b.abr_ciudad,' (',LEFT(d.abr_depart,4),') - ',LEFT(e.nom_paisxx,3)) LIKE '%". $_AJAX['term'] ."%' )
              GROUP BY 1
              LIMIT 10";
    
    $consulta = new Consulta( $mSql, $this -> conexion );
    $transpor = $consulta -> ret_matriz();
    
    $data = array();
    for($i=0, $len = count($transpor); $i<$len; $i++){
       $data [] = '{"label":"'.$transpor[$i][2].'|'.$transpor[$i][0].' - '.utf8_encode( $transpor[$i][1] ).'","value":"'. $transpor[$i][2].'|'.$transpor[$i][0].' - '.utf8_encode( $transpor[$i][1] ).'"}'; 
    }
    echo '['.join(', ',$data).']';
    
  }

  private function Style()
  {
    echo '
      <style>
      
      .label-tr
      {
        padding-right:3px;
        padding-top:8px; 
        font-family:Trebuchet MS, Verdana, Arial; 
        font-size:12px;
        border-bottom: 1px solid #CDCDCD;
        border-top: 1px solid #FFFFFF;
      }
      
      .label-info
      {
        padding-right:3px;
        padding-top:8px; 
        font-family:Trebuchet MS, Verdana, Arial; 
        font-size:12px;
      }

      .displayDIV
      {
        background-color:#f0f0f0;
        border:1px solid #c9c9c9;
        padding:5px;
        min-height:200px;
        -moz-border-radius:5px 5px 5px 5px;
        -webkit-border-radius:5px 5px 5px 5px;
        border-top-left-radius:5px;
        border-top-right-radius:5px;
        border-bottom-right-radius:5px;
        border-bottom-left-radius:5px;
      }

      .displayDIV2
      {
        background-color:#D9D9D9;
        border:1px solid #9F9F9F;
        padding:5px;
        min-height:50px;
        -moz-border-radius:5px 5px 5px 5px;
        -webkit-border-radius:5px 5px 5px 5px;
        border-top-left-radius:5px;
        border-top-right-radius:5px;
        border-bottom-right-radius:5px;
        border-bottom-left-radius:5px;
      }
      
      .label-tr2
      {
        padding-right:3px;
        padding-top:8px; 
        font-family:Trebuchet MS, Verdana, Arial; 
        font-size:10px;
        border-bottom: 1px solid #CDCDCD;
        border-top: 1px solid #FFFFFF;
      }
      
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
      
      </style>
    ';
  }

  protected function InsertCiudad( $_AJAX )
  {
    /*echo "<pre>";
    print_r( $_AJAX );
    echo "</pre>";
    */
    $cod_ciudad = explode( '|', $_AJAX['cod_ciudad'] );

    $mInsert = "INSERT INTO ".BASE_DATOS.".tab_transp_origen
                           ( cod_transp, cod_depart, cod_ciudad, 
                             ind_estado, usr_creaci, fec_creaci)
                     VALUES( '".$_AJAX['cod_transp']."', '".$cod_ciudad[0]."', '".$cod_ciudad[1]."', 
                             '1', '".$_SESSION['datos_usuario']['cod_usuari']."', NOW());";
    
    if( $consulta = new Consulta( $mInsert, $this -> conexion ) )
      echo "y";
    else
      echo "n";
  }
  
  protected function DeleteCiudad( $_AJAX )
  {
    $mDelete = "DELETE FROM ".BASE_DATOS.".tab_transp_origen
                           WHERE cod_transp = '".$_AJAX['cod_transp']."' 
                             AND cod_ciudad = '".$_AJAX['cod_ciudad']."' ";
    
    if( $consulta = new Consulta( $mDelete, $this -> conexion ) )
      echo "y";
    else
      echo "n";
  }

  protected function MainForm( $_AJAX )
  {
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";
    $mHtml  = '';

    $mHtml  .= '<script> $(function() {$( "#registrarID" ).button();});</script>';
    $mHtml  .= '<center>';
      $mHtml  .= '<table width="100%" cellspacing="2px" cellpadding="0">';
    
      $mHtml .= '<tr>';
        $mHtml .= '<td colspan="4" style=" text-align:center;border-bottom:1px solid #000000; font-family:Trebuchet MS, Verdana, Arial; font-size:18px; font-weight:bold; padding-bottom:5px; color: #285C00;" ><i>OR&Iacute;GENES PARA '.str_replace('/-/','&',utf8_decode($_AJAX['nom_transp'])).'</i></td>';
      $mHtml .= '</tr>';
    
      $mHtml .= '</table>';
      $mHtml .= '</center><br><div id="AlarmaID" style="display:none;"></div>';

    $mHtml  .= '<center>';
      $mHtml  .= '<br><div id="filtrosID"><table width="100%" cellspacing="1" cellpadding="0">';
        
        $mHtml  .= '<tr>';
        $mHtml  .= '<td align="right">DIGITE Y/O SELECCIONE LA CIUDAD:&nbsp;&nbsp;&nbsp;</td>';  
        $mHtml  .= '<td align="left"><input type="text" size="35" maxlength="70" name="cod_ciuori" id="cod_ciuoriID" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="ENVIAR" id="registrarID"/></td>';  
        $mHtml  .= '</tr>';
      
        $mHtml  .= '</table></div>';

        $mHtml  .= '</center><br><br>';

    echo $mHtml;

    echo "<link rel=\"stylesheet\" href=\"../".$_AJAX['standa']."/estilos/dinamic_list.css\" type=\"text/css\">";
    echo "<script language=\"JavaScript\" src=\"../".$_AJAX['standa']."/js/dinamic_list.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".$_AJAX['standa']."/js/new_ajax.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".$_AJAX['standa']."/js/functions.js\"></script>\n";

    echo '<script>
          $( "#cod_ciuoriID" ).autocomplete({
            source: "../satt_standa/transp/ajax_transp_origen.php?option=getOrigen&standa=satt_standa",
            minLength: 2, 
            delay: 100
          });

          $( "#cod_ciuoriID" ).bind( "autocompletechange", function(event, ui){SaveCiudad();} );

          </script>';
    
    $mSql = "SELECT a.cod_ciudad, CONCAT(b.abr_ciudad,' (',LEFT(d.abr_depart,4),') - ',LEFT(e.nom_paisxx,3))
               FROM ".BASE_DATOS.".tab_transp_origen a,
                    ".BASE_DATOS.".tab_genera_ciudad b, 
                    ".BASE_DATOS.".tab_genera_depart d,
                    ".BASE_DATOS.".tab_genera_paises e
              WHERE a.cod_ciudad = b.cod_ciudad AND
                    b.cod_depart = d.cod_depart AND
                    b.cod_paisxx = d.cod_paisxx AND
                    d.cod_paisxx = e.cod_paisxx AND
                    b.ind_estado = '1' AND
                    a.cod_transp = '".$_AJAX['cod_transp']."'
              GROUP BY 1";

    $_SESSION["queryXLS"] = $mSql;
    $list = new DinamicList($this -> conexion, $mSql, 2 );
    $list -> SetClose('no');
    $list -> SetHeader("Codigo DANE", "field:a.cod_ciudad; width:1%; type:link; onclick:DeleteOrigen( $(this), '".$_AJAX['cod_transp']."');");
    $list -> SetHeader("Ciudad", "field:CONCAT(b.abr_ciudad,' (',LEFT(d.abr_depart,4),') - ',LEFT(e.nom_paisxx,3)); width:1%");

    $list -> Display( $this -> conexion );

    $_SESSION["DINAMIC_LIST"] = $list;
    echo "Para eliminar una ciudad haga click en el CODIGO DANE de la lista.<br><br>".$list -> GetHtml(); 
  }
}

$proceso = new AjaxOrigenTransportadora();
 ?>