<?php

class tab_genera_contro
{
 var $conexion,
     $cod_aplica,
     $usuario;

 function __construct($co, $us, $ca)
 {
  $this -> conexion = $co;
  $this -> usuario = $us;
  $this -> cod_aplica = $ca;
  $this -> principal();
 }

 function principal()
 {
  include_once("../" . DIR_APLICA_CENTRAL . "/lib/general/dinamic_list.inc");
    
   switch($_REQUEST[option])
   {
        case 1:
          $this->Formulario();
        break;
        case 2:
          $this->Editar();
        break;
        default:
          $this -> registros();
          break;
   }
 }

 /*! \fn: styles
       *  \brief: incluye todos los archivos necesarios para los estilos
       *  \author: Ing. Luis Manrique
       *  \date: 27-04-2020
       *  \date modified: dd/mm/aaaa
       *  \param: 
       *  \return: html
    */

    private function styles(){

        
        echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";
        echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";
        echo "<style> 
            #divBodyID {
                width: 95vw;
                margin-left: 18px;
                overflow-x: auto;
                overflow-y: auto;
            }
        </style>";
        echo "<link type='text/css' href='../". DIR_APLICA_CENTRAL ."/estilos/dinamic_list.css' rel='stylesheet'>";
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

        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";
        echo "<script src='../".DIR_APLICA_CENTRAL."/js/new_ajax.js' language='javascript'></script>";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
        
        
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";
        echo "<script src='../".DIR_APLICA_CENTRAL."/js/new_ajax.js' language='javascript'></script>";
        echo "<script src='../".DIR_APLICA_CENTRAL ."/js/dinamic_list.js?v=0111' language='javascript'></script>";
        echo '<script src="../' . DIR_APLICA_CENTRAL . '/js/tab_genera_contro.js?rand='.rand(150, 20000).'"></script>';

    }

  //---------------------------------------------
  /*! \fn: getInterfParame
  *  \brief:Verificar la interfaz con destino seguro esta activa 
  *  \author: Nelson Liberato
  *  \date: 21/12/2015
  *  \date modified: 21/12/2015
  *  \return BOOL
  */
  function registros() {
    self::styles();
    self::scripts();

    $formulario = new Formulario("index.php", "get", "Listado de Puestos De Control", "form_search\" id=\"form_searchID");
    
    $formulario -> nueva_tabla();
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("standa\" id=\"standaID",DIR_APLICA_CENTRAL,0);
    $formulario -> oculto("cod_servic\" id=\"cod_servicID",$_REQUEST["cod_servic"],0);
    $formulario -> oculto("option\" id=\"optionID",'1',0);

    echo "<hr style=\"border: 1px solid black;\">";
   
    $formulario->OpenDiv("name:divBody");
          echo $this->getData();   
    $formulario->CloseDiv();

    $formulario-> cerrar();

    
  }

  function getData(){

    $mSql = " SELECT  a.cod_contro,
              a.nom_contro,
              IF(a.cod_tpcont IS NULL, 'NO ASIGNADO', c.nom_tpcont) as 'nom_tpcont',
              b.nom_ciudad,
              a.dir_contro,
              a.tel_contro,
              a.val_longit,
              a.val_latitu,
              a.url_wazexx,
              CASE
                    WHEN a.ind_estado='1' THEN 'Activo'
                    WHEN a.ind_estado='0' THEN 'Inactivo'
              END as nom_estado,
              a.ind_estado cod_option
          FROM  ".BASE_DATOS.".tab_genera_contro a
          INNER JOIN ".BASE_DATOS.".tab_genera_ciudad b ON a.cod_ciudad = b.cod_ciudad
          LEFT JOIN ".BASE_DATOS.".tab_tipos_pcontr c ON a.cod_tpcont = c.cod_tpcont";

      $list = new DinamicList($this->conexion, $mSql, "1", "no", 'ASC');
      $list->SetClose('no');
      $list->SetCreate("Nuevo puesto de control", "onclick:nuevoPuestoContro()");
      $list->SetExcel('excel','onclick:exportExcel()');
      $list->SetHeader("Codigo", "field:a.cod_contro;");
      $list->SetHeader("Nombre", "field:a.nom_contro;");
      $list->SetHeader("Tipo", "field:nom_tpcont;having:true;");
      $list->SetHeader("Ciudad", "field:b.nom_ciudad;");
      $list->SetHeader("Dirección", "field:a.dir_contro;");
      $list->SetHeader("Telefono", "field:a.tel_contro;");
      $list->SetHeader("Longitud", "field:a.val_longit;");
      $list->SetHeader("Latitud", "field:a.val_latitu;");
      $list->SetHeader("Url Waze", "field:a.url_wazexx;");
      $list->SetHeader("Estado", "field:nom_estado;having:true;");
      $list->SetOption(("Opciones"),"field:cod_option; width:1%; onclikDisable:editCont( 2, this ); onclikEnable:editCont( 1, this ); onclikEdit:editCont( 99, this );" );
      $list->SetHidden("cod_contro", "0");
      $list->Display($this->conexion);    


      $_SESSION["DINAMIC_LIST"] = $list;
      $this->tableExport($mSql);
      return  $Html = $list->GetHtml();
  }

  function Editar(){

    echo '
            
            <!-- Datatables -->
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-buttons/css/buttons.dataTables.min.css" rel="stylesheet">
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">

            <!-- Float Menu -->
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/button.css" rel="stylesheet">
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/floatMenu.css" rel="stylesheet">

            <!-- Jquery UI -->
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/jquery-ui-1.12.1/jquery-ui-1.12.1/jquery-ui.min.css" rel="stylesheet">

            <!-- Bootstrap -->
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

            <!-- Datetime picker -->
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker-standalone.css" rel="stylesheet">
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" rel="stylesheet">

            <!-- Font Awesome -->
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/fontawesome/css/font-awesome.min.css" rel="stylesheet">
            
            
            <!-- Custom Theme Scripts -->
            <link href="../' . DIR_APLICA_CENTRAL . '/estilos/tab_genera_contro.css" rel="stylesheet">
            <link href="../' . DIR_APLICA_CENTRAL . '/estilos/spectrum.css" rel="stylesheet">
        ';

        echo "<style> 
            #divBodyID {
                width: 95vw;
                margin-left: 18px;
                overflow-x: auto;
                overflow-y: auto;
            }
        </style>";

      $cod = $_REQUEST['cod'];
      $mQuery = "SELECT * FROM ".BASE_DATOS.".tab_genera_contro a WHERE a.cod_contro='$cod'";
      $consulta = new Consulta($mQuery, $this -> conexion); 
      $dato = $consulta->ret_matriz("a")[0];

      $datosTiposPc = $this->tiposPc($dato['cod_tpcont']);
      $objciud = new Despachos($_REQUEST[cod_servic],$_REQUEST[opcion],$this -> aplica,$this -> conexion);
      $ciudad = $objciud -> getListadoCiudades();
      $inicio[0][0]=0;
      $inicio[0][1]='-';
      $ciudades = array_merge($inicio,$ciudad);
      $ciudagen = array_merge($inicio,$ciudad);
    

      $ciudadesHtml = '';
      foreach ($ciudades as $key => $value) {
        $seleted = $dato['cod_ciudad'] == $value['cod_ciudad'] ? 'selected':'';
        $ciudadesHtml.='<option value="'.$value['cod_ciudad'].'" '.$seleted.' >'.$value[1].'</option>';
      }

      $formulario = new Formulario("index.php", "get", "REGISTRAR NUEVO PUESTO DE CONTROL", "form_search\" id=\"form_searchID\"  class=\"FormularioVia\" ");
    
      $formulario -> oculto("window","central",0);
      $formulario -> oculto("standa\" id=\"standaID",DIR_APLICA_CENTRAL,0);
      $formulario -> oculto("cod_servic\" id=\"cod_servicID",$_REQUEST["cod_servic"],0);

    

      $html = '
            <tr>
              <td>
                <div style="width: 96vw; margin-left: 10px;">
                  <div class="row">
                          <div class="col-sm-12">
                              <p>Los campos marcados con (<span class="redObl">*</span>) son OBLIGATORIOS para el registro del servicio en el sistema.</p>
                          </div>
                  </div>
                  <div class="row">
                      <div class="col-sm-4">
                          <label class="col-12 control-label"><span class="redObl">*</span> Nombre</label>
                          <input class="form-control form-control-sm" type="text" placeholder="Nombre" id="nom_controID" name="nom_contro" value="'.$dato['nom_contro'].'" required>
                      </div>
                      
                      <div class="col-sm-8">
                          <label class="col-12 control-label">Direcci&oacute;n</label>
                          <input class="form-control form-control-sm" type="text" placeholder="Direcci&oacute;n" id="dir_controID" value="'.$dato['dir_contro'].'" name="dir_contro">
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-sm-4">
                          <label class="col-12 control-label"> Telefono</label>
                          <input class="form-control form-control-sm" type="text" placeholder="Telefono" id="tel_controID" name="tel_contro" value="'.$dato['tel_contro'].'">
                      </div>
                      <div class="col-sm-4">
                          <label class="col-12 control-label"><span class="redObl">*</span> Ciudad</label>
                          <select class="form-control form-control-sm" id="cod_ciudadID" name="cod_ciudad" required>
                              '.$ciudadesHtml.'
                          </select>
                      </div>
                      <div class="col-sm-4">
                          <label class="col-12 control-label"><span class="redObl">*</span>Tipo Puesto De control</label>
                          <select class="form-control form-control-sm" id="tipFormulID" name="tipFormul" required>
                            <option value="">Escoja una Opci&oacute;n</option>
                              '.$datosTiposPc.'
                          </select>
                      </div>
                  </div>
                  <hr>
                  <div class="row">
                      <div class="col-sm-3">
                          <label class="col-12 control-label"><span class="redObl">*</span> Latitud</label>
                          <input class="form-control form-control-sm" type="text" placeholder="Latitud" id="val_latituID" name="val_latitu"  value="'.$dato['val_latitu'].'" required>
                      </div>
                      <div class="col-sm-3">
                          <label class="col-12 control-label"><span class="redObl">*</span> Longitud</label>
                          <input class="form-control form-control-sm" type="text" placeholder="Longitud" id="val_longitID" name="val_longit"  value="'.$dato['val_longit'].'" required>
                      </div>
                      <div class="col-sm-6">
                          <label class="col-12 control-label">Url Waze</label>
                          <input class="form-control form-control-sm" type="text" placeholder="Waze" id="url_wazexxID" name="url_wazexx" value="'.$dato['url_wazexx'].'" >
                      </div>
                  </div>
                  <div class="row ">
                      <div class="col-sm-3">
                          <label class="col-12 control-label">Color</label>
                          <input class="form-control form-control-sm" type="text" placeholder="color" id="cod_colorxID" name="cod_colorx" value="'.$dato['cod_colorx'].'" required>
                      </div>
                      <div class="col-sm-3">
                          <div class="card" style="width: 18rem;">
                            <img src="https://www.sinrumbofijo.com/wp-content/uploads/2016/05/default-placeholder.png" class="card-img-top" alt="..." style="height: 200px; object-fit: cover; width: 200px; object-position: center center;" id="imgUpload">
                          </div>
                      </div>
                      <div class="col-sm-3">
                        <input type="file" class="form-control-file" name="image" id="img" accept="image/*">
                      </div>
                      <div class="col-sm-12">
                        <center>
                        <button  id="guarServicEdit" type="submit" class="swal2-confirm swal2-styled" aria-label="" style="display: inline-block; background-color: rgb(51, 102, 0); border-left-color: rgb(51, 102, 0); border-right-color: rgb(51, 102, 0);">Guardar</button>
                        </center>
                        <input type="hidden" name="cod_contro" id="cod_controID" value="'.$cod.'">
                      </div>    
                  </div>
                  
                </div>
              </td>
            </tr>
              ';
      echo $html;
    

    echo '
      <tr>
        <td>
        <!-- Moment -->
        <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/moment/moment.js"></script>

        <!-- jQuery -->
        <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/jquery/dist/jquery.min.js"></script>
        <script src="../' . DIR_APLICA_CENTRAL . '/js/jquery-ui-1.12.1/jquery.blockUI.js"></script>
        <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/jquery-ui-1.12.1/jquery-ui-1.12.1/jquery-ui.min.js"></script>

        <!-- Bootstrap -->
        <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap/dist/js/bootstrap.min.js"></script>

        <!-- Datatables -->
        <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
        <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
        <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/jszip/dist/jszip.min.js"></script>
        <script src="../' . DIR_APLICA_CENTRAL . '/js/DataTables/js/pdfmake.min.js" language="javascript"></script>
        <script src="../' . DIR_APLICA_CENTRAL . '/js/DataTables/js/vfs_fonts.js" language="javascript"></script>
        <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
        <script src="../' . DIR_APLICA_CENTRAL . '/js/spectrum.js"></script>

         <!-- SweetAlert -->
        <script src="../' . DIR_APLICA_CENTRAL . '/js/sweetalert2.all.8.11.8.js"></script>

        <!-- Datetime picker -->
        <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>

        <!-- Form validate -->
        <script src="../' . DIR_APLICA_CENTRAL . '/js/validation-scripts/jquery.validate.min.js"></script>
        <script src="../' . DIR_APLICA_CENTRAL . '/js/validation-scripts/jquery.form.js"></script>
        <script src="../' . DIR_APLICA_CENTRAL . '/js/validation-scripts/additional-methods.min.js"></script>

        <script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>

        <!-- Custom Theme Scripts -->
        <script src="../' . DIR_APLICA_CENTRAL . '/js/tab_genera_contro2.js?rand='.rand(150, 20000).'"></script>
    
    </td>
    </tr>';

    $formulario-> cerrar();
            
  }

  function Formulario(){

    echo '
            
            <!-- Datatables -->
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-buttons/css/buttons.dataTables.min.css" rel="stylesheet">
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">

            <!-- Float Menu -->
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/button.css" rel="stylesheet">
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/floatMenu.css" rel="stylesheet">

            <!-- Jquery UI -->
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/jquery-ui-1.12.1/jquery-ui-1.12.1/jquery-ui.min.css" rel="stylesheet">

            <!-- Bootstrap -->
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

            <!-- Datetime picker -->
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker-standalone.css" rel="stylesheet">
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" rel="stylesheet">

            <!-- Font Awesome -->
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/fontawesome/css/font-awesome.min.css" rel="stylesheet">
            
            
            <!-- Custom Theme Scripts -->
            <link href="../' . DIR_APLICA_CENTRAL . '/estilos/tab_genera_contro.css" rel="stylesheet">
            <link href="../' . DIR_APLICA_CENTRAL . '/estilos/spectrum.css" rel="stylesheet">
        ';

        echo "<style> 
            #divBodyID {
                width: 95vw;
                margin-left: 18px;
                overflow-x: auto;
                overflow-y: auto;
            }
        </style>";

        
      $datosTiposPc = $this->tiposPc();
      $objciud = new Despachos($_REQUEST[cod_servic],$_REQUEST[opcion],$this -> aplica,$this -> conexion);
      $ciudad = $objciud -> getListadoCiudades();
      $inicio[0][0]=0;
      $inicio[0][1]='-';
      $ciudades = array_merge($inicio,$ciudad);
      $ciudagen = array_merge($inicio,$ciudad);
    
      if($_REQUEST['ciudad'])
      {
        $ciudad_a = $objciud -> getSeleccCiudad($_REQUEST['ciudad']);
        $ciudades = array_merge($ciudad_a,$ciudades);
      }
      $ciudadesHtml = '';
      foreach ($ciudades as $key => $value) {
        $ciudadesHtml.='<option value="'.$value['cod_ciudad'].'">'.$value[1].'</option>';
      }

      $formulario = new Formulario("index.php", "get", "REGISTRAR NUEVO PUESTO DE CONTROL", "form_search\" id=\"form_searchID\"  class=\"FormularioVia\" ");
    
      $formulario -> oculto("window","central",0);
      $formulario -> oculto("standa\" id=\"standaID",DIR_APLICA_CENTRAL,0);
      $formulario -> oculto("cod_servic\" id=\"cod_servicID",$_REQUEST["cod_servic"],0);
      $formulario -> oculto("option\" id=\"optionID",'1',0);

    

      $html = '
            <tr>
              <td>
                <div style="width: 96vw; margin-left: 10px;">
                  <div class="row">
                          <div class="col-sm-12">
                              <p>Los campos marcados con (<span class="redObl">*</span>) son OBLIGATORIOS para el registro del servicio en el sistema.</p>
                          </div>
                  </div>
                  <div class="row">
                      <div class="col-sm-4">
                          <label class="col-12 control-label"><span class="redObl">*</span> Nombre</label>
                          <input class="form-control form-control-sm" type="text" placeholder="Nombre" id="nom_controID" name="nom_contro" required>
                      </div>
                      
                      <div class="col-sm-8">
                          <label class="col-12 control-label">Direcci&oacute;n</label>
                          <input class="form-control form-control-sm" type="text" placeholder="Direcci&oacute;n" id="dir_controID" name="dir_contro">
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-sm-4">
                          <label class="col-12 control-label"> Telefono</label>
                          <input class="form-control form-control-sm" type="text" placeholder="Telefono" id="tel_controID" name="tel_contro">
                      </div>
                      <div class="col-sm-4">
                          <label class="col-12 control-label"><span class="redObl">*</span> Ciudad</label>
                          <select class="form-control form-control-sm" id="cod_ciudadID" name="cod_ciudad" required>
                              '.$ciudadesHtml.'
                          </select>
                      </div>
                      <div class="col-sm-4">
                          <label class="col-12 control-label"><span class="redObl">*</span>Tipo Puesto De control</label>
                          <select class="form-control form-control-sm" id="tipFormulID" name="tipFormul" required>
                            <option value="">Escoja una Opci&oacute;n</option>
                              '.$datosTiposPc.'
                          </select>
                      </div>
                  </div>

                  <hr>
                  <div class="row">
                      <div class="col-sm-3">
                          <label class="col-12 control-label"><span class="redObl">*</span> Latitud</label>
                          <input class="form-control form-control-sm" type="text" placeholder="Latitud" id="val_latituID" name="val_latitu" required>
                      </div>
                      <div class="col-sm-3">
                          <label class="col-12 control-label"><span class="redObl">*</span> Longitud</label>
                          <input class="form-control form-control-sm" type="text" placeholder="Longitud" id="val_longitID" name="val_longit" required>
                      </div>
                      <div class="col-sm-6">
                          <label class="col-12 control-label">Url Waze</label>
                          <input class="form-control form-control-sm" type="text" placeholder="Waze" id="url_wazexxID" name="url_wazexx" >
                      </div>
                  </div>
                  <div class="row ">
                      <div class="col-sm-3">
                          <label class="col-12 control-label">Color</label>
                          <input class="form-control form-control-sm" type="text" placeholder="color" id="cod_colorxID" name="cod_colorx" required>
                      </div>
                      <div class="col-sm-3">
                          <div class="card" style="width: 18rem;">
                            <img src="https://www.sinrumbofijo.com/wp-content/uploads/2016/05/default-placeholder.png" class="card-img-top" alt="..." style="height: 200px; object-fit: cover; width: 200px; object-position: center center;" id="imgUpload">
                          </div>
                      </div>
                      <div class="col-sm-3">
                        <input type="file" class="form-control-file" name="image" id="img" accept="image/*">
                      </div>
                      <div class="col-sm-12">
                        <center>
                        <button id="guarServic" type="submit" class="swal2-confirm swal2-styled" aria-label="" style="display: inline-block; background-color: rgb(51, 102, 0); border-left-color: rgb(51, 102, 0); border-right-color: rgb(51, 102, 0);">Guardar</button>
                        </center>
                        <input type="hidden" name="cod_contro" id="cod_controID" value="0">
                      </div>
                  </div>
                  
                </div>
              </td>
            </tr>
              ';
      echo $html;
    

    echo '
      <tr>
        <td>
        <!-- Moment -->
        <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/moment/moment.js"></script>

        <!-- jQuery -->
        <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/jquery/dist/jquery.min.js"></script>
        <script src="../' . DIR_APLICA_CENTRAL . '/js/jquery-ui-1.12.1/jquery.blockUI.js"></script>
        <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/jquery-ui-1.12.1/jquery-ui-1.12.1/jquery-ui.min.js"></script>

        <!-- Bootstrap -->
        <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap/dist/js/bootstrap.min.js"></script>

        <!-- Datatables -->
        <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
        <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
        <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/jszip/dist/jszip.min.js"></script>
        <script src="../' . DIR_APLICA_CENTRAL . '/js/DataTables/js/pdfmake.min.js" language="javascript"></script>
        <script src="../' . DIR_APLICA_CENTRAL . '/js/DataTables/js/vfs_fonts.js" language="javascript"></script>
        <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
        <script src="../' . DIR_APLICA_CENTRAL . '/js/spectrum.js"></script>

         <!-- SweetAlert -->
        <script src="../' . DIR_APLICA_CENTRAL . '/js/sweetalert2.all.8.11.8.js"></script>

        <!-- Datetime picker -->
        <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>

        <!-- Form validate -->
        <script src="../' . DIR_APLICA_CENTRAL . '/js/validation-scripts/jquery.validate.min.js"></script>
        <script src="../' . DIR_APLICA_CENTRAL . '/js/validation-scripts/jquery.form.js"></script>
        <script src="../' . DIR_APLICA_CENTRAL . '/js/validation-scripts/additional-methods.min.js"></script>

        <script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>

        <!-- Custom Theme Scripts -->
        <script src="../' . DIR_APLICA_CENTRAL . '/js/tab_genera_contro2.js?rand='.rand(150, 20000).'"></script>
    
    </td>
    </tr>';

    $formulario-> cerrar();
            
  }

  public function tableExport($mSql){

    $consulta  = new Consulta($mSql, $this -> conexion);
    $data = $consulta -> ret_matriz();
    $export = '<table id="tablaRegistros" class="table table-striped table-bordered table-sm" style="width: 90vw;font-size:10px;">
            <thead>
            <tr>
                <th>Codigo</th>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Ciudad</th>
                <th>Dirección</th>
                <th>Telefono</th>
                <th>Longitud</th>
                <th>Latitud</th>
                <th>Url Waze</th>
            </tr>
            </thead>
            <tbody>';
            foreach($data as $value){
               $export .="<tr>";
               $export .="<td>".$value['cod_contro']."</td>";
               $export .="<td>".$value['nom_contro']."</td>";
               $export .="<td>".$value['nom_tpcont']."</td>";
               $export .="<td>".$value['nom_ciudad']."</td>";
               $export .="<td>".$value['dir_contro']."</td>";
               $export .="<td>".$value['tel_contro']."</td>";
               $export .="<td>".$value['val_longit']."</td>";
               $export .="<td>".$value['val_latitu']."</td>";
               $export .="<td>".$value['url_wazexx']."</td>";
               $export .="</tr>";
            }
    $export .='</tbody>
        </table>';

    $_SESSION["HTML"] = $export;
    

  }


  function darOpcionAsistencia(){
    $sql="SELECT a.id,a.nom_asiste FROM ".BASE_DATOS.".tab_formul_asiste a WHERE a.ind_estado=1;";
    $consulta = new Consulta($sql, $this->conexion);
    $respuesta = $consulta->ret_matriz("a");
    $html='';
    foreach($respuesta as $dato){
      $html.='<option value="'.$dato['id'].'">'.$dato['nom_asiste'].'</option>';
    }
    return utf8_encode($html);
  }

  function tiposPc($cod_tpcont = NULL){
    $sql="SELECT a.cod_tpcont, a.nom_tpcont FROM ".BASE_DATOS.".tab_tipos_pcontr a WHERE a.ind_estado=1;";
    $consulta = new Consulta($sql, $this->conexion);
    $respuesta = $consulta->ret_matriz("a");

    $html='';
    foreach($respuesta as $dato){
      $seleted = $dato['cod_tpcont'] == $cod_tpcont ? 'selected':'';
      $html.='<option value="'.$dato['cod_tpcont'].'" '.$seleted.'>'.$dato['nom_tpcont'].'</option>';
    }
    return utf8_encode($html);
    
  }

}

$proceso = new tab_genera_contro($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>
