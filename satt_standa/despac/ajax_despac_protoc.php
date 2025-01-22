<?php
/*! \file: ajax_despac_protoco.php
 *  \brief: Ajax para traer los protocolos y formulario de una novedad.
 *  \author: Ing. Cristian Torres
 *  \version: 1.0
 *  \date: 17/01/2025
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */

/*ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);*/

session_start();

/*! \class: AjaxRecome
 *  \brief: Ajax para los procesos de las recomendaciones
 */
class AjaxProtoco
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
      case "FormProtocolo":
        AjaxProtoco::FormProtocolo();
        break;

      case "SaveNovedadOperativoPreventivo":
        AjaxProtoco::SaveNovedadOperativoPreventivo();
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
  function FormProtocolo() {
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/homolo.css' type='text/css'>\n";


    $queryParame = "SELECT c.cod_matpr, c.cod_formul, c.nom_observ, d.des_protoc
                        FROM " . BASE_DATOS . ".tab_despac_despac a 
                        LEFT JOIN " . BASE_DATOS . ".tab_despac_vehige b ON a.num_despac = b.num_despac 
                        LEFT JOIN " . BASE_DATOS . ".tab_parame_protoc c ON b.cod_transp = c.cod_transp 
                        LEFT JOIN " . BASE_DATOS . ".tab_genera_matpro d ON c.cod_matpr = d.cod_matpr
                        WHERE a.num_despac = " . $_REQUEST['num_despac'] . " AND
                              c.cod_noveda = '".$_REQUEST[noved]."'; ";    
    $consultaPar = new Consulta($queryParame, $this->conexion);
    $parameProt = $consultaPar->ret_matriz();

    $queryFormul = "SELECT 
                        b.cod_consec, b.nom_campox, b.ind_tipoxx, 
                        b.val_htmlxx, b.val_option, b.val_minimo, 
                        b.val_maximo 
                      FROM 
                        " . BASE_DATOS . ".tab_formul_detail a 
                        INNER JOIN " . BASE_DATOS . ".tab_formul_campos b ON a.cod_campox = b.cod_consec 
                      WHERE 
                        a.cod_formul = '".$parameProt[0]['cod_formul']."' 
                        AND b.ind_estado = 1 
                      ORDER BY 
                        num_ordenx ASC";    
    $consultaForm = new Consulta($queryFormul, $this->conexion);
    $campos = $consultaForm->ret_matriz();

    $mForm = '';
    foreach($campos as $campo){
      $mForm .= '<div style="margin-bottom: 10px;">';
      $mForm .=    '<label for="formul_campo_'.$campo['cod_consec'].'" style="display: block; margin-bottom: 5px; font-size: 12px">'.$campo['nom_campox'].':</label>';
      $mForm .=    $campo['val_htmlxx'];
      $mForm .= '</div>';
    }

    $mHtml = '<div style="width: 100%; max-width: 800px;">
                <!-- Gestión a realizar -->
                <div>
                    <div style="background-color: #2c368d; color: white; padding: 6px; font-size: 12px; font-weight: bold; border: 1px solid #0056b3; border-radius: 0;">
                        Gestión a realizar en caso de
                    </div>
                    <div style="border: 1px solid #c4c4c4; border-top: none; padding: 6px; font-size: 12px; line-height: 1.0; color: #333;">
                        '.$parameProt[0]['des_protoc'].'
                    </div>
                </div>

                <!-- Acción acordada -->
                <div>
                    <div style="background-color: #2c368d; color: white; padding: 6px; font-size: 12px; font-weight: bold; border: 1px solid #0056b3; border-radius: 0;">
                        Acción Acordada
                    </div>
                    <div style="border: 1px solid #c4c4c4; border-top: none; padding: 6px; font-size: 12px; line-height: 1.0; color: #333;">
                        <p>'.$parameProt[0]['nom_observ'].'</p>
                    </div>
                </div>

                <!-- Formato -->
                <div>
                    <div style="background-color: #2c368d; color: white; padding: 6px; font-size: 12px; font-weight: bold; border: 1px solid #0056b3; border-radius: 0;">
                        Formato
                    </div>
                    <form name="form-protoco" id="form-protoco">
                    <div style="border: 1px solid #c4c4c4; border-top: none; padding: 6px; display: flex; gap: 20px; align-items: flex-start;">
                        <!-- Formulario -->
                          <div style="flex: 1;">
                              '.$mForm.'
                          </div>
                        <!-- Visualización -->
                        <div style="flex: 1; display: flex; align-items: center; justify-content: center; margin-top: 10px;">
                            <div class="visual-content" style="width: 100%; height: auto; min-height: 200px; padding:10px; border: 1px solid #ccc; background-color: #f9f9f9; display: flex; color: #aaa; font-size: 14px;">Pre visualización</div>
                        </div>
                    </div>
                    </form>
                </div>

                <!-- Botón -->
                <div style="text-align: center; margin-top:8px">
                    <button type="submit" style="padding: 7px; color: #fff; background-color: #2c368d; border: none; border-radius: 12px; cursor: pointer;" onclick="validateForm()">Aceptar</button>
                </div>
            </div>';

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

$proceso = new AjaxProtoco($_SESSION['conexion'], $_SESSION['usuario_aplicacion'], $_SESSION['codigo']);

?>