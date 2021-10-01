<?php 
ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);

/*! \Class: calendAgendamiento
*  \brief: Clase encargada de hacer la conexion para hacer la solicitud de SOAT
*  \author: Ing. Luis Manrique
*  \date: 22/10/2019
*  \param: $mConnection  -  Variable de clase que almacena la conexion de la Base de datosm biene desde el framework
*  \return array
*/

class calendAgendamiento
{
  var $conexion = NULL;   
  var $cod_aplica;

  /*! \fn: calendAgendamiento
  *  \brief: constructor de php4 para la clase
  *  \author: Ing. Luis Manrique
  *  \date: 16/07/2015   
  *  \param: fConection  : Conexion de base de datos 
  *  \param: mParams     : Array con los datos a enviar 
  *  \return n/a
  */
  
  function calendAgendamiento( $mConnection, $mData, $ca )
  {   
    
        $this -> conexion = $mConnection;
        $this-> cod_aplica=$ca;
      	@include_once( '../'.DIR_APLICA_CENTRAL.'/lib/general/functions.inc' );
        switch( $_REQUEST["Option"] )
	    { 

        case "1":
          self::getUsuari();
        break;
	      default:
	        $this -> setCalendar();
	      break;
	    }
  }



  /*! \fn: stylesCSS
  *  \brief: Metodo donde ingresa los archivos CSS  necesarios
  *  \author: Ing. Luis Manrique
  *  \date: 23/10/2019
  *  \return n/a
  */ 

  function stylesCSS(){
      $style = '<meta http-equiv="content-type" content="text/html; ISO-8859-1">';
      $style .= '<link rel="stylesheet" href="../'.DIR_APLICA_CENTRAL.'/js/lib/plugins/fontawesome-free/css/all.min.css" type="text/css">';
      $style .= '<link rel="stylesheet" href="../'.DIR_APLICA_CENTRAL.'/js/lib/plugins/jquery-ui/jquery-ui.min.css" type="text/css">';
      $style .= '<link rel="stylesheet" href="../'.DIR_APLICA_CENTRAL.'/js/lib/plugins/fullcalendar/main.min.css" type="text/css">';
      $style .= '<link rel="stylesheet" href="../'.DIR_APLICA_CENTRAL.'/js/lib/plugins/fullcalendar-daygrid/main.min.css" type="text/css">';
      $style .= '<link rel="stylesheet" href="../'.DIR_APLICA_CENTRAL.'/js/lib/plugins/fullcalendar-timegrid/main.min.css">';
      $style .= '<link rel="stylesheet" href="../'.DIR_APLICA_CENTRAL.'/js/lib/plugins/fullcalendar-bootstrap/main.min.css" type="text/css">';
      $style .= '<link rel="stylesheet" href="../'.DIR_APLICA_CENTRAL.'/js/lib/dist/css/adminlte.min.css" type="text/css">';
      $style .= '<!-- datetimepicker -->';
      $style .= '<link rel="stylesheet" href="../'.DIR_APLICA_CENTRAL.'/js/lib/plugins/datetimepicker/css/bootstrap-datetimepicker.min.css" type="text/css">';
      $style .= '<!-- daterange picker -->';
      $style .= '<link rel="stylesheet" href="../'.DIR_APLICA_CENTRAL.'/js/lib/plugins/daterangepicker/daterangepicker.css" type="text/css">';
      $style .= '<!-- SweetAlert2 -->';
      $style .= '<link rel="stylesheet" href="../'.DIR_APLICA_CENTRAL.'/js/lib/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css" type="text/css">';
      $style .= '<!-- Estilos Pagina -->';
      $style .= '<link rel="stylesheet" href="../'.DIR_APLICA_CENTRAL.'/css/tab_agenda_retirax.css" type="text/css">';
      //Multiselect
      $style .= '<link href="../' . DIR_APLICA_CENTRAL . '/js/lib/multiselect/styles/multiselect.css" rel="stylesheet">';

      echo $style;  
  }

  /*! \fn: stylesJS
  *  \brief: Metodo donde ingresa los archivos JS necesarios
  *  \author: Ing. Luis Manrique
  *  \date: 23/10/2019
  *  \return n/a
  */ 

  function scriptJS(){
      $mPerms = $this->getView('jso_contac');
      $script = '<script type="text/javascript" language="JavaScript" src="../'.DIR_APLICA_CENTRAL.'/js/lib/plugins/jquery/jquery.js"></script>';
      $script .= '<script type="text/javascript" language="JavaScript" src="../'.DIR_APLICA_CENTRAL.'/js/lib/plugins/bootstrap/js/bootstrap.bundle.js"></script>';
      $script .= '<script type="text/javascript" language="JavaScript" src="../'.DIR_APLICA_CENTRAL.'/js/lib/plugins/jquery-ui/jquery-ui.js"></script>';
      $script .= '<script type="text/javascript" language="JavaScript" src="../'.DIR_APLICA_CENTRAL.'/js/lib/plugins/moment/moment.min.js"></script>';
      $script .= '<script type="text/javascript" language="JavaScript" src="../'.DIR_APLICA_CENTRAL.'/js/lib/plugins/fullcalendar/main.js"></script>';
      $script .= '<script type="text/javascript" language="JavaScript" src="../'.DIR_APLICA_CENTRAL.'/js/lib/plugins/fullcalendar/locales-all.js"></script>';
      $script .= '<script type="text/javascript" language="JavaScript" src="../'.DIR_APLICA_CENTRAL.'/js/lib/plugins/fullcalendar-daygrid/main.js"></script>';
      $script .= '<script type="text/javascript" language="JavaScript" src="../'.DIR_APLICA_CENTRAL.'/js/lib/plugins/fullcalendar-timegrid/main.js"></script>';
      $script .= '<script type="text/javascript" language="JavaScript" src="../'.DIR_APLICA_CENTRAL.'/js/lib/plugins/fullcalendar-interaction/main.js"></script>';
      $script .= '<script type="text/javascript" language="JavaScript" src="../'.DIR_APLICA_CENTRAL.'/js/lib/plugins/fullcalendar-bootstrap/main.js"></script>';
      $script .= '<!-- datetimepicker -->';
      $script .= '<script type="text/javascript" language="JavaScript" src="../'.DIR_APLICA_CENTRAL.'/js/lib/plugins/datetimepicker/js/bootstrap-datetimepicker.min.js"></script>';
      $script .= '<!-- date-range-picker -->';
      $script .= '<script type="text/javascript" language="JavaScript" src="../'.DIR_APLICA_CENTRAL.'/js/lib/plugins/daterangepicker/daterangepicker.js"></script>';
      $script .= '<!-- SweetAlert2 -->';
      $script .= '<script type="text/javascript" language="JavaScript" src="../'.DIR_APLICA_CENTRAL.'/js/lib/plugins/sweetalert2/sweetalert2.min.js"></script>';
      $script .= '<!-- AdminLTE App -->';
      $script .= '<script type="text/javascript" language="JavaScript" src="../'.DIR_APLICA_CENTRAL.'/js/lib/dist/js/adminlte.min.js"></script>';
      $script .= '<!-- AdminLTE for demo purposes -->';
      $script .= '<script type="text/javascript" language="JavaScript" src="../'.DIR_APLICA_CENTRAL.'/js/lib/dist/js/demo.js"></script>';
      $script .= '<!-- Moment js format time -->';
      $script .= '<script type="text/javascript" language="JavaScript" src="../'.DIR_APLICA_CENTRAL.'/js/lib/plugins/moment/moment.min.js"></script>';
      
        $script .= '<script type="text/javascript" language="JavaScript" src="../'.DIR_APLICA_CENTRAL.'/js/cal_agenda_pedido.js?rand='.rand(1000, 10000).'"></script>';  
      
        //$script .= '<script type="text/javascript" language="JavaScript" src="../'.DIR_APLICA_CENTRAL.'/js/cal_agenda_pedret.js?rand='.rand(1000, 10000).'"></script>';
        
        $script .= '<script src="../' . DIR_APLICA_CENTRAL . '/js/jquery-ui-1.12.1/jquery.blockUI.js"></script>';
        $script .= '<script src="../' . DIR_APLICA_CENTRAL . '/js/multiselect/jquery.multiselect.js"></script>';
        //Multiselect
        $script .= '<script src="../' . DIR_APLICA_CENTRAL . '/js/lib/multiselect/multiselect.min.js"></script>';

      $script .= '<script type="text/javascript" language="JavaScript" src="../'.DIR_APLICA_CENTRAL.'/js/cal_agenda_valida.js"></script>';
      echo $script;
  }

  /*! \fn: setCalendar
  *  \brief: Metodo que muestra la intefaz principal del calendario
  *  \author: Ing. Luis Manrique
  *  \date: 20/12/2019    
  *  \return: html
  */  

  function setCalendar()
  {
    //Cargando Estilos
    $this -> stylesCSS();
    $mPerms = $this->getView('jso_progra');
      $tam = 9;
      //Valida la visibilidad de panel izquierdo
      
    //if($mPerms->dat_progra->ind_visibl != 1){
      //$tam = 9;
    //}else{
    $code='<div class="col-md-3">
    <div class="sticky-top mb-3">';
    //}
    $html.='<td>
              <!-- Main content -->
              <section class="content">
                <div class="container-fluid">
                  <div class="row">';
    $html.=$code;
    $disable_envio="";
    /*if($this->activeEnvio($_REQUEST['cod_pedido'],$_REQUEST['lin'])){
      $disable_envio="disabled";
            }*/
            $html.='<div class="card">
                                <div class="card-header">
                                <h3 class="card-title" id="titulo">Filtro por persona</h3>
                              </div>

                              <div class="card-body">
                                <div class="form-group">
                                  <label for="usuarioLID">Seleccione Usuario</label>
                                  <div class="input-group">
                                  <select class="form-control field" id="usuarioID" name="cod_usuari">
                                  <option value="">Usuario</option>
                                  ';
                                  $usuari = $this->getUsuari();
                                  foreach ($usuari as $key => $value) {
                                    $html .= '<option value="'.$value['cod_usuari'].'">'.$value['cod_usuari'].'</option>';
                                  }
                                

                                  $html .=  '
                      
                                </select>
                                  </div>
                                </div>
                                <div class="form-group">
                                  <button id="buscar" type="button" class="btn btn-success" '.$disable_envio.' onclick="buscarUsuario()">Buscar</button>
                                </div>
                              </div>
                            </div> ';
            
              if($mPerms->dat_progra->ind_visibl == 1){
                  
                  $html .= '  
                      <div class="card">
                          <div class="card-header">
                            <h3 class="card-title" id="titulo">Programar Turnos</h3>
                          </div>
                          <div class="card-body">
                            <div class="btn-group" style="width: 100%; margin-bottom: 10px;">
                              <!--<button type="button" id="color-chooser-btn" class="btn btn-info btn-block dropdown-toggle" data-toggle="dropdown">Color <span class="caret"></span></button>-->
                              <ul class="fc-color-picker" id="color-chooser">
                                
                              </ul>
                            </div>
                            <!-- /btn-group -->
                            <div class="input-group">
                              <form role="form" id="form_addAgenPed">
                                <div class="form-group">
                                    <label for="perfil">Seleccione Perfil</label>
                                    <div class="input-group">
                                      <select class="form-control field" id="perfilID" name="cod_perfil" onchange="usuariosPerfil(this)">
                                        <option value="">Seleccione Perfil</option>
                                        ';
                                        $perfil = $this->getPerfil();
                                        foreach ($perfil as $key => $value) {
                                          $html .= '<option value="'.$value['cod_perfil'].'">'.$value['nom_perfil'].'</option>';
                                        }
                    
                                        $html .=  '
                                      </select>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    
                                    <div class="input-group">
                                      <div id="usuariDivID" style="width : 100%;">
                                      
                                        ';
                                        /*$usuari = $this->getUsuari();
                                        foreach ($usuari as $key => $value) {
                                          $html .= '<option value="'.$value['cod_usuari'].'">'.$value['nom_usuari'].'</option>';
                                        }*/
                                        $html .=  '
                                      </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                  <label for="horario">Seleccione Horario</label>
                                    <div class="input-group">
                                      <select class="form-control field" id="horariID" name="cod_horari">
                                      <option value="">Seleccione Horario</option>
                                      ';
                            $horari = $this->getHorari();

                            foreach ($horari as $key => $value) {
                              $html .= '<option value="'.$value['cod_horari'].'">'.$value['nom_horari'].'</option>';
                            }
                            

                            $html .=  '
                            
                            </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                  <label for="novedad">Novedad</label>
                                    <div class="input-group">
                                      <select class="form-control field" place id="novedaID" name="cod_noveda" onChange="viewObserva(this)">
                                      <option value="1">Seleccione Novedad</option>
                                      ';
                                        $novtur = $this->getNovtur();

                                        foreach ($novtur as $key => $value) {
                                          $html .= '<option value="'.$value['cod_novtur'].'">'.$value['nom_novtur'].'</option>';
                                        }
                                        

                                        $html .=  '
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                  <div class="input-group">
                                    <input type="text" id="hor_apert_hor_cierre-cl" name="hor_apert_hor_cierre-cl" class="form-control float-right field rango" placeholder="Fecha y hora programada">
                                  </div>
                                </div>
                              <div class="form-group" id="observaID" style="display: none">
                                <label for="observaID">Observacion</label>
                                <textarea id="observatext" name="observatext" type="text" class="form-control field"></textarea>
                              </div>


                                <div class="form-group">
                                  <button id="add-new-event" type="button" class="btn btn-success" '.$disable_envio.' onclick="consultaHoraDisponible()">Enviar</button>
                                </div>
                              <!-- /btn-group -->
                              </form>
                            </div>
                            <!-- /input-group -->
                          </div>
                        </div>';
              }
              
              $html .= 
                      '</div>
                    </div>
                    <!-- /.col -->
                    <div class="col-md-'.$tam.'">
                      <div class="card card-primary">
                        <div id="calendarPadre" class="card-body p-0">
                          <!-- THE CALENDAR -->
                          <div id="calendar"></div>
                        </div>
                        <!-- /.card-body -->
                      </div>
                      <!-- /.card -->
                    </div>
                    <!-- /.col -->
                  </div>
                  <!-- /.row -->
                </div><!-- /.container-fluid -->
              </section>
            </td>';
              
    echo $html;

    //Cargando scripts
    $this -> scriptJS();

  }


  /*! \fn: getView
     *  \brief: Trae los indicadores de secciones visibles por encargado (Perfil)
     *  \author: Ing. Fabian Salinas
     *  \date:  21/02/2020
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mCatego   String   campo categoria a retornar
     *  \return: Object
     */
    function getView( $mCatego )
    {
        $mSql = "SELECT a.jso_bandej, a.jso_encabe, a.jso_plarut, a.jso_contac, a.jso_progra
                   FROM ".BASE_DATOS.".tab_genera_respon a 
             INNER JOIN ".BASE_DATOS.".tab_genera_perfil b 
                     ON a.cod_respon = b.cod_respon 
                  WHERE b.cod_perfil = '".$_SESSION['datos_usuario']['cod_perfil']."' ";
        $mConsult = new Consulta($mSql, $this -> conexion);
        $mData = $mConsult->ret_matrix('a');

        return json_decode($mData[0][$mCatego]);
    }

    /*! \fn: gerPCargue
     *  \brief: Trae los puntos de cargue
     *  \author: Ing. Luis Manrique
     *  \date:  21/02/2020
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mCatego   String   campo categoria a retornar
     *  \return: Object
     */
    function gerPCargue()
    {
        $mSql = "SELECT a.cod_bahia, a.nom_bahia
                   FROM ".BASE_DATOS.".tab_genera_bahias a 
                  WHERE a.dis_cliret = 1";
        $mConsult = new Consulta($mSql, $this -> conexion);
        $mData = $mConsult->ret_matrix('a');

        return $mData;
    }

    private function getUsuari( $cod_docume = NULL )
    {
      
      $query = "SELECT a.cod_consec, a.cod_usuari, a.nom_usuari
                  FROM ".BASE_DATOS.".tab_genera_usuari a
                  
                WHERE a.ind_estado = '1'
                AND a.cod_perfil IN (".COD_PERFIL_CONTROLA.",".COD_PERFIL_SUPEFARO.",".COD_PERFIL_ADMINIST.")
                ORDER BY a.nom_usuari ASC";
      
      
      $consulta = new Consulta( $query, $this->conexion );
      $mUsiari = $consulta->ret_matriz('a');
      return $mUsiari;
    
    }

    private function getPerfil()
    {
      
      $query = "SELECT a.cod_perfil, a.nom_perfil
                  FROM ".BASE_DATOS.".tab_genera_perfil a
                  
                WHERE a.ind_estado = '1'
               ORDER BY a.nom_perfil ASC";
      
      
      $consulta = new Consulta( $query, $this->conexion );
      $mUsiari = $consulta->ret_matriz('a');
      return $mUsiari;
    
    }

    private function getHorari( $cod_horari = NULL )
    {
      
      $query = "SELECT a.cod_horari, a.nom_horari, hor_inicia, hor_finalx
                  FROM ".BASE_DATOS.".tab_config_horari a
                WHERE a.ind_estado = '1'";
      
      $consulta = new Consulta( $query, $this->conexion );
      $mHorari = $consulta->ret_matriz('a');
      return $mHorari;
    
    }

    private function getNovtur( $cod_novtur = NULL )
    {
      
      $query = "SELECT a.cod_novtur, a.nom_novtur
                  FROM ".BASE_DATOS.".tab_genera_novtur a
                WHERE a.ind_estado = '1'";
      
      $consulta = new Consulta( $query, $this->conexion );
      $mNovtur = $consulta->ret_matriz('a');
      return $mNovtur;
    
    }

    /*function activeEnvio($num_pedido,$num_lineax){
      $mSql = "SELECT a.cod_pedido 
                FROM ".BASE_DATOS.".tab_agenda_pedido a 
                WHERE a.cod_pedido = '".$num_pedido."' AND a.num_lineax = '".$num_lineax."';";
      $mConsult = new Consulta($mSql, $this -> conexion);
      $cantidad = $mConsult->ret_num_rows();
      if($cantidad>0){
        return true;
      }
      return false;
    }*/
}
  $solicitud = new calendAgendamiento( $this -> conexion, $_REQUEST, $this-> codigo );
?>

