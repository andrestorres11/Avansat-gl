<?php
/*! \file: ajax_tercer_tercer.php
 *  \brief: archivo con multiples funciones ajax
 *  \author: Ing. Alexander Correa
 *  \author: alexander.correa@intrared.net
 *  \version: 1.0
 *  \date: 21/09/2015
 *  \bug: 
 *  \bug: 
 *  \warning1: Si se requieren agregar mas columnas en las funciones listarConductor, listarPropietarios y listarPoseedor por favor agregarlas despues de la segunda ya que las primeras son utilizadas para pintar la informacion
 *             Buscar #warning1 para ubicar las lineas afectadas
 */

setlocale(LC_ALL,"es_ES");

/*! \class: trans
 *  \brief: Clase trasn que gestiona las diferentes peticiones ajax  */
class trayle{

  private static  $cConexion,
                  $cCodAplica,
                  $cUsuario,
                  $cNull = array( array('','Seleccione un Elemento de la Lista') );


  function __construct($co = null, $us = null, $ca = null)
  {
    if($_REQUEST[Ajax] === 'on' ){
      @include_once( "../lib/ajax.inc" );
      @include_once( "../lib/general/constantes.inc" );
      self::$cConexion = $AjaxConnection;
    }else{
      self::$cConexion = $co;
      self::$cUsuario = $us;
      self::$cCodAplica = $ca;
    }

    if($_REQUEST[Ajax] === 'on' ){
        $opcion = $_REQUEST[Option];
        if(!$opcion){
          $opcion = $_REQUEST[operacion];
        }
      switch($opcion){
        case 'listaTrayles':
          $cod_transp = $_REQUEST['cod_transp'];
          self::listaTrayles($cod_transp);
         break;

        case 'listaVehiculos':
          $cod_transp = $_REQUEST['cod_transp'];
          self::listaVehiculos($cod_transp);
        break;

        case 'getCiudades':
            self::getCiudades();
            break;
            
        case "registrar":
            self::registrar();
            break;
        
        case "modificar":
            self::modificar();
            break;

        case 'eliminar':
            self::eliminar();
            break;

        case 'inactivar':
            self::inactivar();
            break;

        case 'activar':
            self::activar();
            break;

        case 'verificar':
            self::verificar();
            break;
        case 'verificarPlaca':
            self::verificarPlaca();
            break;
        case 'getTercerTransp':
            self::getTercerTransp();
            break;
        case 'registrarVehiculo':
            self::registrarVehiculo();
            break;
        case 'getLineas':
            self::getLineas();
            break;
        case 'modificarVehiculo':
            self::modificarVehiculo();
            break;
        case 'inactivarVehiculo':
            self::inactivarVehiculo();
            break;
        case 'activarVehiculo':
            self::activarVehiculo();
            break;
        case 'getValidaIdGPS':
            self::getValidaIdGPS();
            break;
        case 'saveOpeGps':
              self::saveOpeGps();
              break;
        case 'deteleOpeGps':
              self::deteleOpeGps();
              break;

        default:
          header('Location: index.php?window=central&cod_servic=1366&menant=1366');
          break;
      }
    }
  }

 /**********************************************************************************
 *   \fn: getValidaIdGPS                                                            *
 *  \brief: funcion para validar si el gps seleccionado tiene ID como obligatorio   *
 *  \author: Ing. Nelson Liberato                                                   *
 *  \date:  2019/05/21                                                              *
 *  \date modified:                                                                 *
 *  \param: $cod_opegps nit del operador GPS seleccionado en formulario             *     
 *  \param:                                                                         * 
 *  \return tabla con la lista de los terceros                                      *
 ***********************************************************************************/

private function getValidaIdGPS()
{

    $mSql = "SELECT a.nom_operad, IF(a.apl_idxxxx = '1', 'S', 'N') AS ind_usaidx
          FROM ".BASE_DATOS.".tab_genera_opegps a
         WHERE  a.cod_operad = '".mysql_real_escape_string($_REQUEST['cod_operad'])."' ";
    $consulta = new Consulta($mSql, self::$cConexion);
    $mGPS = $consulta->ret_matrix("a");
    echo json_encode([ 'cod_operad' => $_REQUEST['cod_operad'], 'ind_usaidx' => $mGPS[0]['ind_usaidx']] );
}

 /***********************************************************************************
 *   \fn: listaTrayles                                                             *
 *  \brief: funcion para listar los traylers de una transportadoras                 *
 *  \author: Ing. Alexander Correa                                                  *
 *  \date:  4/09/2015                                                               *
 *  \date modified:                                                                 *
 *  \param: $cod_transp codigo de la Trasnportador                                  *     
 *  \param:                                                                         * 
 *  \return tabla con la lista de los terceros                                      *
 ***********************************************************************************/

    private function listaTrayles($cod_transp){

        $mSql = "SELECT a.num_trayle,a.nom_propie,b.nom_martra,d.nom_colorx,a.tra_capaci,e.nom_carroc,
                        IF(a.ind_estado = '1','Activo', 'Inactivo') cod_estado,
                        a.ind_estado cod_option
              FROM ".BASE_DATOS.".tab_vehige_trayle a
              INNER JOIN ".BASE_DATOS.".tab_vehige_martra b ON b.cod_martra = a.cod_marcax
              INNER JOIN ".BASE_DATOS.".tab_transp_trayle c ON c.num_trayle = a.num_trayle
              INNER JOIN ".BASE_DATOS.".tab_vehige_colore d ON d.cod_colorx = a.cod_colore
              INNER JOIN ".BASE_DATOS.".tab_vehige_carroc e ON e.cod_carroc = a.cod_carroc
             WHERE  c.cod_transp = '$cod_transp'";
                                         
        $_SESSION["queryXLS"] = $mSql;

        if(!class_exists(DinamicList)) {
          include_once("../".DIR_APLICA_CENTRAL."/lib/general/dinamic_list.inc");                         
        }
        $list = new DinamicList(self::$cConexion, $mSql, "7" , "no", 'ASC');
        $list->SetClose('no');
        $list->SetCreate("Agregar Remolque", "onclick:formulario()");
        $list->SetHeader(("Nro. de Remolque"), "field:a.num_trayle; width:1%;  ");
        $list->SetHeader(("Poseedor"), "field:a.nom_propie; width:1%");
        $list->SetHeader(("Marca"), "field:a.nom_martra; width:1%");
        $list->SetHeader(("Color"), "field:a.nom_colorx" );
        $list->SetHeader(("Capacidad (TN)"), "field:a.tra_capaci" );
        $list->SetHeader(("Carroceria"), "field:a.nom_carroc" );
        $list->SetHeader(("Estado"), "field:a.cod_estado" );
        $list->SetOption(("Opciones"),"field:cod_option; width:1%; onclikDisable:editarRemolque( 2, this ); onclikEnable:editarRemolque( 1, this ); onclikEdit:editarRemolque( 99, this );" );
        $list->SetHidden("num_trayle", "0" );
        $list->SetHidden("nom_propie", "1" );


        $list->Display(self::$cConexion);

        $_SESSION["DINAMIC_LIST"] = $list;

        $Html = $list -> GetHtml();

        echo $Html;
    }

/************************************************************************************
 *   \fn: listaVehiculos                                                              *
 *  \brief: funcion para listar los vehiculos de una transportadoras                 *
 *  \author: Ing. Alexander Correa                                                  *
 *  \date:  4/09/2015                                                               *
 *  \date modified:                                                                 *
 *  \param: $cod_transp codigo de la Trasnportador                                  *     
 *  \param:                                                                         * 
 *  \return tabla con la lista de los terceros                                      *
 ***********************************************************************************/

    private function listaVehiculos($cod_transp){
        $mSql = "SELECT DISTINCT(a.num_placax),b.abr_tercer,b.num_telef1,b.num_telmov,c.nom_marcax,
                        d.nom_lineax,e.nom_colorx,f.nom_carroc,a.ano_modelo,/*g.num_trayle,*/
                        IF(i.ind_estado = '1','Activo', 'Inactivo') cod_estado,
                        max(g.num_noveda) novedad, i.ind_estado cod_option
              FROM ".BASE_DATOS.".tab_vehicu_vehicu a
              INNER JOIN ".BASE_DATOS.".tab_tercer_tercer b ON b.cod_tercer = a.cod_conduc
              INNER JOIN ".BASE_DATOS.".tab_genera_marcas c ON a.cod_marcax = c.cod_marcax
              INNER JOIN ".BASE_DATOS.".tab_vehige_lineas d ON a.cod_marcax = d.cod_marcax AND a.cod_lineax = d.cod_lineax 
              INNER JOIN ".BASE_DATOS.".tab_vehige_colore e ON a.cod_colorx = e.cod_colorx
              INNER JOIN ".BASE_DATOS.".tab_vehige_carroc f ON a.cod_carroc = f.cod_carroc
              LEFT  JOIN ".BASE_DATOS.".tab_trayle_placas g ON a.num_placax = g.num_placax
              INNER JOIN ".BASE_DATOS.".tab_transp_vehicu i ON i.num_placax = a.num_placax
             WHERE  i.cod_transp = '$cod_transp' GROUP BY a.num_placax";
                                         
        $_SESSION["queryXLS"] = $mSql;

        if(!class_exists(DinamicList)) {
          include_once("../".DIR_APLICA_CENTRAL."/lib/general/dinamic_list.inc");                         
        }
        $list = new DinamicList(self::$cConexion, $mSql, "11" , "no", 'ASC');
        $list->SetClose('no');
        $list->SetCreate("Agregar Vehiculo", "onclick:formulario()");
        $list->SetExcel("Excel", "onclick:exportExcel('opcion=3')");
        $list->SetHeader(utf8_decode("Placa"), "field:a.num_placax; width:1%;  ");
        $list->SetHeader(utf8_decode("Poseedor"), "field:b.abr_tercer; width:1%");
        $list->SetHeader(utf8_decode("Teléfono"), "field:b.num_telef1; width:1%");
        $list->SetHeader(utf8_decode("Celular"), "field:b.num_telmov" );
        $list->SetHeader(utf8_decode("Marca"), "field:c.nom_marcax" );
        $list->SetHeader(utf8_decode("Linea"), "field:d.nom_lineax" );
        $list->SetHeader(utf8_decode("Color"), "field:e.nom_colorx" );
        $list->SetHeader(utf8_decode("Carroceria"), "field:f.nom_carroc" );
        $list->SetHeader(utf8_decode("Modelo"), "field:a.ano_modelo" );
        //$list->SetHeader(utf8_decode("Remolque"), "field:g.num_trayle" );
        $list->SetHeader(utf8_decode("Estado"), "field:cod_estado" );
        $list->SetOption(utf8_decode("Opciones"),"field:cod_option; width:1%; onclikDisable:editarVehiculo( 2, this ); onclikEnable:editarVehiculo( 1, this ); onclikEdit:editarVehiculo( 99, this );" );
        $list->SetHidden("num_placax", "0" );


        $list->Display(self::$cConexion);

        $_SESSION["DINAMIC_LIST"] = $list;

        $Html = $list -> GetHtml();

        echo $Html;
    }

     /*****************************************************************************
     *  \fn: registrar                                                            *
     *  \brief: funcion para registros nuevos traylers                            *
     *  \author: Ing. Alexander Correa                                            *
     *  \date: 24/09/2015                                                         *
     *  \date modified:                                                           *
     *  \param:                                                                   *
     *  \param:                                                                   *
     *  \return popUp con el resultado de la operacion                            *
    ******************************************************************************/
    private function registrar(){
      #arma los objetos para cada una de las tablas necesarias
      $trayle = (object) $_POST['trayle']; #datos principales del trayler

      #pregunta si ya hay algun trayler con el n&uacute;mero ingresado
      $query = "SELECT num_trayle
            FROM ".BASE_DATOS.".tab_vehige_trayle
            WHERE num_trayle = '$trayle->num_trayle'";
      $consulta = new Consulta($query,  self::$cConexion, "BR");
      $indter = $consulta -> ret_arreglo();
      if(!$indter){
        #si ingresaron foto la mueve la directorio
        if($_FILES){
         if(move_uploaded_file($_FILES['foto']['tmp_name'],"../../".NOM_URL_APLICA."/".URL_REMOLQ.$trayle->num_trayle.".jpg")){
             $trayle->dir_fottra = URL_REMOLQ.$trayle->num_trayle.".jpg";
             $foto = "dir_fottra = '$trayle->dir_fottra',";
          }
        }else{
          $trayle->dir_fottra = "NULL";
        }
        #añade usuario y fecha de creacion
        $trayle->usr_creaci =$_SESSION['datos_usuario']['cod_usuari'];
        $trayle->fec_creaci = date("Y-m-d H:i:s");

        #realiza el registro
        $query ="INSERT INTO ".BASE_DATOS.".tab_vehige_trayle  
                              (num_trayle,cod_marcax,ano_modelo,cod_config,
                               tra_pesoxx,tra_capaci,tra_anchox,tra_altoxx,
                               tra_largox,tra_volpos,tip_tramit,cod_carroc,
                               ser_chasis,nom_propie,cod_colore,dir_fottra,
                               usr_creaci,fec_creaci)
                      VALUES  ('$trayle->num_trayle','$trayle->cod_marcax','$trayle->ano_modelo','$trayle->cod_config',
                               '$trayle->tra_pesoxx','$trayle->tra_capaci','$trayle->tra_anchox','$trayle->tra_altoxx',
                               '$trayle->tra_largox','$trayle->tra_volpos','$trayle->tip_tramit','$trayle->cod_carroc',
                               '$trayle->ser_chasis','$trayle->nom_propie','$trayle->cod_colore','$trayle->dir_fottra',
                               '$trayle->usr_creaci','$trayle->fec_creaci')";
        $consulta = new Consulta($query,  self::$cConexion);

        #crea la relacion entre la transportadora y el trayler
        $query = "INSERT INTO ".BASE_DATOS.".tab_transp_trayle
                (cod_transp,num_trayle,usr_creaci,fec_creaci)
             VALUES ('$trayle->cod_tercer','$trayle->num_trayle','$trayle->usr_creaci','$trayle->fec_creaci')";
        $consulta = new Consulta($query,  self::$cConexion);
        if ($consulta = new Consulta("COMMIT", self::$cConexion)) {
            header('Location: ../../'.NOM_URL_APLICA.'/index.php?cod_servic=1100&menant=1100&window=central&resultado=1&operacion=Registr&oacute;&opcion=123&conductor='.$trayle->nom_propie);
            /*$mensaje = "<font color='#000000'>Se Registr&oacute; El Trayler: <b>$trayle->num_trayle</b> Exitosamente.<br></font>";
            $mensaje .= "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closed()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
            $mens = new mensajes();
            echo $mens->correcto2("INSERTAR TRAYLER", $mensaje);*/

        }else{
          header('Location: ../../'.NOM_URL_APLICA.'/index.php?cod_servic=1100&menant=1100&window=central&resultado=1&operacion=Registr&oacute;&opcion=123&conductor='.$trayle->nom_propie);
            /*
            $mensaje = "<font color='#000000'>Ocurri&oacute; un Error inesperado al registrar el Trayler: <b>$tercer->num_trayle</b><br> verifique e intente nuevamente.<br>Si el error persiste informe a mesa de apoyo</font>";
            $mensaje .= "<br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
            $mens = new mensajes();
            echo $mens->error2("INSERTAR TRAYLER", $mensaje);*/

        }
      }else{
        #consulta si ya existe relacion entre el trayler y la transportadora
        $query = "SELECT num_trayle FROM ".BASE_DATOS.". tab_transp_trayle WHERE cod_transp = '$trayle->cod_tercer' AND num_trayle = '$trayle->num_trayle' ";
        $consulta = new Consulta($query,  self::$cConexion, "BR");
        $indter = $consulta -> ret_arreglo();
        if(!$indter){
          # si no hay relacion crea la relacion entre la transportadora y el trayler
          $query = "INSERT INTO ".BASE_DATOS.".tab_transp_trayle
                  (cod_transp,num_trayle,usr_creaci,fec_creaci)
               VALUES ('$trayle->cod_tercer','$trayle->num_trayle','$trayle->usr_creaci','$trayle->fec_creaci')";
          $consulta = new Consulta($query,  self::$cConexion);
        }
        $this->modificar("Registr&oacute;");
      }
      
    }

    /******************************************************************************
     *  \fn: activar                                                              *
     *  \brief: funci&oacute;n para activar una trayler                                  *
     *  \author: Ing. Alexander Correa                                            *
     *  \date: 24/09/2015                                                         *
     *  \date modified:                                                           *
     *  \param:                                                                   *
     *  \param:                                                                   *
     *  \return popUp con el resultado de la operacion                            *
    ******************************************************************************/
    private function activar(){
       $trayle = (object) $_POST['trayle']; //objeto para la tabla tab_tercer_tercer
       $trayle->usr_modifi =$_SESSION['datos_usuario']['cod_usuari'];
       $trayle->fec_modifi = date("Y-m-d H:i:s");
       if(!$trayle->num_trayle){
          $trayle->num_trayle = $_POST['cod_tercer'];
          $trayle->nom_propie = $_POST['nom_tercer'];
       }
        

        $query = "UPDATE ".BASE_DATOS.".tab_vehige_trayle 
                        SET ind_estado = 1,
                            usr_modifi = '$trayle->usr_modifi',
                            fec_modifi = '$trayle->fec_modifi'
                            WHERE num_trayle = '$trayle->num_trayle' ";
        $insercion = new Consulta($query, self::$cConexion, "R");
        if($consulta = new Consulta ("COMMIT",self::$cConexion)) {
           $mensaje = "Se activ&oacute; el Trayler de : ".$trayle->nom_propie." exitosamente.";
           $mensaje .= "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closed()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
           $mens = new mensajes();
           $mens -> correcto2("ACTIVAR TRAYLER",$mensaje);
        }


    }
    /******************************************************************************
     *  \fn: inactivar                                                            *
     *  \brief: funci&oacute;n para inactivar una trayler                                *
     *  \author: Ing. Alexander Correa                                            *
     *  \date: 24/09/2015                                                         *
     *  \date modified:                                                           *
     *  \param:                                                                   *
     *  \param:                                                                   *
     *  \return popUp con el resultado de la operacion                            *
    ******************************************************************************/
    private function inactivar(){
     $trayle = (object) $_POST['trayle']; //objeto para la tabla tab_tercer_tercer
       $trayle->usr_modifi =$_SESSION['datos_usuario']['cod_usuari'];
       $trayle->fec_modifi = date("Y-m-d H:i:s");
       if(!$trayle->num_trayle){
          $trayle->num_trayle = $_POST['cod_tercer'];
          $trayle->nom_propie = $_POST['nom_tercer'];
       }
        

        $query = "UPDATE ".BASE_DATOS.".tab_vehige_trayle 
                        SET ind_estado = 0,
                            usr_modifi = '$trayle->usr_modifi',
                            fec_modifi = '$trayle->fec_modifi'
                            WHERE num_trayle = '$trayle->num_trayle' ";
        $insercion = new Consulta($query, self::$cConexion, "R");
        if($consulta = new Consulta ("COMMIT",self::$cConexion)) {
           $mensaje = "Se inactiv&oacute; el  Trayler de : ".$trayle->nom_propie." exitosamente.";
           $mensaje .= "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closed()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
           $mens = new mensajes();
           $mens -> correcto2("INACTIVAR TRAYLER",$mensaje);
        }


    }
    /******************************************************************************
     *  \fn: modificar                                                            *
     *  \brief: funci&oacute;n para modificar una conductores                            *
     *  \author: Ing. Alexander Correa                                            *
     *  \date: 23/09/2015                                                         *
     *  \date modified:                                                           *
     *  \param:                                                                   *
     *  \param:                                                                   *
     *  \return popUp con el resultado de la operacion                            *
    ******************************************************************************/
    private function modificar($operacion = null){
      if(!$operacion){
        $operacion = "Modific&oacute;";
      }
      $trayle = (object) $_POST['trayle']; #datos principales del tercero
      $trayle->usr_modifi =$_SESSION['datos_usuario']['cod_usuari'];
      $trayle->fec_modifi = date("Y-m-d H:i:s");
      if(!$trayle->num_trayle){
        $trayle->num_trayle = $trayle->cod_tercer;
      }
      $foto = "";
      if($_FILES){
         if(move_uploaded_file($_FILES['foto']['tmp_name'],"../../".NOM_URL_APLICA."/".URL_REMOLQ.$trayle->num_trayle.".jpg")){
             $trayle->dir_fottra = URL_REMOLQ.$trayle->num_trayle.".jpg";
             $foto = "dir_fottra = '$trayle->dir_fottra',";
          }
      }
      $query ="UPDATE ".BASE_DATOS.".tab_vehige_trayle 
                                    SET
                        cod_marcax = '$trayle->cod_marcax',
                        ano_modelo = '$trayle->ano_modelo',
                        cod_config = '$trayle->cod_config',
                        tra_pesoxx = '$trayle->tra_pesoxx',
                        tra_capaci = '$trayle->tra_capaci',
                        tra_anchox = '$trayle->tra_anchox',
                        tra_altoxx = '$trayle->tra_altoxx',
                        tra_largox = '$trayle->tra_largox',
                        tra_volpos = '$trayle->tra_volpos',
                        tip_tramit = '$trayle->tip_tramit',
                        cod_carroc = '$trayle->cod_carroc',
                        ser_chasis = '$trayle->ser_chasis',
                        nom_propie = '$trayle->nom_propie',
                        cod_colore = '$trayle->cod_colore',
                                      $foto
                        usr_modifi = '$trayle->usr_modifi',
                        fec_modifi = '$trayle->fec_modifi'
               WHERE num_trayle = '$trayle->num_trayle'";
      $insercion = new Consulta($query,self::$cConexion,"R");

      if ($consulta = new Consulta("COMMIT", self::$cConexion)) {
        header('Location: ../../'.NOM_URL_APLICA.'/index.php?cod_servic=1100&menant=1100&window=central&resultado=1&operacion='.$operacion.'&opcion=123&conductor='.$trayle->nom_propie);
           /* $mensaje = "<font color='#000000'>Se Modific&oacute; El Trayler de: <b>$trayle->nom_propie</b> Exitosamente.<br></font>";
            $mensaje .= "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closed()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
            $mens = new mensajes();
            echo $mens->correcto2("MODIFICAR TERCERO", $mensaje)*/;


      }else{
          header('Location: ../../'.NOM_URL_APLICA.'/index.php?cod_servic=1100&menant=1100&window=central&opcion=123&resultado=0');
          /*$mensaje = "<font color='#000000'>Ocurri&oacute; un Error inesperado al modificar el Trayler de: <b>$trayle->nom_propie</b><br> verifique e intente nuevamente.<br>Si el error persiste informe a mesa de apoyo</font>";
          $mensaje .= "<br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
          $mens = new mensajes();
          echo $mens->error2("MODIFICAR TERCERO", $mensaje);
*/
      }
  }

    /******************************************************************************
     *  \fn: getDatosTrayler                                                      *
     *  \brief: funci&oacute;n que consulta los datos de trayler                         *
     *  \author: Ing. Alexander Correa                                            *
     *  \date: 23/09/2015                                                         *
     *  \date modified:                                                           *
     *  \param:                                                                   *
     *  \param:                                                                   *
     *  \return $data => objeto con los datos de la consulta                      *
    ******************************************************************************/

    public function getDatosTrayler($num_trayle = 0){

      #objeto que contiene los datos a retornar 
      $datos = new stdClass();

      #consulta las marcas
      $query = "SELECT cod_martra, nom_martra
           FROM ".BASE_DATOS.".tab_vehige_martra
           ORDER BY nom_martra ASC";
      $consulta = new Consulta($query, self::$cConexion);
      $marcas = $consulta->ret_matriz("a");
      
      #agrega las marcas al objeto
      $datos->marcas = $marcas;

      #consulta las configuraciones
      $query = "SELECT a.num_config,a.num_config
              FROM ".BASE_DATOS.".tab_vehige_config a
              WHERE a.num_config IN (2,3,4)  
              ORDER BY a.num_config ASC ";
      $consulta = new Consulta($query, self::$cConexion);
      $configuraciones = $consulta->ret_matriz("a");

      #agrega las configuraciones al objeto
      $datos->configuraciones = $configuraciones;

      #consulta las carrocerias
      $query = "SELECT cod_carroc, nom_carroc
            FROM ".BASE_DATOS.".tab_vehige_carroc
            ORDER BY nom_carroc ASC";
      $consulta = new Consulta($query, self::$cConexion);
      $carrocerias = $consulta->ret_matriz("a");
      
      #agrega las carrocerias al objeto
      $datos->carrocerias = $carrocerias;
      
      #consulta los colores
      $query = "SELECT cod_colorx, nom_colorx
            FROM ".BASE_DATOS.".tab_vehige_colore
            ORDER BY nom_colorx ASC";
      $consulta = new Consulta($query, self::$cConexion);
      $colores = $consulta->ret_matriz("a");
      
      #agrega las colores al objeto
      $datos->colores = $colores;

      #consulta los datos del trayler
      $query = "SELECT num_trayle,cod_marcax,ano_modelo,cod_config,
                       tra_pesoxx,tra_capaci,tra_anchox,tra_altoxx,
                       tra_largox,tra_volpos,tip_tramit,cod_carroc,
                       ser_chasis,nom_propie,cod_colore,ind_estado,
                       dir_fottra
                FROM ".BASE_DATOS.".tab_vehige_trayle 
                WHERE num_trayle = '$num_trayle'";
      $consulta = new Consulta($query, self::$cConexion);
      $trayler= $consulta->ret_matrix("a");
      $datos->principal =(object) $trayler[0];
      $datos->principal->nom_propie = utf8_encode($datos->principal->nom_propie);

      $query = "SELECT cod_operad,nom_operad
               FROM ".BASE_DATOS.".tab_genera_opegps
               WHERE ind_estado = '1' 
           ORDER BY 2 ";
      $consulta = new Consulta($query, self::$cConexion);
      $operadores = $consulta->ret_matriz("a");
      $operadores = array_merge(self::$cNull,$operadores);
      $datos->opegps = $operadores;

      $query = "SELECT a.cod_operad, b.nom_operad, a.usr_gpsxxx, 
                       a.clv_gpsxxx, a.idx_gpsxxx
               FROM ".BASE_DATOS.".tab_trayle_opegps a
               INNER JOIN ".BASE_DATOS.".tab_genera_opegps b ON
                a.cod_operad = b.cod_operad
               WHERE a.num_trayle = '$num_trayle'";
      $consulta = new Consulta($query, self::$cConexion);
      $ope_remolq = $consulta->ret_matriz("a");
      $datos->principal->ope_remolq = $ope_remolq;
      
      return $datos;
    }

    /*! \fn: getDatosVehiculo
     *  \brief: trae los datos necesarios para registrar y modificar un vehiculo
     *  \author: Ing. Alexander Correa
     *  \date: 08/10/2015
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return objeto con los datos de la consulta
     */
    public function getDatosVehiculo($num_placax = '0'){

      $datos = new stdClass();
      #consulta los datos principales del vehiculo
      $query = "SELECT a.num_placax,a.cod_marcax,a.cod_lineax,
                       a.ano_modelo,a.ano_repote,a.cod_tipveh,
                       a.cod_carroc,a.num_motorx,a.num_seriex,
                       a.val_pesove,a.val_capaci,a.num_config,
                       a.fec_revmec,a.nom_vincul,a.fec_vigvin,
                       a.num_agases,a.fec_vengas,a.reg_nalcar,
                       a.num_tarope,a.cod_califi,a.ind_chelis,
                       a.num_poliza,a.nom_asesoa,a.fec_vigfin,
                       a.num_polirc,a.cod_aseprc,a.fec_venprc,
                       a.cod_tenedo,a.cod_propie,a.cod_conduc,
                       a.cod_colorx,a.num_tarpro,a.obs_vehicu,
                       a.usr_creaci,a.fec_creaci,a.usr_modifi,
                       a.fec_modifi,a.dir_fotfre,a.dir_fotizq,
                        a.cod_opegps,a.usr_gpsxxx,a.clv_gpsxxx,a.idx_gpsxxx,
                       a.dir_fotder,a.dir_fotpos,b.abr_tercer nom_poseed,
                       c.abr_tercer nom_conduc, d.abr_tercer nom_propie,
                       a.ind_estado, MAX(e.num_noveda), e.num_trayle
                  FROM ".BASE_DATOS.".tab_vehicu_vehicu a
                  INNER JOIN ".BASE_DATOS.".tab_tercer_tercer b ON b.cod_tercer = a.cod_tenedo
                  INNER JOIN ".BASE_DATOS.".tab_tercer_tercer c ON c.cod_tercer = a.cod_conduc
                  INNER JOIN ".BASE_DATOS.".tab_tercer_tercer d ON d.cod_tercer = a.cod_propie
                  LEFT JOIN ".BASE_DATOS.".tab_trayle_placas e ON a.num_placax = e.num_placax
                 WHERE a.num_placax = '$num_placax' GROUP BY e.num_noveda, a.num_placax";

      $consulta = new Consulta($query, self::$cConexion);
      $vehiculo= $consulta->ret_matrix("a");
      $datos->principal =(object) $vehiculo[(count($vehiculo)-1)];
      #las marcas
      $query = "SELECT cod_marcax,nom_marcax
               FROM ".BASE_DATOS.".tab_genera_marcas
           ORDER BY nom_marcax ";
      $consulta = new Consulta($query, self::$cConexion);
      $marcas = $consulta->ret_matriz("a");
      $marcas = array_merge(self::$cNull,$marcas);
      #agrega las marcas
      $datos->marcas = $marcas;

      if($num_placax === '0'){ #para registros nuevos
        #se cargan las lineas de la primera marca en el arreglo de maracas
         $query = "SELECT cod_lineax,nom_lineax
               FROM ".BASE_DATOS.".tab_vehige_lineas
              WHERE cod_marcax = '".$marcas[0][0]."'
           ORDER BY nom_lineax ";
        $consulta = new Consulta($query, self::$cConexion);
        $lineas = $consulta->ret_matriz("a");
      }else{
        //trae las lineas de la marca del vehiculo
        $query = "SELECT cod_lineax,nom_lineax
               FROM ".BASE_DATOS.".tab_vehige_lineas
              WHERE cod_marcax = '".$datos->principal->cod_marcax."'
           ORDER BY nom_lineax ";
        $consulta = new Consulta($query, self::$cConexion);
        $lineas = $consulta->ret_matriz("a");
      }
      $lineas = array_merge(self::$cNull,$lineas);
      $datos->lineas = $lineas;
      #los colores
      $query = "SELECT cod_colorx,nom_colorx
               FROM ".BASE_DATOS.".tab_vehige_colore
           ORDER BY 2 ";
      $consulta = new Consulta($query, self::$cConexion);
      $colores = $consulta->ret_matriz("a");
      $colores = array_merge(self::$cNull,$colores);
      $datos->colores = $colores;
      #los GPS
      $query = "SELECT cod_operad,nom_operad
               FROM ".BASE_DATOS.".tab_genera_opegps
               WHERE ind_estado = '1' 
           ORDER BY 2 ";
      $consulta = new Consulta($query, self::$cConexion);
      $operadores = $consulta->ret_matriz("a");
      $operadores = array_merge(self::$cNull,$operadores);
      $datos->opegps = $operadores;

      #las configuraciones
      $query = "SELECT num_config,num_config
               FROM ".BASE_DATOS.".tab_vehige_config
               WHERE ind_estado = '1'
           ORDER BY num_config ";

      $consulta = new Consulta($query, self::$cConexion);
      $configuraciones = $consulta->ret_matriz("a");
      $configuraciones = array_merge(self::$cNull,$configuraciones);

      $datos->configuraciones = $configuraciones;

      //trae las carrocerias de vehiculos
      $query = "SELECT cod_carroc,nom_carroc
               FROM ".BASE_DATOS.".tab_vehige_carroc
           ORDER BY nom_carroc ";
      $consulta = new Consulta($query, self::$cConexion);
      $carrocerias = $consulta->ret_matriz("a");
      $carrocerias = array_merge(self::$cNull,$carrocerias);

      $datos->carrocerias = $carrocerias;

      #las vinculaciones
      $query = "SELECT cod_tipveh,nom_tipveh
               FROM ".BASE_DATOS.".tab_genera_tipveh
               ORDER BY nom_tipveh";
      $consulta = new Consulta($query, self::$cConexion);
      $vinculaciones = $consulta->ret_matriz("a");
      $vinculaciones = array_merge(self::$cNull,$vinculaciones);

      $datos->vinculaciones = $vinculaciones;

      //las calificaciones
      $query = "SELECT cod_califi,nom_califi
               FROM ".BASE_DATOS.".tab_genera_califi 
               ORDER BY nom_califi";
      $consulta = new Consulta($query, self::$cConexion);
      $calificaciones = $consulta->ret_matriz("a");
      $calificaciones = array_merge(self::$cNull,$calificaciones);

      $datos->calificaciones = $calificaciones;

      //los traylers
      $query = "SELECT DISTINCT(num_trayle),num_trayle
             FROM ".BASE_DATOS.".tab_vehige_trayle
             ORDER BY num_trayle";

      $consulta = new Consulta($query, self::$cConexion);
      $remolques = $consulta->ret_matriz("a");
      $remolques = array_merge(self::$cNull,$remolques);

      $datos->remolques = $remolques;

      return $datos;
    }

        /*! \fn: verificar
        *  \brief: funcion para saber si se repite un numero de trayler
        *  \author: Ing. Alexander Correa
        *  \date: 29/09/2015
        *  \date modified: dia/mes/año
        *  \param: 
        *  \param: 
        *  \return true si no existe, false si ya esta registrado
        */
          
    public function verificar(){
        $trayle = $_POST['trayle'];
        $query = "SELECT num_trayle
              FROM ".BASE_DATOS.".tab_vehige_trayle
              WHERE num_trayle = '$trayle'";
        $consulta = new Consulta($query,  self::$cConexion, "BR");
        $indter = $consulta -> ret_arreglo();
        if(!$indter){
            echo true;
        }else{
           $datos = $this->getDatosTrayler($trayle);
           $datos = json_encode($datos->principal);
           echo $datos;
        }
    }
        /*! \fn: verificarPlaca
        *  \brief: funcion para saber si se repite un numero de placa
        *  \author: Ing. Alexander Correa
        *  \date: 29/09/2015
        *  \date modified: dia/mes/año
        *  \param: 
        *  \param: 
        *  \return true si no existe, false si ya esta registrado
        */
          
    public function verificarPlaca(){
        $placa = $_POST['placa'];
        $query = "SELECT num_placax
              FROM ".BASE_DATOS.".tab_vehicu_vehicu
              WHERE num_placax = '$placa'";
        $consulta = new Consulta($query,  self::$cConexion, "BR");
        $indter = $consulta -> ret_arreglo();
        if(!$indter){
            echo true;
        }else{
            $datos = $this->getDatosVehiculo($placa);
            $data->lineas = $datos->lineas;
            $data->principal = $datos->principal;
            $data = json_encode($data);
            echo $data;
        }
    }


    /*! \fn: getTercerTransp
     *  \brief: Consulta la lista de terceros de una transportadora segun su actividad
     *  \author: Ing. Alexander Correa
     *  \date: 13/10/2015
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return lista de los conductores
     */

    public function getTercerTransp(){

        $transp = $_REQUEST['transp'];
        $activi = $_REQUEST['cod_activi'];

        $mSql = "SELECT a.cod_tercer, a.abr_tercer,a.num_telef1 
                        FROM ".BASE_DATOS.".tab_tercer_tercer a 
                        INNER JOIN ".BASE_DATOS.".tab_tercer_activi b ON a.cod_tercer = b.cod_tercer
                        INNER JOIN ".BASE_DATOS.".tab_genera_activi c ON c.cod_activi = b.cod_activi
                        INNER JOIN ".BASE_DATOS.".tab_transp_tercer d ON d.cod_tercer = a.cod_tercer
                        WHERE c.cod_activi = '$activi' AND d.cod_transp = '$transp'";
        $_SESSION["queryXLS"] = $mSql;

        if(!class_exists(DinamicList)) {
          include_once("../".DIR_APLICA_CENTRAL."/lib/general/dinamic_list.inc");                         
        }
        $list = new DinamicList(self::$cConexion, $mSql, "2" , "no", 'ASC');
        $list->SetClose('no');
        $list->SetHeader(utf8_decode("Documento"), "field:a.cod_tercer;  width:1%; type:link; onclick:pintar( this, $activi );");
        $list->SetHeader(utf8_decode("Nombre"), "field:a.abr_tercer; width:1%"); #warning1
        $list->SetHeader(utf8_decode("Teléfono"), "field:a.num_telef1; width:1%");
        $list->Display(self::$cConexion);

        $_SESSION["DINAMIC_LIST"] = $list;

        $Html = $list -> GetHtml();

        echo $Html;

    }

    /*! \fn: getLineas
     *  \brief: trael la mista de las lineas de una marca
     *  \author: Ing. Alexander Correa
     *  \date: 21/10/2015
     *  \date modified: dia/mes/año
     *  \param: $marca = maraca para la cual se van a consultar las lineas, llega por request.
     *  \param: 
     *  \return Retorna un Select con las lineas 
     */
    
    public function getLineas(){
      $marca = $_REQUEST['marca'];
      $query = "SELECT cod_lineax,nom_lineax
               FROM ".BASE_DATOS.".tab_vehige_lineas
              WHERE cod_marcax = '$marca'
           ORDER BY nom_lineax ";
        $consulta = new Consulta($query, self::$cConexion);
        $lineas = $consulta->ret_matriz("a");
        $select = '<select validate="select" id="cod_lineaxID" obl="1" name="vehicu[cod_lineax]" class="form_01">
                      <option value="">Seleccione un Elemento de la Lista</option>';
        '</select>';
        foreach ($lineas as $key => $value) {
            $select .= "<option value='".$value['cod_lineax']."'>".$value['nom_lineax']."</option>";
        }
        $select .='</select>';
       echo $select; 
    }
   

    public function registrarVehiculo(){
      
      $vehicu = (object) $_POST["vehicu"];
      $vehicu->ind_estado = 1;
      $vehicu->usr_creaci =$_SESSION['datos_usuario']['cod_usuari'];
      $vehicu->fec_creaci = date("Y-m-d H:i:s");
      $query = "SELECT num_placax
            FROM ".BASE_DATOS.".tab_vehicu_vehicu
            WHERE num_placax = '$vehicu->num_placax'";
      $consulta = new Consulta($query,  self::$cConexion, "BR");
      $indter = $consulta -> ret_arreglo();
      $sql = "SELECT cod_tercer FROM ".BASE_DATOS.".tab_tercer_conduc WHERE cod_tercer = '$vehicu->cod_conduc'";
      $consulta = new Consulta($sql,  self::$cConexion, "BR");
      $conductor = $consulta -> ret_arreglo();
      if($conductor){
        if(!$indter){
          #mueve las fotos cargadas
          if($_FILES['fotoFrente']){
            if(move_uploaded_file($_FILES['fotoFrente']['tmp_name'],"../../".NOM_URL_APLICA."/".URL_VEHICU."frente_".$vehicu->num_placax.".jpg")){
              $vehicu->dir_fotfre = "frente_".$vehicu->num_placax.".jpg";
            }else{
              $vehicu->dir_fotfre = "NULL";
            }
          }else{
            $vehicu->dir_fotfre = "NULL";
          }
          if($_FILES['fotoIzquierda']){
            if(move_uploaded_file($_FILES['fotoIzquierda']['tmp_name'],"../../".NOM_URL_APLICA."/".URL_VEHICU."izquierda_".$vehicu->num_placax.".jpg")){
              $vehicu->dir_fotizq = "izquierda_".$vehicu->num_placax.".jpg";
            }else{
              $vehicu->dir_fotizq = "NULL";
            }
          }else{
            $vehicu->dir_fotizq = "NULL";
          }
          if($_FILES['fotoDerecha']){
            if(move_uploaded_file($_FILES['fotoDerecha']['tmp_name'],"../../".NOM_URL_APLICA."/".URL_VEHICU."derecha_".$vehicu->num_placax.".jpg")){
              $vehicu->dir_fotder = "derecha_".$vehicu->num_placax.".jpg";
            }else{
              $vehicu->dir_fotder = "NULL";
            }
          }else{
            $vehicu->dir_fotpos = "NULL";
          }
          if($_FILES['fotoPosterior']){
            if(move_uploaded_file($_FILES['fotoPosterior']['tmp_name'],"../../".NOM_URL_APLICA."/".URL_VEHICU."posterior_".$vehicu->num_placax.".jpg")){
              $vehicu->dir_fotpos = "posterior_".$vehicu->num_placax.".jpg";
            }else{
              $vehicu->dir_fotpos = "NULL";
            }
          }
            #inserci&oacute;n de los datos b&aacute;sicos de el vehiculo
          $query = "INSERT ".BASE_DATOS.".tab_vehicu_vehicu
                          (num_placax,cod_marcax,cod_lineax,ano_modelo,cod_colorx,cod_carroc,num_motorx,
                           num_seriex,num_chasis,val_pesove,val_capaci,reg_nalcar,num_poliza,nom_asesoa,
                           fec_vigfin,ano_repote,num_config,cod_propie,cod_tenedo,cod_conduc,nom_vincul,
                           num_tarpro,cod_califi,fec_vigvin,num_polirc,fec_venprc,cod_aseprc,ind_estado,
                           cod_opegps,usr_gpsxxx,clv_gpsxxx,idx_gpsxxx,
                           obs_vehicu,cod_tipveh,ind_chelis,fec_revmec,num_agases,fec_vengas,dir_fotfre,
                           dir_fotizq,dir_fotder,dir_fotpos,usr_creaci,fec_creaci,num_tarope)
                   VALUES
                          ('$vehicu->num_placax','$vehicu->cod_marcax','$vehicu->cod_lineax','$vehicu->ano_modelo','$vehicu->cod_colorx','$vehicu->cod_carroc','$vehicu->num_motorx',
                           '$vehicu->num_seriex','$vehicu->num_chasis','$vehicu->val_pesove','$vehicu->val_capaci','$vehicu->reg_nalcar','$vehicu->num_poliza','$vehicu->nom_asesoa',
                           '$vehicu->fec_vigfin','$vehicu->ano_repote','$vehicu->num_config','$vehicu->cod_propie','$vehicu->cod_tenedo','$vehicu->cod_conduc','$vehicu->nom_vincul',
                           '$vehicu->num_tarpro','$vehicu->cod_califi','$vehicu->fec_vigvin','$vehicu->num_polirc','$vehicu->fec_venprc','$vehicu->cod_aseprc','$vehicu->ind_estado',
                           '$vehicu->cod_opegps','$vehicu->usr_gpsxxx','$vehicu->clv_gpsxxx','$vehicu->idx_gpsxxx',
                           '$vehicu->obs_vehicu','$vehicu->cod_tipveh','$vehicu->ind_chelis','$vehicu->fec_revmec','$vehicu->num_agases','$vehicu->fec_vengas','$vehicu->dir_fotfre',
                           '$vehicu->dir_fotizq','$vehicu->dir_fotder','$vehicu->dir_fotpos','$vehicu->usr_creaci','$vehicu->fec_creaci','$vehicu->num_tarope')";
          $insercion = new Consulta($query,self::$cConexion,"R");

          #realcion vehiculo-transportadora
          $query = "INSERT INTO ".BASE_DATOS.".tab_transp_vehicu
                 (cod_transp,num_placax,usr_creaci,fec_creaci)
              VALUES ('$vehicu->cod_transp','$vehicu->num_placax','$vehicu->usr_creaci','$vehicu->fec_creaci')";
          $insercion = new Consulta($query,self::$cConexion,"R");

          if($vehicu->num_trayle){
            if($vehicu->num_config != '2' && $vehicu->num_config != '3' && $vehicu->num_config != '4'){
                  // trae la ultima novedad de acuerdo al vehiculo
                  $query = "SELECT Max(a.num_noveda) AS maximo
                                  FROM ".BASE_DATOS.".tab_trayle_placas a
                                  WHERE a.num_placax = '$vehicu->num_placax'";
                  //obtiene el consecutivo
                  $consec = new Consulta($query, self::$cConexion);
                  $ultimo = $consec -> ret_matrix("a");
                  $nuevo_consec = $ultimo[0]['maximo']+1; 
                        $query = "INSERT ".BASE_DATOS.".tab_trayle_placas
                                       VALUES ('$vehicu->num_placax','$vehicu->num_trayle',
                                               '$nuevo_consec','$vehicu->fec_modifi','S')";
                         $consulta = new Consulta($query, self::$cConexion, "R");
            }
          } 


          #relaciono conductor, poseedor y propietario del vehiculo con la transportadora
          if($vehicu->cod_propie){
             #inserta la relacion entre el propietario y la transportadora si no existe previamente
            $query = "SELECT cod_transp FROM ".BASE_DATOS.".tab_transp_tercer WHERE cod_transp = '$vehicu->cod_transp' AND cod_tercer = '$vehicu->cod_propie'";
            $consulta = new Consulta($query, self::$cConexion);
            $existe = $consulta->ret_matriz("a");

            if(!$existe){
              $query = "INSERT INTO ".BASE_DATOS.".tab_transp_tercer
                             (cod_transp,cod_tercer,usr_creaci,fec_creaci)
                                VALUES ('$vehicu->cod_transp','$vehicu->cod_propie','$vehicu->usr_creaci','$vehicu->fec_creaci')";
                      $insercion = new Consulta($query,self::$cConexion,"R");
            }
          }
          if($vehicu->cod_tenedo){
             #inserta la relacion entre el tenedor y la transportadora si no existe previamente
            $query = "SELECT cod_transp FROM ".BASE_DATOS.".tab_transp_tercer WHERE cod_transp = '$vehicu->cod_transp' AND cod_tercer = '$vehicu->cod_tenedo'";
            $consulta = new Consulta($query, self::$cConexion);
            $existe = $consulta->ret_matriz("a");

            if(!$existe){
              $query = "INSERT INTO ".BASE_DATOS.".tab_transp_tercer
                             (cod_transp,cod_tercer,usr_creaci,fec_creaci)
                                VALUES ('$vehicu->cod_transp',$vehicu->cod_tenedo','$vehicu->usr_creaci','$vehicu->fec_creaci')";
                      $insercion = new Consulta($query,self::$cConexion,"R");
            }
          }

          
          if ($consulta = new Consulta("COMMIT", self::$cConexion)) {
                header('Location: ../../'.NOM_URL_APLICA.'/index.php?cod_servic=1120&menant=1120&window=central&resultado=1&operacion=Registr&oacute;&opcion=123&placa='.$vehicu->num_placax);
                /*$mensaje = "<font color='#000000'>Se Registr&oacute; El Trayler: <b>$trayle->num_trayle</b> Exitosamente.<br></font>";
                $mensaje .= "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closed()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
                $mens = new mensajes();
                echo $mens->correcto2("INSERTAR TRAYLER", $mensaje);*/


          }else{
            header('Location: ../../'.NOM_URL_APLICA.'/index.php?cod_servic=1120&menant=1120&window=central&opcion=123&resultado=0');
              /*
              $mensaje = "<font color='#000000'>Ocurri&oacute; un Error inesperado al registrar el Trayler: <b>$tercer->num_trayle</b><br> verifique e intente nuevamente.<br>Si el error persiste informe a mesa de apoyo</font>";
              $mensaje .= "<br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
              $mens = new mensajes();
              echo $mens->error2("INSERTAR TRAYLER", $mensaje);*/

          }
        }else{ //si es un vehiculo ya registrado
          $query = "SELECT num_placax FROM ".BASE_DATOS.".tab_transp_vehicu WHERE cod_transp = '$vehicu->cod_transp' AND num_placax = '$vehicu->num_placax' ";
          $consulta = new Consulta($query,  self::$cConexion, "BR");
          $indter = $consulta -> ret_arreglo();
            if(!$indter){
            #realcion vehiculo-transportadora
            $query = "INSERT INTO ".BASE_DATOS.".tab_transp_vehicu
                   (cod_transp,num_placax,usr_creaci,fec_creaci)
                VALUES ('$vehicu->cod_transp','$vehicu->num_placax','$vehicu->usr_creaci','$vehicu->fec_creaci')";
            if($insercion = new Consulta($query,self::$cConexion,"R")){
              $this->modificarVehiculo("Registr&oacute;");
            }else{
              header('Location: ../../'.NOM_URL_APLICA.'/index.php?cod_servic=1120&menant=1120&window=central&opcion=123&resultado=0');
            }          
          }else{
             $this->modificarVehiculo("Registr&oacute;");
          }
        }
      }else{
        header('Location: ../../'.NOM_URL_APLICA.'/index.php?cod_servic=1120&menant=1120&window=central&opcion=123&resultado=2');//cuando el conductor esta marcado como tal pero no registrado en la tabla de conductores
      }
    }

  public function modificarVehiculo($operacion = null){
    if(!$operacion){
      $operacion = "Registr&oacute;";
    }
    $vehicu = (object) $_POST["vehicu"];
    $sql = "SELECT cod_tercer FROM ".BASE_DATOS.".tab_tercer_conduc WHERE cod_tercer = '$vehicu->cod_conduc'";
    $consulta = new Consulta($sql,  self::$cConexion, "BR");
    $conductor = $consulta -> ret_arreglo();
    if($conductor){
      $vehicu->ind_estado = 1;
      $vehicu->usr_modifi =$_SESSION['datos_usuario']['cod_usuari'];
      $vehicu->fec_modifi = date("Y-m-d H:i:s");

      $dir_fotfre = "";
      $dir_fotizq = "";
      $dir_fotder = "";
      $dir_fotpos = "";
      if($_FILES['fotoFrente']){
        if(move_uploaded_file($_FILES['fotoFrente']['tmp_name'],"../../".NOM_URL_APLICA."/".URL_VEHICU."frente_".$vehicu->num_placax.".jpg")){
          $vehicu->dir_fotfre = "frente_".$vehicu->num_placax.".jpg";
          $dir_fotfre = ",dir_fotfre = '$vehicu->dir_fotfre'";
        }
      }
      if($_FILES['fotoIzquierda']){
        if(move_uploaded_file($_FILES['fotoIzquierda']['tmp_name'],"../../".NOM_URL_APLICA."/".URL_VEHICU."izquierda_".$vehicu->num_placax.".jpg")){
          $vehicu->dir_fotizq = "izquierda_".$vehicu->num_placax.".jpg";
          $dir_fotizq = ",dir_fotizq = '$vehicu->dir_fotizq'";         
        }
      }
      if($_FILES['fotoDerecha']){
        if(move_uploaded_file($_FILES['fotoDerecha']['tmp_name'],"../../".NOM_URL_APLICA."/".URL_VEHICU."derecha_".$vehicu->num_placax.".jpg")){
          $vehicu->dir_fotder = "derecha_".$vehicu->num_placax.".jpg";
          $dir_fotder = ",dir_fotder = '$vehicu->dir_fotder'";
        }
      }
      if($_FILES['fotoPosterior']){
        if(move_uploaded_file($_FILES['fotoPosterior']['tmp_name'],"../../".NOM_URL_APLICA."/".URL_VEHICU."posterior_".$vehicu->num_placax.".jpg")){
          $vehicu->dir_fotpos = "posterior_".$vehicu->num_placax.".jpg";
          $dir_fotpos = ",dir_fotpos = '$vehicu->dir_fotpos'";
        }
      }
      #query de modificacion
      $query = "UPDATE ".BASE_DATOS.".tab_vehicu_vehicu
                                      SET
                        cod_marcax = '$vehicu->cod_marcax',
                        cod_lineax = '$vehicu->cod_lineax',
                        ano_modelo = '$vehicu->ano_modelo',
                        cod_colorx = '$vehicu->cod_colorx',
                        cod_carroc = '$vehicu->cod_carroc',
                        num_motorx = '$vehicu->num_motorx',
                        num_seriex = '$vehicu->num_seriex',
                        num_chasis = '$vehicu->num_chasis',
                        val_pesove = '$vehicu->val_pesove',
                        val_capaci = '$vehicu->val_capaci',
                        reg_nalcar = '$vehicu->reg_nalcar',
                        num_poliza = '$vehicu->num_poliza',
                        nom_asesoa = '$vehicu->nom_asesoa',
                        fec_vigfin = '$vehicu->fec_vigfin',
                        ano_repote = '$vehicu->ano_repote',
                        num_config = '$vehicu->num_config',
                        cod_propie = '$vehicu->cod_propie',
                        cod_tenedo = '$vehicu->cod_tenedo',
                        cod_conduc = '$vehicu->cod_conduc',
                        nom_vincul = '$vehicu->nom_vincul',
                        num_tarpro = '$vehicu->num_tarpro',
                        cod_califi = '$vehicu->cod_califi',
                        fec_vigvin = '$vehicu->fec_vigvin',
                        num_polirc = '$vehicu->num_polirc',
                        fec_venprc = '$vehicu->fec_venprc',
                        cod_aseprc = '$vehicu->cod_aseprc',
                        ind_estado = '$vehicu->ind_estado',
                        cod_opegps = '$vehicu->cod_opegps',
                        usr_gpsxxx = '$vehicu->usr_gpsxxx',
                        clv_gpsxxx = '$vehicu->clv_gpsxxx',
                        idx_gpsxxx = '$vehicu->idx_gpsxxx',
                        obs_vehicu = '$vehicu->obs_vehicu',
                        cod_tipveh = '$vehicu->cod_tipveh',
                        ind_chelis = '$vehicu->ind_chelis',
                        fec_revmec = '$vehicu->fec_revmec',
                        num_agases = '$vehicu->num_agases',
                        fec_vengas = '$vehicu->fec_vengas',
                        usr_modifi = '$vehicu->usr_modifi',
                        fec_modifi = '$vehicu->fec_modifi',
                        num_tarope = '$vehicu->num_tarope'
                                      $dir_fotfre
                                      $dir_fotizq 
                                      $dir_fotder 
                                      $dir_fotpos
      WHERE num_placax = '$vehicu->num_placax'";
      $insercion = new Consulta($query, self::$cConexion, "R");
      if($vehicu->num_trayle){
        if($vehicu->num_config != '2' && $vehicu->num_config != '3' && $vehicu->num_config != '4'){
              // trae la ultima novedad de acuerdo al vehiculo

              $query = "SELECT Max(a.num_noveda) AS maximo
                              FROM ".BASE_DATOS.".tab_trayle_placas a
                              WHERE a.num_placax = '$vehicu->num_placax'";
              //obtiene el consecutivo
              $consec = new Consulta($query, self::$cConexion);
              $ultimo = $consec -> ret_matrix("a");
              $nuevo_consec = $ultimo[0]['maximo']+1; 

                    $query = "INSERT ".BASE_DATOS.".tab_trayle_placas
                                   VALUES ('$vehicu->num_placax','$vehicu->num_trayle',
                                           '$nuevo_consec','$vehicu->fec_modifi','S')";
                    
                     $consulta = new Consulta($query, self::$cConexion, "R");
        }
      } 

      if($vehicu->cod_propie){
        #inserta la relacion entre el propietario y la transportadora si no existe previamente
        $query = "SELECT cod_transp FROM ".BASE_DATOS.".tab_transp_tercer WHERE cod_transp = '$vehicu->cod_transp' AND cod_tercer = '$vehicu->cod_propie'";
        $consulta = new Consulta($query, self::$cConexion);
        $existe = $consulta->ret_matriz("a");

        if(!$existe){
          $query = "INSERT INTO ".BASE_DATOS.".tab_transp_tercer
                         (cod_transp,cod_tercer,usr_creaci,fec_creaci)
                            VALUES ('$vehicu->cod_transp','$vehicu->cod_propie','$vehicu->usr_creaci','$vehicu->fec_creaci')";
                  $insercion = new Consulta($query,self::$cConexion,"R");
        }
      }
      if($vehicu->cod_tenedo){
        #inserta la relacion entre el tenedor y la transportadora si no existe previamente
        $query = "SELECT cod_transp FROM ".BASE_DATOS.".tab_transp_tercer WHERE cod_transp = '$vehicu->cod_transp' AND cod_tercer = '$vehicu->cod_tenedo'";
        $consulta = new Consulta($query, self::$cConexion);
        $existe = $consulta->ret_matriz("a");

        if(!$existe){
          $query = "INSERT INTO ".BASE_DATOS.".tab_transp_tercer
                         (cod_transp,cod_tercer,usr_creaci,fec_creaci)
                            VALUES ('$vehicu->cod_transp','$vehicu->cod_tenedo','$vehicu->usr_creaci','$vehicu->fec_creaci')";
                  $insercion = new Consulta($query,self::$cConexion,"R");
        }
      }

      if ($consulta = new Consulta("COMMIT", self::$cConexion)) {
            header('Location: ../../'.NOM_URL_APLICA.'/index.php?cod_servic=1120&menant=1120&window=central&resultado=1&operacion='.$operacion.'&opcion=123&placa='.$vehicu->num_placax);
            /*$mensaje = "<font color='#000000'>Se Modific&oacute; El Trayler: <b>$trayle->num_trayle</b> Exitosamente.<br></font>";
            $mensaje .= "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closed()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
            $mens = new mensajes();
            echo $mens->correcto2("INSERTAR TRAYLER", $mensaje);*/
      }else{
        header('Location: ../../'.NOM_URL_APLICA.'/index.php?cod_servic=1120&menant=1120&window=central&opcion=123&resultado=0');
        /*
        $mensaje = "<font color='#000000'>Ocurri&oacute; un Error inesperado al registrar el Trayler: <b>$tercer->num_trayle</b><br> verifique e intente nuevamente.<br>Si el error persiste informe a mesa de apoyo</font>";
        $mensaje .= "<br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
        $mens = new mensajes();
        echo $mens->error2("INSERTAR TRAYLER", $mensaje);*/
      }
    }else{
      header('Location: ../../'.NOM_URL_APLICA.'/index.php?cod_servic=1120&menant=1120&window=central&opcion=123&resultado=2');//cuando el conductor esta marcado como tal pero no registrado en la tabla de conductores
    }
  }
 

    public function inactivarVehiculo(){
      $placa = $_REQUEST['placa'];
      $transp = $_REQUEST['cod_tercer']; 

      $query = "UPDATE ".BASE_DATOS.".tab_transp_vehicu 
                   SET  
                       ind_estado = 0
                 WHERE num_placax = '$placa'
                   AND cod_transp = '$transp'";
  
      $consulta = new Consulta($query, self::$cConexion, "R");
      if ($consulta = new Consulta("COMMIT", self::$cConexion)) {
             $mensaje = "<font color='#000000'>Se Inactiv&oacute; El Veh&iacute;culo de placa: <b>$placa</b> Exitosamente.<br></font>";
              $mensaje .= "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closed()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
              $mens = new mensajes();
              echo $mens->correcto2("INACTIVAR VEHÍCULO", $mensaje);

      }else{
          $mensaje = "<font color='#000000'>Ocurri&oacute; un Error inesperado al inactivar el vehiculo de placa: <b>$placa</b><br> por favor, intente nuevamente.<br>Si el error persiste informe a mesa de apoyo</font>";
          $mensaje .= "<br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
          $mens = new mensajes();
          echo $mens->error2("INACTIVAR VEHÍCULO", $mensaje);

      }
    }

    public function activarVehiculo(){
      $placa = $_REQUEST['placa'];
      $transp = $_REQUEST['cod_tercer']; 
      
      $query = "UPDATE ".BASE_DATOS.".tab_transp_vehicu 
                   SET  
                       ind_estado = 1
                 WHERE num_placax = '$placa'
                   AND cod_transp = '$transp'";
      $consulta = new Consulta($query, self::$cConexion, "R");
      if ($consulta = new Consulta("COMMIT", self::$cConexion)) {
             $mensaje = "<font color='#000000'>Se Activ&oacute; El Veh&iacute;culo de placa: <b>$placa</b> Exitosamente.<br></font>";
              $mensaje .= "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closed()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
              $mens = new mensajes();
              echo $mens->correcto2("ACTIVAR VEHÍCULO", $mensaje);

      }else{
          $mensaje = "<font color='#000000'>Ocurri&oacute; un Error inesperado al actiar el vehiculo de placa: <b>$placa</b><br> por favor, intente nuevamente.<br>Si el error persiste informe a mesa de apoyo</font>";
          $mensaje .= "<br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
          $mens = new mensajes();
          echo $mens->error2("ACTIVAR VEHÍCULO", $mensaje);

      }
    }

    public function saveOpeGps(){
      $info = [];
      $usuario = $_SESSION['datos_usuario']['cod_usuari'];
      $query = "SELECT cod_operad FROM ".BASE_DATOS.".tab_trayle_opegps WHERE num_trayle = '".$_REQUEST['num_trayle']."' AND cod_operad = '".$_REQUEST['cod_opegps']."' ";
      $consulta = new Consulta($query, self::$cConexion);
      $existe = $consulta->ret_matriz("a");
      if(!$existe){
        $query = "INSERT INTO ".BASE_DATOS.".tab_trayle_opegps 
                  (num_trayle, cod_operad, usr_gpsxxx,
                   clv_gpsxxx, idx_gpsxxx, usr_creaci,
                   fec_creaci) VALUES 
                  ('".$_REQUEST['num_trayle']."','".$_REQUEST['cod_opegps']."','".$_REQUEST['usr_gpsxxx']."',
                   '".$_REQUEST['clv_gpsxxx']."','".$_REQUEST['idx_gpsxxx']."','".$usuario."',
                   NOW())";
        $consulta = new Consulta($query, self::$cConexion, "R"); 
        if($consulta = new Consulta("COMMIT", self::$cConexion)){
          $info['status'] = 1000;
          $info['msj'] = 'Se ha registrado el operador gps';
        }else{
          $info['status'] = 2000;
          $info['msj'] = 'No fue posible registrar el operador gps. Por favor comuniquese con mesa de apoyo';
        }
      }else{
        $info['status'] = 3000;
        $info['msj'] = 'El operador gps ya se encuentra asignado al remolque';
      }
      echo json_encode($info);
    }

    public function deteleOpeGps(){
      $info = [];
      $query = "DELETE FROM ".BASE_DATOS.".tab_trayle_opegps WHERE num_trayle = '".$_REQUEST['num_trayle']."' AND cod_operad = '".$_REQUEST['cod_opegps']."' ";
      $consulta = new Consulta($query, self::$cConexion, "R");
      if($consulta = new Consulta("COMMIT", self::$cConexion)){
        $info['status'] = 1000;
        $info['msj'] = 'Operador gps eliminado';
      }else{
        $info['status'] = 2000;
        $info['msj'] = 'No fue posible eliminar el operador gps. Por favor comuniquese con mesa de apoyo';
      }
      echo json_encode($info);
    }

  }
if($_REQUEST[Ajax] === 'on' )
  $_INFORM = new trayle();

?>
