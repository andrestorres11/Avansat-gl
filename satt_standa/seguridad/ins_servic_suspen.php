<?php
/* ! \archive: ins_servic_suspen.php
 *  \brief: archivo para el manejo de la suspencion de servicos a empresas
 *  \author: Ing. Alexander Correa
 *  \author: aleander.correa@intrared.net
 *  \date: 15/04/2016
 *  \date modified: dia/mes/año
 *  \warning:   
 *  \ 
 */

/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/

session_start();

require "ajax_perfil_perfil.php";
require_once '../'.DIR_APLICA_CENTRAL.'/lib/general/suspensiones.php';

class ins_servic_suspen {

    var $conexion, $usuario, $cod_aplica;
    private static $cFunciones;

    function __construct($co, $us, $ca) {
        #importa los  css y js necesarios
        ?>

        <script src= "../<?= DIR_APLICA_CENTRAL ?>/js/DataTables/js/jquery-3.3.1.js" language="javascript"></script>
        <script src= "../<?= DIR_APLICA_CENTRAL ?>/js/DataTables/js/jquery.dataTables.min.js" language="javascript"></script>
        <script src= "../<?= DIR_APLICA_CENTRAL ?>/js/lib/datatables.net-bs/js/dataTables.bootstrap.min.js" type="text/javascript"></script>
        <script src= "../<?= DIR_APLICA_CENTRAL ?>/js/lib/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src= "../<?= DIR_APLICA_CENTRAL ?>/js/moment.min.js" type="text/javascript"></script>
        <script src= "../<?= DIR_APLICA_CENTRAL ?>/js/DataTables/js/dataTables.buttons.min.js" language="javascript"></script>
        <script src= "../<?= DIR_APLICA_CENTRAL ?>/js/DataTables/js/buttons.flash.min.js" language="javascript"></script>
        <script src= "../<?= DIR_APLICA_CENTRAL ?>/js/DataTables/js/jszip.min.js" language="javascript"></script>
        <script src= "../<?= DIR_APLICA_CENTRAL ?>/js/DataTables/js/pdfmake.min.js" language="javascript"></script>
        <script src= "../<?= DIR_APLICA_CENTRAL ?>/js/DataTables/js/vfs_fonts.js" language="javascript"></script>
        <script src= "../<?= DIR_APLICA_CENTRAL ?>/js/DataTables/js/buttons.html5.min.js" language="javascript"></script>
        <script src= "../<?= DIR_APLICA_CENTRAL ?>/js/DataTables/js/daterangepicker.min.js" type="text/javascript" ></script>
        <script src= "../<?= DIR_APLICA_CENTRAL ?>/js/suspension.js?rand=<?= rand(1500, 50000) ?>" language="javascript"></script>
        <link href="../<?= DIR_APLICA_CENTRAL ?>/js/lib/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../<?= DIR_APLICA_CENTRAL ?>/js/lib/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../<?= DIR_APLICA_CENTRAL ?>/js/DataTables/css/daterangepicker.css" rel="stylesheet" type="text/css"/>

        <?php
        $this->conexion = $co;
        $this->usuario = $us;
        $this->cod_aplica = $ca;
        self::$cFunciones = new seguri($co, $us, $ca);
        switch ($_REQUEST[opcion]) {
            case 1: //registrar nuevo
            case 2: //editar            
                $this->Formulario();
                break;
            default:
                $this->lista();
                break;
        }
    }

    /* ! \fn: lista
     *  \brief: funcion inicial que muestra una lista con las empresas y los servicios contratados
     *  \author: Ing. Alexander Correa
     *  \date: 15/04/2016
     *  \date modified: dia/mes/a�o
     *  \param: 
     *  \return 
     */

    function lista() {

        $datos_usuario = $this->usuario->retornar();
        $usuario = $datos_usuario["cod_usuari"];
        $sus_terceros = new suspensiones($this->conexion);
        $data = $sus_terceros->SetSuspensiones();

        
        $html = '</table>
                    <div> 
                        <div class="panel-group" id="accordion">
                          <div class="panel panel-default">
                            <div class="panel-heading">
                              <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#filtro">
                                    Filtros
                                </a>
                              </h4>
                            </div>
                            <div id="filtro" class="panel-collapse collapse in">
                              <div class="panel-body">
                                  <div class="form-row">
                                    <div class="form-group col-md-4">
                                      <label for="fec_rangox">Fechas</label>
                                      <input type="text" class="form-control" id="fec_rangox">
                                    </div> 
                                  </div>
                              </div>
                            </div>
                          </div>
                          <div class="panel panel-default">
                            <div class="panel-heading">
                              <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#tablaDatos">
                                    Informaci�n de suspendidos
                                </a>
                              </h4>
                            </div>
                            <div id="tablaDatos" class="panel-collapse collapse in" style="overflow: auto;">
                              <div class="panel-body">
                                <table id="tablaSuspend" class="table table-striped table-bordered" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Documento Tercero</th>
                                            <th>Nombre Tercero</th>
                                            <th>Fecha Suspensi�n</th>
                                            <th>N�mero de factura</th>
                                            <th>Saldo Pendiente</th>
                                            <th>Estado Suspensi�n</th>
                                            <th>Detalle Suspensi�n</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ';

                                        foreach ($data as $estadoSus => $estados) {
                                            foreach ($estados as $campo => $datos) {
                                                # code...
                                            
                                                $dateNow = new DateTime();
                                                $dateSus = new DateTime($datos['fec_suspen']);
                                                $interval = $dateNow->diff($dateSus);

                                                if($datos['fec_suspen'] < date('Y-m-d')){
                                                    $datos['est_suspen'] = 'Suspendido';
                                                    $datos['det_suspen'] = $interval->format('Suspendido, %a d�a(s) de suspensi�n');
                                                }else{
                                                    $datos['det_suspen'] = $interval->format('Faltan %a d�a(s) para ser suspendido');
                                                    $datos['est_suspen'] = 'Proximo a suspender';
                                                }

                                                 $html.= '<tr>
                                                            <td>'.$datos['cod_tercer'].'</td>
                                                            <td>'.$datos['abr_tercer'].'</td>
                                                            <td>'.$datos['fec_suspen'].'</td>
                                                            <td>'.$datos['num_factur'].'</td>
                                                            <td>$'.$datos['val_totalx'].'</td>
                                                            <td>'.$datos['est_suspen'].'</td>
                                                            <td>'.$datos['det_suspen'].'</td>
                                                          </tr>'
                                                         ;
                                            }
                                        }
                                        $html .='
                                    </tbody>
                                </table>
                              </div>
                            </div>
                          </div>
                        </div>
                    </div>  ';

        

        echo $html;
        
    }

    /* ! \fn: formulario
     *  \brief: pinta el formulario para editar una configuracion de transportadora
     *  \author: Ing. Alexander Correa
     *  \date: 15/04/2016
     *  \date modified: dia/mes/a�o
     *  \param: 
     *  \return 
     */

    function Formulario() {
       $datos = (object) $_POST;
      
       ?>
       </table>
       <form action="index.php" method="post" name="form_search" id="form_searchID" enctype="multipart/form-data">
            <input type="hidden" name="cod_servic" id="cod_servic" value="<?= $_REQUEST['cod_servic'] ?>"/>
            <input type="hidden" name="standa" id="standa" value="<?= DIR_APLICA_CENTRAL ?>"/>
            <input type="hidden" name="window" id="window" value="central"/>
       </form>
       <div class="accordion">
       <h1 style="padding:6px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>INFORMACI&Oacute;N B&Aacute;SICA DEL CLIENTE</b></h1>
       <div id="contenido">
            <div class="Style2DIV">
                <table width="100%" cellspacing="0" cellpadding="0">
                    <tbody>
                        <tr>
                            <td style="text-align:center" class="CellHead contenido">
                                <div class="col-md-12 ancho">
                                    <div class="col-md-3 text-right">NIT</div>
                                    <div class="col-md-3 text-left">
                                        <input class="text-center" type="text" readonly name="cod_tercer" id="cod_tercer" value="<?= $datos->cod_tercer ?>"/>
                                    </div>
                                    <div class="col-md-3 text-right">Transportadora </div>
                                    <div class="col-md-3 text-left">
                                        <input type="text" class="ancho text-center" readonly name="nom_tercer" id="nom_tercer" value="<?= $datos->nom_tercer ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-12 CellHead">
                                    <b>Suspenci&oacute;n/Activaci&oacute;n</b>
                                </div>
                                <div class="col-md-12 text-center">
                                <div class="col-md-5"></div>
                                <div class="col-md-2">
                                    <select id="tip_bitaco" name="tip_bitaco" obl="1" validate="select">
                                            <option value="">Seeccione una Opci&oacute;n</option>
                                            <option value="1">Activaci&oacute;n</option>
                                            <option value="0">Suspenci&oacute;n</option>
                                    </select>
                                </div>
                                <div class="col-md-5"></div>
                                    
                                </div>
                                <div class="col-md-12 contenido">
                                    <div class="col-md-2">Fecha:</div>
                                    <div class="col-md-2"><input type="text" obl="1" validate="date" maxlength="10" minlength="10" class="date text-center" id="fec_operac" name="fec_operac" readonly /></div>
                                    <div class="col-md-2">Hora:</div>
                                    <div class="col-md-2"><input type="text" obl="1" validate="dir" maxlength="5" minlength="5" class="time text-center" id="hor_operac" name="hor_operac" readonly onfocus="removeStyle('hor_operac')" /></div>
                                    <div class="col-md-2">Observaci&oacute;n:</div>
                                    <div class="col-md-2"><input type="text" obl="1" validate="texto" maxlength="200" minlength="2"  class="text-center" name="obs_operac" id="obs_suspen" placeholder="Ingresa una observaci&oacute;n"/></div>
                                </div> 
                                <div class="col-md-12 CellHead">
                                    <b>Servicios a Suspender/Activar</b>
                                </div>
                                 <div class="col-md-12 ancho">
                                    <div class="col-md-3 text-right">EAL</div>
                                    <div class="col-md-3 text-left">
                                        <input type="checkbox" name="sus_ealxxx" id="sus_ealxxx" value="1"/>
                                    </div>
                                    <div class="col-md-3 text-right">M/A </div>
                                    <div class="col-md-3 text-left">
                                        <input type="checkbox" name="sus_monati" id="sus_monati" value="2"/>
                                    </div>
                                </div>
                                <div class="col-md-12 CellHead">
                                    <b>Notificaciones</b>
                                </div>
                                <div class="col-md-12"></div>
                                <div class="col-md-12 CellHead">
                                    <div class="col-md-3"><b>Email</b></div>
                                    <div class="col-md-8">
                                        <div class="col-md-6"><b>Desde:</b></div>
                                        <div class="col-md-6"><b>Hasta:</b></div>
                                        <div class="col-md-12 contenido"></div>
                                        <div class="col-md-3"><b>Fecha</b></div>
                                        <div class="col-md-3"><b>Hora</b></div>
                                        <div class="col-md-3"><b>Fecha</b></div>
                                        <div class="col-md-3"><b>Hora</b></div>
                                    </div>
                                    <div class="col-md-1"><b>Eliminar</b></div>                                   
                                </div>
                                <div class="col-md-12 text-center">Para agregar otro Email haga click <a style="cursor: pointer; color: black; text-decoration: none" onclick="addMail()"><b>aqu&iacute;...</b></a></div> 
                                <div id="emails" class="col-md-12 ancho">
                                    <?php $emails = explode(",",$datos->dir_emailx);
                                    foreach ($emails as $key => $value) {
                                        if(trim($value)!= ""){
                                       ?>
                                           <div id="email<?= $key ?>" class="col-md-12">
                                                <div class="col-md-3">
                                                    <input class="ancho text-center" obl="1" maxlength="50" minlength="15" type="text" name="dir_emailx[]" minlength="8" maxlength="50" validate="email" id="dir_emailx<?= $key ?>" value="<?= $value ?>"/>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="col-md-3">    
                                                        <input type="text" obl="1" maxlength="10" minlength="10" validate="date"  class="date text-center" id="fec_inicia<?= $key ?>" readonly name="fec_inicia[]"/>
                                                    </div>
                                                    <div class="col-md-3"> 
                                                        <input type="text" obl="1" maxlength="5" minlength="5" validate="dir"  class="time text-center" onfocus="removeStyle('hor_inicia<?= $key ?>')" id="hor_inicia<?= $key ?>" readonly name="hor_inicia[]"/>
                                                    </div>
                                                    <div class="col-md-3"> 
                                                        <input type="text" obl="1" maxlength="10" minlength="10" validate="date"   class="date text-center" id="fec_finali<?= $key ?>" readonly name="fec_finali[]"/>
                                                    </div>
                                                    <div class="col-md-3"> 
                                                        <input type="text" obl="1" maxlength="5" minlength="5" validate="dir" class="time text-center" onfocus="removeStyle('hor_finali<?= $key ?>')" id="hor_finali<?= $key ?>" readonly name="hor_finali[]"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <img class="pointer" src="../<?= DIR_APLICA_CENTRAL ?>/images/delete.png" width="14px" height="14px" onclick="removeEmail(<?= $key ?>)">
                                                </div>
                                           </div>
                                       <?php
                                        }
                                    }
                                     ?>
                                </div>
                                <input type="hidden" value="<?= $key ?>" id="tot_emailxx"/>
                                <input type="hidden" name="standa" id="standa" value="<?= DIR_APLICA_CENTRAL ?>"/>                               
                                
                                <div class="col-md-12 CellHead">
                                    <b>Bit&aacute;cora de Suspenciones y Activaciones</b>
                                </div>
                                <div class="col-md-2"></div>
                                <div class="col-md-12 CellHead">
                                    <div class="col-md-2"><b>Fecha y Hora de Suspenci&oacute;n</b></div>
                                    <div class="col-md-2"><b>Usuario</b></div>
                                    <div class="col-md-2"><b>Observaci&oacute;n</b></div>
                                    <div class="col-md-2"><b>Fecha y Hora de Activaci&oacute;n</b></div>
                                    <div class="col-md-2"><b>Usuario</b></div>
                                    <div class="col-md-2"><b>Observaci&oacute;n</b></div>
                                </div> 
                                <div class="col-md-6 contenido">
                                <?php 
                                $suspenciones = self::$cFunciones->getRegistroSuspenciones($datos->cod_tercer);  
                                if(!$suspenciones){
                                     ?>
                                    <b>No hay registros de suspenciones para esta empresa.</b>
                                    <?php
                                }else{
                                    foreach ($suspenciones as $key => $value) {
                                        ?>
                                        <div class="col-md-4"><?= $value['fec_operac'] ?></div>
                                        <div class="col-md-4"><?= $value['usr_creaci'] ?></div>
                                        <div class="col-md-4"><?= $value['obs_bitaco'] ?></div>
                                        <?php
                                    }
                                }
                                ?>                                    
                                </div>                              
                                <div class="col-md-6 contenido">
                                <?php 
                                $activciones = self::$cFunciones->getRegistroActivaciones($datos->cod_tercer); 
                                if(!$activciones){
                                    ?>
                                    <b>No hay registros de activaciones para esta empresa.</b>
                                    <?php
                                }else{
                                    foreach ($activciones as $key => $value) {
                                       ?>
                                        <div class="col-md-4"><?= $value['fec_operac'] ?></div>
                                        <div class="col-md-4"><?= $value['usr_creaci'] ?></div>
                                        <div class="col-md-4"><?= $value['obs_bitaco'] ?></div>
                                       <?php
                                    }
                                }
                                ?>   
                                </div>
                                <div class="col-md-12 text-center">
                                   <input type="button" name="aceptar" class="crmButton small save ui-button ui-widget ui-state-default ui-corner-all pointer" id="aceptar" value="Registrar" onclick="registrarSuspencion()"/>
                                </div>                              
                            </td>
                        </tr>
                    </tbody>
                </table>
                
            </div>           
        </div>
    </div>
       <?php
    }

}
//FIN CLASE
$proceso = new ins_servic_suspen($this->conexion, $this->usuario_aplicacion, $this->codigo);
?>
