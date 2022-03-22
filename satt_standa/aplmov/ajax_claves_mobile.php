<?php
ini_set('display_errors', false);
session_start();

class AjaxAdministrarClaves
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

  protected function newPassword( $mData )
  {
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";

    $mHtml  = '<div class="StyleDIV">';
    $mHtml .= '<table width="95%" cellpadding="0" cellspacing="1">';

    $mHtml .= '<tr>';
    $mHtml .= '<td class="CellHead" colspan="2" align="center">Cambio de Clave Para el Despacho <b>'.$mData['num_despac'].'</b><input type="hidden" name="num_despac" id="num_despacID" value="'.$mData['num_despac'].'" /></td>';
    $mHtml .= '</tr>';

    $mHtml .= '<tr>';
    $mHtml .= '<td class="cellInfo1" align="right">* Nueva Clave:</td>';
    $mHtml .= '<td class="cellInfo1" align="left"><input type="text" name="nue_passwo" id="nue_passwoID" maxlength="20" size="25" /></td>';
    $mHtml .= '</tr>';
	
    $mHtml .= '<tr>';
    $mHtml .= '<td class="cellInfo2" align="right">* Repita Clave:</td>';
    $mHtml .= '<td class="cellInfo2" align="left"><input type="password" name="rep_passwo" id="rep_passwoID" maxlength="20" size="25" /></td>';
    $mHtml .= '</tr>';
	
    $mHtml  .= '</table>';
    $mHtml  .= '</div>';
  	
  	echo '<center>'.$mHtml.'</center>';
  }

  protected function FormPassword( $mData )
  {
    $mSelect = "SELECT a.num_despac, a.cod_manifi, d.nom_tipdes,
                       e.nom_ciudad AS nom_ciuori, 
                       f.nom_ciudad AS nom_ciudes, 
                       g.abr_tercer AS nom_transp, 
                       b.num_placax, b.cod_conduc, 
                       b.clv_satmov, c.num_desext, h.nom_produc
                  FROM ".BASE_DATOS.".tab_despac_despac a 
             LEFT JOIN ".BASE_DATOS.".tab_genera_tipdes d
                    ON a.cod_tipdes = d.cod_tipdes
             LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad e
                    ON a.cod_ciuori = e.cod_ciudad
             LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad f
                    ON a.cod_ciudes = f.cod_ciudad
             LEFT JOIN ".BASE_DATOS.".tab_despac_sisext c
                    ON a.num_despac = c.num_despac
             LEFT JOIN ".BASE_DATOS.".tab_genera_produc h
                    ON c.cod_mercan = h.cod_produc,
                       ".BASE_DATOS.".tab_despac_vehige b
             LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer g
                    ON b.cod_transp = g.cod_tercer
                 WHERE b.num_placax = '".$mData['num_placax']."'
                   AND a.num_despac = b.num_despac 
                   AND a.fec_salida IS NOT NULL 
                   AND a.fec_llegad IS NULL 
                   AND a.ind_anulad = 'R' 
                   AND b.ind_activo = 'S' 
              ORDER BY b.num_despac DESC";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_DESPAC = $consulta -> ret_matriz();

    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";

    $mHtml  = '<table width="100%" cellpadding="0" cellspacing="1">';
    
    if( sizeof( $_DESPAC ) > 0 )
    {
      $mHtml .= '<tr>';
        $mHtml .= '<td class="CellHead" colspan="7" align="center">Se Encontraron '.sizeof( $_DESPAC ).' Despachos en Ruta para la Placa '. $mData['num_placax'] .'</td>';
      $mHtml .= '</tr>';
      
      $mHtml .= '<tr>';
        $mHtml .= '<td class="CellHead" align="center">Despacho</td>';
        $mHtml .= '<td class="CellHead" align="center">No. Manifiesto</td>';
        $mHtml .= '<td class="CellHead" align="center">No. Viaje</td>';
        $mHtml .= '<td class="CellHead" align="center">Tipo Despacho</td>';
        $mHtml .= '<td class="CellHead" align="center">Origen</td>';
        $mHtml .= '<td class="CellHead" align="center">Destino</td>';
        $mHtml .= '<td class="CellHead" align="center">&nbsp;</td>';
      $mHtml .= '</tr>';

      for( $i = 0, $limit = sizeof( $_DESPAC ); $i < $limit; $i++ )
      {
        $row = $_DESPAC[$i];
        $style = $i % 2 == 0 ? 'cellInfo1' : 'cellInfo2';

        $mHtml .= '<tr>';
          $mHtml .= '<td class="'.$style.'" align="center">'.$row['num_despac'].'</td>';
          $mHtml .= '<td class="'.$style.'" align="center">'.$row['cod_manifi'].'</td>';
          $mHtml .= '<td class="'.$style.'" align="center">'.$row['num_desext'].'</td>';
          $mHtml .= '<td class="'.$style.'" align="center">'.$row['nom_tipdes'].'</td>';
          $mHtml .= '<td class="'.$style.'" align="center">'.$row['nom_ciuori'].'</td>';
          $mHtml .= '<td class="'.$style.'" align="center">'.$row['nom_ciudes'].'</td>';
          $mHtml .= '<td class="'.$style.'" align="center"><input type="button" class="crmButton small save" name="num_despac" onclick="setPassword( $(this) )" id="'.$row['num_despac'].'" value="Cambiar" /></td>';
        $mHtml .= '</tr>';
      }

    }
    else
    {
      $mHtml .= '<tr>';
        $mHtml .= '<td class="CellHead" colspan="15" align="center">No Se Encontraron Despachos en Ruta para la Placa '. $mData['num_placax'] .'</td>';
      $mHtml .= '</tr>';
    }  
    
    $mHtml .= '</table>';

    echo $mHtml;
  }

  protected function ChangeClave( $mData )
  {
    $mUpdate = "UPDATE ".BASE_DATOS.".tab_despac_vehige 
                   SET clv_satmov = MD5( '".strtolower( $mData['nue_passwo'] )."' ) 
                 WHERE num_despac = '".$mData['num_despac']."'";
  	
  	if( new Consulta( $mUpdate, $this -> conexion ) )
  	  echo 'y';
  	else
  	  echo 'n';
  }

 
}

$proceso = new AjaxAdministrarClaves();

?>