<?php
/*! \file: ajax_despac_recome.php
 *  \brief: Ajax para los procesos de las recomendaciones
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 25/05/2015
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */

ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);

session_start();

/*! \class: AjaxRecome
 *  \brief: Ajax para los procesos de las recomendaciones
 */
class AjaxOppPrev
{

  var $conexion,
      $cod_aplica,
      $usuario;

  function __construct($co, $us, $ca)
  {
    $this->conexion = $co;
    $this->usuario = $us;
    $this->cod_aplica = $ca;
    $this->principal();
  }

  function principal()
  {
    @include( "../lib/ajax.inc" );

    $this -> conexion = $AjaxConnection;

    switch ($_REQUEST[Option])
    {
      case "FormNovedadOperativoPreventivo":
        AjaxOppPrev::FormNovedadOperativoPreventivo();
        break;

      case "SaveNovedadOperativoPreventivo":
        AjaxOppPrev::SaveNovedadOperativoPreventivo();
        break;

      default:
        header('Location: index.php?window=central&cod_servic=1366&menant=1366');
        break;
    }
  }

  

  /*! \fn: FormDespacRecome
   *  \brief: Formulario para asignar recomendaciones a despachos
   *  \author: Ing. Fabian Salinas
   *  \date: 25/05/2015
   *  \date modified: dia/mes/aÃ±o
   *  \param: 
   *  \return:
   */
  function FormNovedadOperativoPreventivo() {
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/homolo.css' type='text/css'>\n";

    $mHtml  = '<form method="POST" id="formOperativoPreventivo">';
    $mHtml .= '<table width="100%" cellspacing="1" cellpadding="5" border="0">';

    // Primera fila (Persona y Número de contacto)
    $mHtml .=   '<tr>';
    $mHtml .=     '<td>';
    $mHtml .=       '<label for="persona_impulsa">Persona que impulsa el caso:</label><br>';
    $mHtml .=       '<input type="text" name="persona_impulsa" id="persona_impulsa" style="width: 100%;">';
    $mHtml .=     '</td>';
    $mHtml .=     '<td>';
    $mHtml .=       '<label for="numero_contacto">Número de contacto:</label><br>';
    $mHtml .=       '<input type="text" name="numero_contacto" id="numero_contacto" style="width: 100%;">';
    $mHtml .=     '</td>';
    $mHtml .=   '</tr>';

    // Segunda fila (Empresa y Mercancía)
    $mHtml .=   '<tr>';
    $mHtml .=     '<td>';
    $mHtml .=       '<label for="empresa_generadora">Empresa generadora de la carga:</label><br>';
    $mHtml .=       '<input type="text" name="empresa_generadora" id="empresa_generadora" style="width: 100%;">';
    $mHtml .=     '</td>';
    $mHtml .=     '<td>';
    $mHtml .=       '<label for="mercancia_transportada">Mercancía transportada:</label><br>';
    $mHtml .=       '<input type="text" name="mercancia_transportada" id="mercancia_transportada" style="width: 100%;">';
    $mHtml .=     '</td>';
    $mHtml .=   '</tr>';

    // Tercera fila (Último reporte y Descripción)
    $mHtml .=   '<tr>';
    $mHtml .=     '<td>';
    $mHtml .=       '<label for="ultimo_reporte">Último reporte:</label><br>';
    $mHtml .=       '<textarea name="ultimo_reporte" id="ultimo_reporte" style="width: 100%; height: 80px;"></textarea>';
    $mHtml .=     '</td>';
    $mHtml .=     '<td>';
    $mHtml .=       '<label for="descripcion_caso">Descripción del caso:</label><br>';
    $mHtml .=       '<textarea name="descripcion_caso" id="descripcion_caso" style="width: 100%; height: 80px;"></textarea>';
    $mHtml .=     '</td>';
    $mHtml .=   '</tr>';

    // Cuarta fila (Latitud y Longitud)
    $mHtml .=   '<tr>';
    $mHtml .=     '<td>';
    $mHtml .=       '<label for="latitud">Latitud:</label><br>';
    $mHtml .=       '<input type="text" name="latitud" id="latitud" style="width: 100%;">';
    $mHtml .=     '</td>';
    $mHtml .=     '<td>';
    $mHtml .=       '<label for="longitud">Longitud:</label><br>';
    $mHtml .=       '<input type="text" name="longitud" id="longitud" style="width: 100%;">';
    $mHtml .=     '</td>';
    $mHtml .=   '</tr>';

    $mHtml .=   '<input type="hidden" name="ori" id="ori" value="'.$_REQUEST['ori'].'">';
    $mHtml .=   '<input type="hidden" name="consec" id="consec" value="'.$_REQUEST['consec'].'">';
    // Botón
    $mHtml .=   '<tr>';
    $mHtml .=     '<td colspan="2" align="center">';
    $mHtml .=       '<input class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="button" value="Aceptar" onclick="validateAndSubmitForm();">';
    $mHtml .=     '</td>';
    $mHtml .=   '</tr>';

    $mHtml .= '</table>';
    $mHtml .= '</form>';

    echo utf8_encode($mHtml);
}

  /*! \fn: SaveNovedadOperativoPreventivo
   *  \brief: Formulario para dar solucion a las recomendaciones
   *  \author: Ing. Fabian Salinas
   *  \date: 25/05/2015
   *  \date modified: dia/mes/aÃ±o
   *  \param: 
   *  \return:
   */
  function SaveNovedadOperativoPreventivo()
  {
    $mSql = " INSERT INTO ".BASE_DATOS.".tab_noveda_opprev
                  (num_despac, cod_connov, tip_seguim,
                   nom_person, num_contac, gen_cargax,
                   mer_transp, ult_report, des_casoxx,
                   val_latitu, val_longit, usr_creaci,
                   fec_creaci) VALUES
                  ('".$_REQUEST[num_despac]."', '".$_REQUEST[consec]."', '".$_REQUEST[ori]."',
                   '".utf8_decode($_REQUEST[persona_impulsa])."', '".$_REQUEST[numero_contacto]."', '".utf8_decode($_REQUEST[empresa_generadora])."',
                    '".utf8_decode($_REQUEST[mercancia_transportada])."', '".utf8_decode($_REQUEST[ultimo_reporte])."', '".utf8_decode($_REQUEST[descripcion_caso])."',
                    '".$_REQUEST[latitud]."', '".$_REQUEST[longitud]."', '', NOW())";
    $mConsult = new Consulta( $mSql, $this -> conexion, "R" );
    echo json_encode(['success' => true, 'message' => 'Registro insertado correctamente']);
  }

  /*! \fn: UpdSoluciRecome
   *  \brief: Cambia el estado de las recomendaciones a ejecutadas
   *  \author: Ing. Fabian Salinas
   *  \date: 25/05/2015
   *  \date modified: dia/mes/aÃ±o
   *  \param: 
   *  \return:
   */
  function UpdSoluciRecome()
  {
    $mTxt = '';

    foreach ($_REQUEST[data] as $row) 
    {
      $mSql = " UPDATE ".BASE_DATOS.".tab_recome_asigna 
                SET obs_ejecuc = '$row[obs_ejecuc]'
                WHERE num_despac = '$_REQUEST[num_despac]' 
                  AND num_condes = '$row[num_condes]' ";
      $mConsult = new Consulta( $mSql, $this -> conexion, "R" );

      $mSql = " SELECT UPPER(a.tex_encabe)
                  FROM ".BASE_DATOS.".tab_genera_recome a
            INNER JOIN ".BASE_DATOS.".tab_recome_asigna b 
                    ON a.cod_consec = b.cod_recome 
                 WHERE b.num_despac = '$_REQUEST[num_despac]' 
                   AND b.num_condes = '$row[num_condes]' ";
      $mConsult = new Consulta( $mSql, $this -> conexion );
      $mTextEncabe = $mConsult -> ret_arreglo();

      $mTxt .= $mTextEncabe[0].': '.$row[obs_ejecuc].'. ||';
    }

    echo $mTxt;

  }

}

$proceso = new AjaxOppPrev($_SESSION['conexion'], $_SESSION['usuario_aplicacion'], $_SESSION['codigo']);

?>