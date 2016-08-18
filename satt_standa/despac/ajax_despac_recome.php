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
class AjaxRecome
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
      case "FormDespacRecome":
        AjaxRecome::FormDespacRecome();
        break;

      case "SoluciRecome":
        AjaxRecome::SoluciRecome();
        break;

      case "UpdSoluciRecome":
        AjaxRecome::UpdSoluciRecome();
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
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return:
   */
  function FormDespacRecome()
  {
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/homolo.css' type='text/css'>\n";

    $mSql = "SELECT a.cod_consec, a.tex_encabe, a.des_texto, 
                    IF( a.ind_requer = '1' ,'SI', 'NO')
               FROM ". BASE_DATOS .".tab_genera_recome a ";
    $consulta = new Consulta( $mSql, $this -> conexion );
    $mArrayData = $consulta -> ret_matrix('i');

    $mHtml  = '<table width="100%" cellspacing="1" cellpadding="0" border="0">';

    $mHtml .=   '<tr>';
    $mHtml .=     '<th class="CellHead">Solicitar</th>';
    $mHtml .=     '<th class="CellHead">Recomendaci&oacute;n</th>';
    $mHtml .=     '<th class="CellHead">Obligatorio</th>';
    $mHtml .=   '</tr>';

    foreach ($mArrayData as $row) {
      $mHtml .= '<tr>';
      $mHtml .=   '<td class="cellInfo"><input type="checkbox" name="cod_consec" id="cod_consecID" value="'.$row[0].'=> '.$row[1].'" ></td>';
      $mHtml .=   '<td class="cellInfo">'.$row[1].'</td>';
      $mHtml .=   '<td class="cellInfo">'.$row[3].'</td>';
      $mHtml .= '</tr>';
    }

    $mHtml .= '<tr><td colspan="3" align="right"> <input class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="button" value="Aceptar" onclick="setObserRecome();" /> </td></tr>';

    $mHtml .= '</table>';

    echo $mHtml;
  }

  /*! \fn: SoluciRecome
   *  \brief: Formulario para dar solucion a las recomendaciones
   *  \author: Ing. Fabian Salinas
   *  \date: 25/05/2015
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return:
   */
  function SoluciRecome()
  {
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/homolo.css' type='text/css'>\n";

    $mCodRecome = '';

    $mSql = "SELECT a.des_texto,  a.htm_config, a.ind_requer, 
                    b.num_condes, b.num_despac, b.cod_contro, 
                    b.cod_noveda, b.cod_rutasx, b.cod_recome, 
                    a.cod_tipoxx 
               FROM ".BASE_DATOS.".tab_genera_recome a 
         INNER JOIN ".BASE_DATOS.".tab_recome_asigna b 
                 ON a.cod_consec = b.cod_recome 
              WHERE b.num_despac = '".$_REQUEST[num_despac]."' 
                AND b.cod_contro = '".$_REQUEST[cod_contro]."' 
                AND b.ind_ejecuc = '0' 
         ";
    $mConsult = new Consulta( $mSql, $this -> conexion );
    $mArrayData = $mConsult -> ret_matrix('i');

    $mHtml  = '<form name="formSoluciRecome" id="formSoluciRecomeID" method="POST" action="?" >';
    $mHtml .= '<table width="100%" cellspacing="1" cellpadding="0" border="0">';

    $mHtml .=   '<tr>';
    $mHtml .=     '<th class="CellHead">#</th>';
    $mHtml .=     '<th class="CellHead">Obligatorio</th>';
    $mHtml .=     '<th class="CellHead">Recomendaci&oacute;n</th>';
    $mHtml .=     '<th class="CellHead">Respuesta</th>';
    $mHtml .=   '</tr>';
    $mCon = 0;
    foreach ($mArrayData as $row) {
      $mCodRecome .= $mCodRecome == '' ? $row[8] : '|'.$row[8];
      $mHtml .= '<tr>';
      $mHtml .=   '<td class="cellInfo" align="center" >'.($mCon+1).'</td>';
      $mHtml .=   '<td class="cellInfo" align="center" >'.( $row[2] == 0 ? 'NO' : 'SI' ).'</td>';
      $mHtml .=   '<td class="cellInfo">'.$row[0].'</td>';
      $mHtml .=   '<td class="cellInfo">
                    <span id="mInput'.$mCon.'ID" >
                      '.$row[1].'
                      <input id="InputsForm'.$mCon.'ID" type="hidden"  ind_requer="'.$row[2].'" num_condes="'.$row[3].'" num_despac="'.$row[4].'" cod_contro="'.$row[5].'" cod_noveda="'.$row[6].'" cod_rutasx="'.$row[7].'" cod_recome="'.$row[8].'" cod_tipoxx="'.$row[9].'"  />
                    </span>
                   </td>';

      $mHtml .= '</tr>';
      $mCon ++;
    }

    $mHtml .= '<tr><td colspan="3" align="right"> <input class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="button" value="Aceptar" onclick="setSoluciRecome();" /> </td></tr>';

    $mHtml .= '<input type="hidden" name="dir_aplica" id="dir_aplicaID" value="'.$_REQUEST[standa].'" >';
    $mHtml .= '<input type="hidden" name="num_despac" id="tot_inputsID" value="'.$_REQUEST[num_despac].'" >';
    $mHtml .= '<input type="hidden" name="tot_inputs" id="tot_inputsID" value="'.$mCon.'" >';
    $mHtml .= '<input type="hidden" name="cod_recome" id="cod_recomeID" value="'.$mCodRecome.'" >';

    $mHtml .= '</table>';
    $mHtml .= '</form>';

    echo $mHtml;
  }

  /*! \fn: UpdSoluciRecome
   *  \brief: Cambia el estado de las recomendaciones a ejecutadas
   *  \author: Ing. Fabian Salinas
   *  \date: 25/05/2015
   *  \date modified: dia/mes/año
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

$proceso = new AjaxRecome($_SESSION['conexion'], $_SESSION['usuario_aplicacion'], $_SESSION['codigo']);

?>