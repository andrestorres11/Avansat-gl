<?php
/*ini_set('error_reporting', E_ALL);
	ini_set("display_errors", 1);*/
class ins_asiste_asiscar
{
    var $conexion,
     $cod_aplica,
     $usuario;

 function __construct($co, $us, $ca)
 {
  $this -> conexion = $co;
  $this -> usuario = $us;
  $this -> cod_aplica = $ca;
  $this -> filtro();
 }


 /*! \fn: styles
       *  \brief: incluye todos los archivos necesarios para los estilos
       *  \author: Ing. Cristian Andrï¿½s Torres
       *  \date: 27-04-2020
       *  \date modified: dd/mm/aaaa
       *  \param: 
       *  \return: html
    */

    private function styles(){
        echo '
            <!-- Bootstrap 4.1.3 -->
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap-4/css/bootstrap.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css" rel="stylesheet">
            
            

            <!-- Font Awesome -->
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/fontawesome/css/font-awesome.min.css" rel="stylesheet">
            
            <!-- Jquery UI -->
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/jquery-ui-1.12.1/jquery-ui-1.12.1/jquery-ui.min.css" rel="stylesheet">
            
            <!-- Datatables all in one-->
            <link  rel="stylesheet" href="../' . DIR_APLICA_CENTRAL . '/js/lib/DataTablesb4/datatables.css" rel="stylesheet">
            <link  rel="stylesheet" href="../' . DIR_APLICA_CENTRAL . '/js/lib/DataTablesb4/Buttons-1.7.1/css/buttons.bootstrap4.min.css"  rel="stylesheet">

            <!-- Float Menu -->
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/button.css" rel="stylesheet">
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/floatMenu.css" rel="stylesheet">

            <!-- Custom Theme Style -->
            <link href="../' . DIR_APLICA_CENTRAL . '/estilos/estilos_table.css" rel="stylesheet">
            <link href="../' . DIR_APLICA_CENTRAL . '/estilos/inform_style.css" rel="stylesheet">
            <link href="../' . DIR_APLICA_CENTRAL . '/estilos/inf_transp_tipser.css" rel="stylesheet">
        ';
    }

    /*! \fn: scripts
   *  \brief: incluye todos los archivos necesarios para los eeventos js
   *  \author: Ing. Luis Manrique
   *  \date: 27-04-2020
   *  \date modified: dd/mm/aaaa
   *  \param: 
   *  \return: html
*/

    private function scripts(){

        echo '
            <!-- jQuery -->
            <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/jquery/dist/jquery.min.js"></script>
            

            <!-- Bootstrap 4.1.3-->
            
            
            <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap-4/js/bootstrap.bundle.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>

            <!-- bootstrap-progressbar -->
            <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>

            

            <!-- SweetAlert -->
            <script src="../' . DIR_APLICA_CENTRAL . '/js/sweetalert2.all.8.11.8.js"></script>

            <!-- Form validate -->
            <script src="../' . DIR_APLICA_CENTRAL . '/js/validation-scripts/jquery.validate.min.js"></script>
            <script src="../' . DIR_APLICA_CENTRAL . '/js/validation-scripts/jquery.form.js"></script>
            <script src="../' . DIR_APLICA_CENTRAL . '/js/validation-scripts/additional-methods.min.js"></script>

            <!-- Datatables ALL IN ONE-->
            <script type="text/javascript" src="../' . DIR_APLICA_CENTRAL . '/js/lib/DataTablesb4/datatables.js" ></script>
            <script type="text/javascript" src="../' . DIR_APLICA_CENTRAL . '/js/lib/DataTablesb4/Buttons-1.7.1/js/buttons.bootstrap.min.js" ></script>

            <!-- Custom Theme Scripts -->
            <script src="../' . DIR_APLICA_CENTRAL . '/js/inf_asiste_asiscar.js"></script>
        ';
    }

  //---------------------------------------------
  /*! \fn: getInterfParame
  *  \brief:Verificar la interfaz con destino seguro esta activa
  *  \author: Nelson Liberato
  *  \date: 21/12/2015
  *  \date modified: 21/12/2015
  *  \return BOOL
  */
  function filtro() {
    self::styles();
    $mDateNOW = date('Y-m-d');
    $mDateYes=date("Y-m-d",strtotime($mDateNOW."- 7 days"));

    $option_trans = $this->obtenerTransportadoras();
    $option_prove = $this->obtenerProveedores();
    $option_regio = $this->obtenerRegionales();
    $option_tipser = $this->obtenerTipoServicio();

    $html = '
            <td class="page">
              <form id="formSearch" method="post">
                <div class="card" style="margin:15px; width: 95vw;">
                    <div class="card-header color-heading">
                    Informe de asistencia en carretera
                    </div>
                    <div class="card-body">
                            <div class="row mt-3">
                                <div class="col-2 text-right">
                                  <label style="margin-top: 12px;">Transportadora:</label>
                                </div>
                                <div class="col-3">
                                  <select class="form-control form-control-sm " multiple  id="optionTransp" name="optionTransp">
                                          <option value="">Seleccione opción</option>
                                          '.$option_trans.'
                                  </select>
                                </div>
                                <div class="col-2 text-right">
                                  <label style="margin-top: 12px;">Proveedor:</label>
                                </div>
                                <div class="col-3">
                                  <select class="form-control form-control-sm " multiple id="optionProv" name="optionProv">
                                    <option value="">Seleccione opción</option>
                                    '.$option_prove.'
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-2 text-right">
                                  <label style="margin-top: 12px;">Regional:</label>
                                </div>
                                <div class="col-3">
                                  <select class="form-control form-control-sm " multiple id="optionRegio" name="optionRegio">
                                      <option value="">Seleccione opción</option>
                                      '.$option_regio.'
                                  </select>
                                </div>
                                <div class="col-2 text-right">
                                  <label style="margin-top: 12px;">Tipo Servicio:</label>
                                </div>
                                <div class="col-3">
                                  <select class="form-control form-control-sm " multiple id="optionTipSer" name="optionTipSer">
                                    <option value="">Seleccione opción</option>
                                    '.$option_tipser.'
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-2 text-right">
                                  <label style="margin-top: 12px;">Fecha Inicial:</label>
                                </div>
                                <div class="col-3">
                                  <input type="date" class="form-control form-control-sm" id="datInit" name="datInit" required value="'.$mDateYes.'">
                                </div>
                                <div class="col-2 text-right">
                                  <label style="margin-top: 12px;">Fecha Final:</label>
                                </div>
                                <div class="col-3">
                                  <input type="date" class="form-control form-control-sm" id="datEnd" name="datEnd" required value="'.$mDateNOW.'">
                                </div>
                            </div>
                    </div>
                </div> 
                <div class="card" style="margin:15px;width: 95vw;">
                  <div class="card-header color-heading">
                    <input class="small-box-footer btn btn-success btn-sm" type="submit" style="background: #aadd95;" value="Generar Informe"/>
                  </div>
                  <div class="card-body">
                    <div class="tab-content" id="pills-tabContent">
                    '.$this->Results().'
                    </div>
                  </div>
                </div> 
              </form>
            </td>';

    echo utf8_decode($html);
    self::scripts();

  }

  private function Results(){
      $html='
      <div class="tab-pane fade show active p-3" id="pills-results" role="tabpanel" aria-labelledby="pills-result-tab">
        <div  class="panel-collapse" style="overflow: auto;">
          <table class="table table-bordered" id="tabla_results">
              <thead>
                  <tr>
                      <th>No.</th> 
                      <th>No.Solicit</th>
                      <th>Tipo servicio</th>
                      <th>Transportadora</th>
                      <th>Regional</th>
                      <th>Nombre solicitante</th>
                      <th>Correo solicitante</th>
                      <th>No.Celular</th>
                      <th>Aseguradora</th>
                      <th>No.Poliza</th>
                      <th>Nombre Conductor</th>
                      <th>No.Celular</th>
                      <th>No.Placa</th>
                      <th>Origen</th>
                      <th>Destino</th>
                      <th>Valor Cliente</th>
                      <th>Valor Provee</th>
                      <th>Nombre Provee</th>
                      <th>Ceduls Provee</th>
                      <th>Sol. Anticipo</th>
                      <th>RTFuente</th>
                      <th>RIca</th>
                      <th>Saldo Rest</th>
                      <th>Utilidad</th>
                      <th>Rentabilidad</th>
                      <th>Fecha Solicit</th>
                      <th>Fecha Finaliz</th>
                  </tr>
              </thead>
              <tbody>
                  <tr id="results_info">
                  </tr>
              </tbody>
          </table>
        </div>
      </div>';
    return $html;
  }
    function obtenerTransportadoras()
    {
        $sql = "SELECT b.cod_tercer, b.abr_tercer FROM ".BASE_DATOS.".tab_tercer_emptra a
        INNER JOIN ".BASE_DATOS.".tab_tercer_tercer b 
        ON a.cod_tercer = b.cod_tercer
        WHERE b.cod_estado = 1 ORDER BY b.abr_tercer ASC";
        $consulta = new Consulta($sql, $this->conexion);
        $respuesta = $consulta->ret_matriz("a");
        $html='';
        foreach($respuesta as $dato){
        $html.='<option value="'.$dato['cod_tercer'].'">'.$dato['abr_tercer'].'</option>';
        }
        return utf8_encode($html);
    }
    function obtenerProveedores()
    {
        $sql = "SELECT b.cod_docume as docume, concat(COALESCE(b.nom_contra,''),' ',COALESCE(b.pri_apelli,''),' ',COALESCE(b.seg_apelli,'')) as name FROM ".BASE_DATOS.".tab_asiste_carret a 
        INNER JOIN ".BASE_DATOS.".tab_hojvid_ctxxxx b ON a.cod_provee = b.cod_docume 
        WHERE b.ind_estado = '1'
        UNION 
        SELECT d.cod_tercer as docume,concat(COALESCE(d.nom_tercer,''),' ',COALESCE(d.nom_apell1,''),' ',COALESCE(d.nom_apell2,'')) as name 
        FROM satt_faro.tab_tercer_activi as c INNER JOIN ".BASE_DATOS.".tab_tercer_tercer as d ON c.cod_tercer = d.cod_tercer 
        WHERE c.cod_activi='".COD_FILTRO_PROVEE."' and d.cod_estado='1' GROUP BY name ORDER BY name ASC";

        $consulta = new Consulta($sql, $this->conexion);
        $respuesta = $consulta->ret_matriz("a");
        $html='';
        foreach($respuesta as $dato){
        $html.='<option value="'.$dato['docume'].'">'.$dato['name'].'</option>';
        }
        return utf8_encode($html);
    }
    function obtenerRegionales()
    {
      $sql = "SELECT cod_region,nom_region FROM ".BASE_DATOS.".tab_genera_region  WHERE ind_estado='1'";
      $consulta = new Consulta($sql, $this->conexion);
      $respuesta = $consulta->ret_matriz("a");
      $html='';
        foreach($respuesta as $dato){
        $html.='<option value="'.$dato['cod_region'].'">'.$dato['nom_region'].'</option>';
        }
      return utf8_encode($html);
    }

    function obtenerTipoServicio()
    {
      $sql = "SELECT id,nom_asiste FROM ".BASE_DATOS.".tab_formul_asiste  WHERE ind_estado='1'";
      $consulta = new Consulta($sql, $this->conexion);
      $respuesta = $consulta->ret_matriz("a");
      $html='';
        foreach($respuesta as $dato){
        $html.='<option value="'.$dato['id'].'">'.$dato['nom_asiste'].'</option>';
        }
      return utf8_encode($html);
        
    }
    

}

$proceso = new ins_asiste_asiscar($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>