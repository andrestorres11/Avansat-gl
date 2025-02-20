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
class AjaxSolNovAlarmas
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
      case "FormSoluciAlarmaNovedad":
        AjaxSolNovAlarmas::FormSoluciAlarmaNovedad();
        break;

      case "SaveSoluciNovedad":
        AjaxSolNovAlarmas::SaveSoluciNovedad();
        break;

      case "showInfoSolucion":
          AjaxSolNovAlarmas::showInfoSolucion();
          break;

      default:
        header('Location: index.php?window=central&cod_servic=1366&menant=1366');
        break;
    }
  }

  

  /*! \fn: FormSoluciAlarmaNovedad
   *  \brief: Formulario para dar solucion a la novedades con alarmas
   *  \author: Ing. Cristian Andrés Torres
   *  \date: 25/05/2015
   *  \date modified: dia/mes/aÃ±o
   *  \param: 
   *  \return:
   */
  function FormSoluciAlarmaNovedad() {
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/homolo.css' type='text/css'>\n";

    $mHtml  = '<form method="POST" id="formSoluciNovedad">';
    $mHtml .= '<table width="100%" cellspacing="1" cellpadding="5" border="0">';

    $fechaHoraActual = date('Y-m-d\TH:i');
    // Nueva fila: Campo textarea (por ejemplo, Detalle de la novedad)
    $mHtml .=   '<tr>';
    $mHtml .=     '<td colspan="2">';
    $mHtml .=       '<label for="detalle_novedad">Observación:</label><br>';
    $mHtml .=       '<textarea name="detalle_novedad" id="detalle_novedad" style="width: 100%; height: 80px;"></textarea>';
    $mHtml .=     '</td>';
    $mHtml .=   '</tr>';

    // Nueva fila: Checkbox "Solicita tiempo" y campo Fecha y Hora en la misma fila
    $mHtml .=   '<tr>';
    $mHtml .=     '<td>';
    $mHtml .=       '<input type="checkbox" name="solicita_tiempo" id="solicita_tiempo" onclick="toggleFechaHora()">';
    $mHtml .=       '<label for="solicita_tiempo"> Solicita tiempo</label>';
    $mHtml .=     '</td>';
    $mHtml .=     '<td style="display:none">';
    $mHtml .=         '<label for="fecha_hora">Fecha y Hora:</label><br>';
    $mHtml .=         '<input type="datetime-local" name="fecha_hora" id="fecha_hora" style="width: 100%;" value="'.$fechaHoraActual.'">';
    
    $mHtml .=     '</td>';
    $mHtml .=   '</tr>';

    // Campos ocultos
    $mHtml .=   '<input type="hidden" name="num_despac" id="num_despacSol" value="'.$_REQUEST['num_despac'].'">';
    $mHtml .=   '<input type="hidden" name="cod_noveda" id="cod_novedaSol" value="'.$_REQUEST['cod_noveda'].'">';
    $mHtml .=   '<input type="hidden" name="consec" id="consecSol" value="'.$_REQUEST['consec'].'">';
    $mHtml .=   '<input type="hidden" name="tip" id="tipSol" value="'.$_REQUEST['tip_noveda'].'">';
    
    // Botón de envío
    $mHtml .=   '<tr>';
    $mHtml .=     '<td colspan="2" align="center">';
    $mHtml .=       '<input class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="button" value="Aceptar" onclick="validateAndSubmitFormSoluci();">';
    $mHtml .=     '</td>';
    $mHtml .=   '</tr>';

    $mHtml .= '</table>';
    $mHtml .= '</form>';

    
    echo $mHtml;
  }

  function showInfoSolucion(){

    if ($_REQUEST["tip_noveda"] == 'NC') {
      $table = 'tab_despac_contro';
    } else {
        $table = 'tab_despac_noveda';
    }

    $mSql = " SELECT b.obs_soluci, b.sol_tiempo, b.tie_solici,
                     b.usr_creaci, b.fec_creaci
                  FROM ".BASE_DATOS.".".$table." a
                  INNER JOIN ".BASE_DATOS.".tab_soluci_noveda b ON a.cod_soluci = b.cod_soluci
                  WHERE a.num_despac = '".$_REQUEST["num_despac"]."'
                  AND   a.cod_consec = '".$_REQUEST["consec"]."'";
    $mConsult = new Consulta( $mSql, $this -> conexion );
    $infoResp = $mConsult -> ret_arreglo();
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/homolo.css' type='text/css'>\n";

    // Puedes ajustar la fecha según convenga (por ejemplo, la fecha actual o la fecha registrada)
    $fechaHoraActual = date('Y-m-d\TH:i');

    // Se arma la tabla con los datos en modo visualización (disabled)
    $mHtml  = '<table width="100%" cellspacing="1" cellpadding="5" border="0">';

    // Fila: Observación (textarea disabled)
    $mHtml .= '<tr>';
    $mHtml .=   '<td colspan="2">';
    $mHtml .=     '<label for="detalle_novedad">Observación:</label><br>';
    // Puedes sustituir el contenido entre las etiquetas <textarea> por la información registrada (por ejemplo, de una variable o base de datos)
    $mHtml .=     '<textarea name="detalle_novedad" id="detalle_novedad" style="width: 100%; height: 80px;" disabled>' 
                . (isset($infoResp['obs_soluci'] ) ? $infoResp['obs_soluci'] : '') 
                . '</textarea>';
    $mHtml .=   '</td>';
    $mHtml .= '</tr>';

    // Fila: Solicita tiempo (checkbox) y Fecha y Hora (input datetime-local)
    $mHtml .= '<tr>';
    $mHtml .=   '<td>';
    // Se utiliza el atributo disabled y se marca el checkbox si está definido y verdadero
    $mHtml .=     '<input type="checkbox" name="solicita_tiempo" id="solicita_tiempo" disabled ' 
                . ((isset($infoResp['sol_tiempo']) && $infoResp['sol_tiempo']) ? 'checked' : '') 
                . '>';
    $mHtml .=     '<label for="solicita_tiempo"> Solicita tiempo</label>';
    $mHtml .=   '</td>';

    if($infoResp['sol_tiempo']){
      $mHtml .=   '<td>';
      $mHtml .=     '<label for="fecha_hora">Fecha y Hora:</label><br>';
      $mHtml .=     '<input type="datetime-local" name="fecha_hora" id="fecha_hora" style="width: 100%;" value="'
                  . (isset($infoResp['tie_solici']) ? $infoResp['tie_solici'] : '') 
                  . '" disabled>';
      $mHtml .=   '</td>';
      $mHtml .= '</tr>';
    }
    

    // Fila nueva: Usuario de Solución y Fecha y Hora de Registro
    $mHtml .= '<tr>';
    $mHtml .=   '<td>';
    $mHtml .=     '<label for="usuario_solucion">Usuario de Solución:</label><br>';
    $mHtml .=     '<input type="text" name="usuario_solucion" id="usuario_solucion" style="width: 100%;" value="'
                . (isset($infoResp['usr_creaci']) ? $infoResp['usr_creaci'] : '') 
                . '" disabled>';
    $mHtml .=   '</td>';
    $mHtml .=   '<td>';
    $mHtml .=     '<label for="fecha_registro">Fecha y Hora de Registro:</label><br>';
    $mHtml .=     '<input type="datetime-local" name="fecha_registro" id="fecha_registro" style="width: 100%;" value="'
                . (isset($infoResp['fec_creaci']) ? $infoResp['fec_creaci'] : '') 
                . '" disabled>';
    $mHtml .=   '</td>';
    $mHtml .= '</tr>';

    $mHtml .= '</table>';
    echo $mHtml;
  }


  /*! \fn: SaveSoluciNovedad
   *  \brief: Formulario para dar solucion a las recomendaciones
   *  \author: Ing. Fabian Salinas
   *  \date: 25/05/2015
   *  \date modified: dia/mes/aÃ±o
   *  \param: 
   *  \return:
   */
  function SaveSoluciNovedad()
  {
    if($_REQUEST[solicita_tiempo]=='on'){
      $_REQUEST[solicita_tiempo]=1;
    }
    $cod_soluci = self::getMaxConsec();
    $usr_creaci = $_SESSION["datos_usuario"]["cod_usuari"];
    $mSql = " INSERT INTO ".BASE_DATOS.".tab_soluci_noveda
                  (cod_soluci, obs_soluci, sol_tiempo, tie_solici,
                   usr_creaci, fec_creaci) VALUES
                  ('".$cod_soluci."', '".$_REQUEST[detalle_novedad]."', '".$_REQUEST[solicita_tiempo]."', '".$_REQUEST[fecha_hora]."',
                    '".$usr_creaci."', NOW())";
    $mConsult = new Consulta( $mSql, $this -> conexion, "R" );

    self::UpdateNoveda($_REQUEST[tip], $cod_soluci, $_REQUEST[num_despac], $_REQUEST[consec]);
    if($_REQUEST[solicita_tiempo]){
      self::insertNovedadTiempo($_REQUEST[tip], $_REQUEST[num_despac], $_REQUEST[fecha_hora], $_REQUEST[detalle_novedad]);
    }


    echo json_encode(['success' => true, 'message' => 'Registro insertado correctamente']);
  }

  /*! \fn: UpdateNoveda
   *  \brief: Actualizar el codigo de la solucion de la novedad
   *  \author: Ing. Cristian Andrés Torres
   *  \date: 11/02/2025
   *  \date modified: dia/mes/aÃ±o
   *  \param: 
   *  \return:
   */
  function UpdateNoveda($tip, $cod_soluci, $num_despac, $consec)
  {
      // Se determina la tabla según el tipo de novedad (NC o PC)
      if ($tip == 'NC') {
          $table = 'tab_despac_contro';
      } else {
          $table = 'tab_despac_noveda';
      }
      
      // Verificar si el valor recibido en $consec tiene un guión ("-")
      if (strpos($consec, '-') !== false) {
          // Se trata de un rango, separamos los valores (mínimo y máximo)
          list($min, $max) = explode('-', $consec);
          $min = trim($min);
          $max = trim($max);
          $mSql = " UPDATE ".BASE_DATOS.".".$table."
                    SET cod_soluci = '".$cod_soluci."'
                    WHERE num_despac = '".$num_despac."' 
                      AND cod_consec BETWEEN '".$min."' AND '".$max."' ";
      } else {
          // Es un único consecutivo
          $mSql = " UPDATE ".BASE_DATOS.".".$table."
                    SET cod_soluci = '".$cod_soluci."'
                    WHERE num_despac = '".$num_despac."' 
                      AND cod_consec = '".$consec."' ";
      }
      
      $mConsult = new Consulta($mSql, $this->conexion, "R");
  }

  function insertNovedadTiempo($tip, $num_despac, $tiempo, $obs){
    if ($tip == 'NC') {
      $table = 'tab_despac_contro';
    } else {
        $table = 'tab_despac_noveda';
    }

    $fechaActual = new DateTime();
    $fechaNovedad = new DateTime($tiempo);
    
    // Calculamos la diferencia en segundos y obtenemos el valor absoluto
    $diferenciaSegundos = abs($fechaActual->getTimestamp() - $fechaNovedad->getTimestamp());
    
    // Convertimos los segundos a minutos
    $minutosTranscurridos = floor($diferenciaSegundos / 60);

    $tie_transacu = $minutosTranscurridos;

    $observacion = $obs.' - Solicitud de tiempo extra: '.$tiempo.' ('.  $tie_transacu . ')';
    $cod_noveda = 81;
    $ultNov = self::busquedaNoveda($num_despac, $tip);
    $usr_creaci = $_SESSION["datos_usuario"]["cod_usuari"];
    $mSql = " INSERT INTO ".BASE_DATOS.".".$table."
                  (num_despac, cod_contro, cod_rutasx, cod_consec,
                   obs_contro, val_longit, val_latitu, cod_noveda,
                   tiem_duraci, fec_contro, val_retras, cod_sitiox,
                   cod_sitioy, ind_sitiox, kms_vehicu, cod_usrcre,
                   ind_enviad, usr_creaci, fec_creaci) VALUES
                  ('".$ultNov['num_despac']."', '".$ultNov['cod_contro']."', '".$ultNov['cod_rutasx']."', '".($ultNov['cod_consec']+1)."',
                   '".$observacion."',NULL, NULL, '".$cod_noveda."',
                   '".$tie_transacu."', NOW(), '".$ultNov['val_retras']."', '".$ultNov['cod_sitiox']."',
                   '".$ultNov['cod_sitioy']."', '".$ultNov['ind_sitiox']."', '".$ultNov['kms_vehicu']."', '".$ultNov['cod_usrcre']."',
                   0, '".$usr_creaci."', NOW())";
    $mConsult = new Consulta( $mSql, $this -> conexion, "R" );
  }

  function busquedaNoveda($num_despac, $tip){
    if ($tip == 'NC') {
      $table = 'tab_despac_contro';
    } else {
        $table = 'tab_despac_noveda';
    }
    $mSql = " SELECT *
                  FROM ".BASE_DATOS.".".$table." 
                  WHERE num_despac = '".$num_despac."' order by fec_creaci DESC";
    $mConsult = new Consulta( $mSql, $this -> conexion );
    $ultima = $mConsult -> ret_arreglo();
    return $ultima;
  }


  function getMaxConsec(){
    $mSql = " SELECT MAX(cod_soluci)
                  FROM ".BASE_DATOS.".tab_soluci_noveda ";
    $mConsult = new Consulta( $mSql, $this -> conexion );
    $cod_consec = $mConsult -> ret_arreglo();
    return $cod_consec[0] + 1;
  }

  function getNoveltiesSimi($num_despac, $cod_noveda, $tip){

    if($tip == 'NC'){
      $table = 'tab_despac_contro';
    }else{
      $table = 'tab_despac_noveda';
    }

    $mSql = "SELECT UPPER(b.nom_noveda) AS nom_noveda, a.cod_contro, g.cod_tipoxx,
                                a.ind_enviad, f.ind_resfar, b.cod_riesgo, a.cod_consec,
                                a.cod_noveda, a.fec_creaci
                        FROM ".BASE_DATOS.".".$table." a
                    INNER JOIN ".BASE_DATOS.".tab_genera_noveda b
                            ON a.cod_noveda = b.cod_noveda
                    INNER JOIN ".BASE_DATOS.".tab_genera_contro d
                            ON a.cod_contro = d.cod_contro
                    INNER JOIN ".BASE_DATOS.".tab_despac_vehige e
                            ON a.num_despac = e.num_despac
                    LEFT JOIN ".BASE_DATOS.".tab_parame_novseg f
                            ON b.cod_noveda = f.cod_noveda AND
                                f.cod_transp = e.cod_transp
                    LEFT JOIN ".BASE_DATOS.".tab_noveda_tipoxx g
                                ON b.cod_tipoxx = g.cod_tipoxx
                        WHERE a.num_despac = '".$num_despac."'
                        AND   f.ind_reqres = 1
                        AND   a.cod_noveda = '".$cod_noveda."'
                        AND   (a.cod_soluci = 0 OR a.cod_soluci = '' OR a.cod_soluci IS NULL)
                        ORDER BY a.fec_creaci ASC
                    ";
    $mConsult = new Consulta( $mSql, $this -> conexion );
    $registros = $mConsult -> ret_matrix('a');

    // Código de novedad a agrupar (por ejemplo, 57)
    $codigoBuscado = $cod_noveda;

    // Inicializa el arreglo que contendrá los grupos y la variable para el grupo actual.
    $grupos = array();
    $grupoActual = null;

    // Recorre cada registro obtenido.
    foreach ($registros as $registro) {
        // Si el registro pertenece al código buscado...
        if ($registro['cod_noveda'] == $codigoBuscado) {
            if ($grupoActual === null) {
                // Se inicia un nuevo grupo con el primer registro.
                $grupoActual = array(
                    'fecha_inicio'       => $registro['fec_creaci'],
                    'fecha_fin'          => $registro['fec_creaci'],
                    'cod_consec_inicio'  => $registro['cod_consec'],
                    'cod_consec_fin'     => $registro['cod_consec'],
                    'registros'          => array($registro)
                );
            } else {
                // Se agrega el registro al grupo actual y se actualizan los datos (fecha final y consecutivo final).
                $grupoActual['fecha_fin'] = $registro['fec_creaci'];
                $grupoActual['cod_consec_fin'] = $registro['cod_consec'];
                $grupoActual['registros'][] = $registro;
            }
        } else {
            // Si el registro no tiene el código buscado y hay un grupo abierto, se finaliza el grupo.
            if ($grupoActual !== null) {
                $grupos[] = $grupoActual;
                $grupoActual = null;
            }
            // Si lo deseas, podrías almacenar o procesar de otro modo los registros que no cumplen el criterio.
        }
    }

    // Al finalizar el ciclo, si quedó un grupo abierto, se agrega al arreglo de grupos.
    if ($grupoActual !== null) {
        $grupos[] = $grupoActual;
    }

    return $grupos;
  }

}

$proceso = new AjaxSolNovAlarmas($_SESSION['conexion'], $_SESSION['usuario_aplicacion'], $_SESSION['codigo']);

?>