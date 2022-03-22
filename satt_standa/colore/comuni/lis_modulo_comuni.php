<?php

class ModuloComunicaciones
{
  var $conexion,
      $usuario,
      $cod_aplica;
  public function __construct( $co, $us, $ca )
  {
    $this -> conexion = $co;
    $this -> usuario = $us;
    $this -> cod_aplica = $ca;
    $this -> principal();
  }
  
  private function principal()
  {
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
		
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/es.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/time.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";
		
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/asi_comuni_comuni.js' ></script>\n";
    
    echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";

    $formulario = new Formulario ( "index.php", "post", "LISTAR PARAMETRIZAR USUARIO", "formulario" );
		echo "<td>";
		$formulario->oculto("Standa\" id=\"StandaID\"", DIR_APLICA_CENTRAL, 0);
    $formulario->oculto("filter\" id=\"filterID\"", COD_FILTRO_EMPTRA, 0);
    $formulario->oculto("transp\" id=\"transpID\"", '', 0);
    #$formulario->oculto("listar\" id=\"listarID\"", 'listar', 0);
		echo "<td></tr>";
		echo "<tr>";
		echo "<table width='100%' border='0' class='tablaList' align='center' cellspacing='0' cellpadding='0'>";
		echo "<tr>";
		echo "<td class='celda_titulo2' style='padding:4px;' width='100%' colspan='8' >Seleccion de Transportadora</td>";
		echo "</tr>";
		echo "<tr>";
    $readonly = ''; 
    $filter = $this -> VerifyTranspor();
    if( sizeof( $filter ) > 0 )
    { 
      $TRANSP = $this -> getTransp( $filter['clv_filtro'] );
      $readonly = 'readonly="readonly" value="'.$TRANSP[0][0].' - '.$TRANSP[0][1].'"';
    }
    
		echo "<td class='celda_titulo' style='padding:4px;' width='50%' colspan='2' align='right' >
				Nit </td>";
		echo "<td class='celda_titulo' style='padding:4px;' width='50%' colspan='1' >
				<input class='campo_texto' type='text' size='35' name='cod_transp2' id='cod_transp2ID' ".$readonly."/></td>";
        echo "<td class='celda_titulo' style='padding:4px;' width='50%' >Nombre Empresa:</td>";
        echo "<td class='celda_titulo' style='padding:4px;' width='50%' ><label id='nom_empresID'></label></td>";
		echo "</tr>";
		echo "<tr> 
            <td  class='celda_titulo' style='padding:4px;' align='left' colspan='9' >         
              Novedad:         ".ModuloComunicaciones::getNovedades('cod_noveda')."  
              Tipo Correo:     ".ModuloComunicaciones::getTipCorreo('tip_correo')."  
            </td>
          </tr>
          <tr>
            <td class='celda_titulo' style='padding:4px;' align='left' colspan='9'>      
              Destino:         ".ModuloComunicaciones::getLisCiudad('cod_ciudes', 'd')."
              Origen:          ".ModuloComunicaciones::getLisCiudad('cod_ciuori', 'o')."
              Producto:        ".ModuloComunicaciones::getLisProduc('cod_produc')."
              Tipo Operacion:  ".ModuloComunicaciones::getLisOperac('cod_operac')."
    
            </td>
          </tr> 
          <tr>
            <td class='celda_titulo' style='padding:4px;' align='left' colspan='9'>                           
              Tipo Transporte: ".ModuloComunicaciones::getLisTiptra('cod_tiptra')."
              Zona:            ".ModuloComunicaciones::getLisZonasx('cod_zonasx')."
              Canal:           ".ModuloComunicaciones::getLisCanalx('cod_canalx')."
              Depositos:       ".ModuloComunicaciones::getLisDeposi('cod_deposi')."
          </td>
        </tr>";

    echo "<td  class='celda_etiqueta' style='padding:4px;' align='center' colspan='9' >				 
            <input class='crmButton small save' style='cursor:pointer;' type='button' value='Similres' onclick='ListarDuplicados();'/></td>";
		echo "</tr>";
		echo "</table></td>";
		$formulario -> cerrar();
    
    echo "<center><div id='resultID'></div></center>";
    echo "<center><div id='PopUpID'></div></center>";
    
  }

  function getNovedades($mCodNivel = NULL)
  {
      $mSelect = '(
                    SELECT cod_noveda, UPPER( nom_noveda ) AS nom_noveda
                    FROM '.BASE_DATOS.'.tab_genera_noveda 
                    WHERE ind_visibl = "1" AND nom_noveda LIKE "%NER /%" 
                  )
                  UNION ALL 
                  (
                   SELECT cod_noveda, UPPER( nom_noveda ) AS nom_noveda
                     FROM '.BASE_DATOS.'.tab_genera_noveda 
                    WHERE ind_visibl ="1" AND 
                         ( nom_noveda LIKE "%NEC /%" OR  nom_noveda LIKE "%NICC /%" )
                  )
                  UNION ALL 
                  (
                   SELECT cod_noveda, UPPER( nom_noveda ) AS nom_noveda
                     FROM '.BASE_DATOS.'.tab_genera_noveda 
                    WHERE ind_visibl = "1" AND 
                          nom_noveda LIKE "%NED /%"
                  ) ';


      $consulta = new Consulta( $mSelect, $this -> conexion );
      $_NOVEDA = $consulta -> ret_matriz('i');
    
      $mHtml = '<select id="'.$mCodNivel.'ID" name="'.$mCodNivel.'">';
        $mHtml .= '<option value="">--</option>';
        foreach ($_NOVEDA as $mKey => $mValue) {
          $mHtml .= '<option value="'.$mValue["cod_noveda"].'">'.$mValue["nom_noveda"].'</option>';
        }
      $mHtml .= '</select>';

      return $mHtml;
  }

  function getTipCorreo($mCodNivel = NULL)
  {
    $mSelect = '(
                  SELECT "P" AS cod_correc, "Primario" AS nom_correc                     
                )
                UNION ALL 
                (
                  SELECT "s" AS cod_correc, "Segundario" AS nom_correc
                ) ';


      $consulta = new Consulta( $mSelect, $this -> conexion );
      $_CORREO = $consulta -> ret_matrix('a');

    $mHtml = '<select id="'.$mCodNivel.'ID" name="'.$mCodNivel.'">';
        $mHtml .= '<option value="">--</option>';
        foreach ($_CORREO as $mKey => $mValue) {
          $mHtml .= '<option value="'.$mValue["cod_correc"].'">'.$mValue["nom_correc"].'</option>';
        }
      $mHtml .= '</select>';
      return $mHtml;
  }

  function getLisCiudad( $mCodNivel = NULL, $mList = 'o')
  {
    if($mList == 'o')
    {
      $mSelect = "SELECT a.cod_ciudad, CONCAT(b.abr_ciudad,' (',LEFT(d.abr_depart,4),') - ',LEFT(e.nom_paisxx,3)) AS nom_ciudad
                  FROM ".BASE_DATOS.".tab_transp_origen a,
                       ".BASE_DATOS.".tab_genera_ciudad b, 
                       ".BASE_DATOS.".tab_genera_depart d,
                       ".BASE_DATOS.".tab_genera_paises e
                   WHERE a.cod_ciudad = b.cod_ciudad AND
                       b.cod_depart = d.cod_depart AND
                       b.cod_paisxx = d.cod_paisxx AND
                       d.cod_paisxx = e.cod_paisxx AND
                       b.ind_estado = '1' AND
                       a.cod_transp = '860068121'
                   GROUP BY 1 ORDER BY 2";
    }
    else
    {
      $mSelect = "SELECT b.cod_ciudad, CONCAT(b.abr_ciudad,' (',LEFT(d.abr_depart,4),') - ',LEFT(e.nom_paisxx,3)) AS nom_ciudad
                  FROM ".BASE_DATOS.".tab_genera_ciudad b, 
                       ".BASE_DATOS.".tab_genera_depart d,
                       ".BASE_DATOS.".tab_genera_paises e
                 WHERE b.cod_depart = d.cod_depart AND
                       b.cod_paisxx = d.cod_paisxx AND
                       d.cod_paisxx = e.cod_paisxx AND
                       b.ind_estado = '1'  
                 GROUP BY 1 ORDER BY 2 ";
    }
    

      $consulta = new Consulta( $mSelect, $this -> conexion );
      $_ORIGEN = $consulta -> ret_matriz('i');

      $mHtml = '<select id="'.$mCodNivel.'ID" name="'.$mCodNivel.'">';
        $mHtml .= '<option value="">--</option>';
        foreach ($_ORIGEN as $mKey => $mValue) {
          
          $mHtml .= '<option value="'.$mValue["cod_ciudad"].'">'.$mValue["nom_ciudad"].'</option>';
        }
      $mHtml .= '</select>';

      return $mHtml;

  }

  function getLisProduc( $mCodNivel = NULL)
  {
            // Producto
      $mSelect = "SELECT cod_produc, nom_produc FROM ".BASE_DATOS.".tab_genera_produc WHERE ind_estado = '1' ORDER BY 2";
      $consulta = new Consulta( $mSelect, $this -> conexion );
      $_PRODUC = $consulta -> ret_matrix("a");
    
      $mHtml = '<select id="'.$mCodNivel.'ID" name="'.$mCodNivel.'">';
        $mHtml .= '<option value="">--</option>';
        foreach ($_PRODUC as $mKey => $mValue) {
          $mHtml .= '<option value="'.$mValue["cod_produc"].'">'.$mValue["nom_produc"].'</option>';
        }
      $mHtml .= '</select>';

      return $mHtml;
  }

  function getLisOperac( $mCodNivel = NULL)
  {
     // Tipo de Operacion
      $mSelect = "SELECT cod_tipdes, nom_tipdes FROM ".BASE_DATOS.".tab_genera_tipdes WHERE 1 = 1 ORDER BY 2";
      $consulta = new Consulta( $mSelect, $this -> conexion );
      $_TIPDES = $consulta -> ret_matrix("a");
    
      $mHtml = '<select id="'.$mCodNivel.'ID" name="'.$mCodNivel.'">';
        $mHtml .= '<option value="">--</option>';
        foreach ($_TIPDES as $mKey => $mValue) {
          $mHtml .= '<option value="'.$mValue["cod_tipdes"].'">'.$mValue["nom_tipdes"].'</option>';
        }
      $mHtml .= '</select>';

      return $mHtml;
  } 

  function getLisTiptra( $mCodNivel = NULL)
  {
      // Tipo Transportadora
      $mSelect = "SELECT cod_tiptra, nom_tiptra FROM ".BASE_DATOS.".tab_genera_tiptra WHERE ind_estado = '1' ORDER BY 2";
      $consulta = new Consulta( $mSelect, $this -> conexion );
      $_TIPTRA = $consulta -> ret_matrix("a");
    
      $mHtml = '<select id="'.$mCodNivel.'ID" name="'.$mCodNivel.'">';
        $mHtml .= '<option value="">--</option>';
        foreach ($_TIPTRA as $mKey => $mValue) {
          $mHtml .= '<option value="'.$mValue["cod_tiptra"].'">'.$mValue["nom_tiptra"].'</option>';
        }
      $mHtml .= '</select>';

      return $mHtml;
  }
  
  function getLisZonasx( $mCodNivel = NULL)
  {
      // Zona
      $mSelect = "SELECT cod_zonaxx, nom_zonaxx FROM ".BASE_DATOS.".tab_genera_zonasx WHERE ind_estado = '1' ORDER BY 2";
      $consulta = new Consulta( $mSelect, $this -> conexion );
      $_ZONASX = $consulta -> ret_matrix("a");

      $mHtml = '<select id="'.$mCodNivel.'ID" name="'.$mCodNivel.'">';
        $mHtml .= '<option value="">--</option>';
        foreach ($_ZONASX as $mKey => $mValue) {
          $mHtml .= '<option value="'.$mValue["cod_zonaxx"].'">'.$mValue["nom_zonaxx"].'</option>';
        }
      $mHtml .= '</select>';

      return $mHtml;
  }

  function getLisCanalx( $mCodNivel = NULL)
  {
      // Canal
      $mSelect = "SELECT con_consec, nom_canalx FROM ".BASE_DATOS.".tab_genera_canalx WHERE ind_estado = '1' ORDER BY 2";
      $consulta = new Consulta( $mSelect, $this -> conexion );
      $_CANALX = $consulta -> ret_matrix("a");

      $mHtml = '<select id="'.$mCodNivel.'ID" name="'.$mCodNivel.'">';
        $mHtml .= '<option value="">--</option>';
        foreach ($_CANALX as $mKey => $mValue) {
          $mHtml .= '<option value="'.$mValue["con_consec"].'">'.$mValue["nom_canalx"].'</option>';
        }
      $mHtml .= '</select>';

      return $mHtml;
  } 
  function getLisDeposi ( $mCodNivel = NULL)
  {
      $mSelect = "SELECT cod_deposi, nom_deposi FROM ".BASE_DATOS.".tab_genera_deposi WHERE ind_estado = '1' ORDER BY 2";

      $consulta = new Consulta( $mSelect, $this -> conexion );
      $_DEPOSI = $consulta -> ret_matrix("a");

      $mHtml = '<select id="'.$mCodNivel.'ID" name="'.$mCodNivel.'">';
        $mHtml .= '<option value="">--</option>';
        foreach ($_DEPOSI as $mKey => $mValue) {
          $mHtml .= '<option value="'.$mValue["cod_deposi"].'">'.$mValue["nom_deposi"].'</option>';
        }
      $mHtml .= '</select>';

      return $mHtml;
  }


  
  private function getTransp( $cod_transp )
  {
    $mSql = "SELECT cod_tercer, UPPER(abr_tercer) AS nom_tercer FROM ".BASE_DATOS.".tab_tercer_tercer WHERE cod_tercer = '".$cod_transp."' LIMIT 1";
    $consulta = new Consulta( $mSql, $this -> conexion );
    return $consulta -> ret_matriz();
  }
  
  private function VerifyTranspor()
  {
    if ( $_SESSION['datos_usuario']['cod_perfil'] == NULL ) {
      //--------------------------
      //@PARA EL FILTRO DE EMPRESA
      //--------------------------
      $filtro = new Aplica_Filtro_Usuari( 1, COD_FILTRO_EMPTRA, $_SESSION['datos_usuario']['cod_usuari'] );
      if ( $filtro -> listar( $this -> conexion ) ) : 
      $datos_filtro = $filtro -> retornar();
      endif;
    }
    else { 
      //--------------------------
      //@PARA EL FILTRO DE EMPRESA
      //--------------------------
      $filtro = new Aplica_Filtro_Perfil( 1, COD_FILTRO_EMPTRA, $_SESSION['datos_usuario']['cod_perfil'] );
      if ( $filtro -> listar( $this -> conexion ) ) : 
      $datos_filtro = $filtro -> retornar();
      endif;
    }
    return $datos_filtro;
  }

}

$centro = new ModuloComunicaciones( $this -> conexion, $this -> usuario_aplicacion, $this-> codigo );


 ?>