<?php
/*! \file: inf_proyec_noveda.php
 *  \brief: Archivo que contiene las sentencias php para el informe de proyeccion de novedades(Informes > Gestion de operacion > Proyeccion de nov)
 *  \author: Ing. Carlos Nieto
 *  \author: carlos.nieto@intrared.net
 *  \version: 1.0
 *  \date: 17/06/2021
 *  \bug: 
 *  \warning: 
 */

class Informe_Proyeccion
  {

    var $conexion,
    $cod_aplica,
    $usuario;

    function __construct($co = null , $us = null, $ca = null)
    {
      /* ini_set('display_errors', 1); 
      ini_set('display_startup_errors', 1);
      error_reporting(E_ALL); */
      $this -> conexion = $co;
      $this -> usuario = $us;
      $this -> cod_aplica = $ca;
      $this -> Principal();
  
    }
    
    /*! funcion: Principal
 *  \brief: funcion que inicializa la vista
 *  \author: Ing. Carlos Nieto
 *  \author: carlos.nieto@intrared.net
 *  \version: 1.0
 *  \date: 17/06/2021
 *  \bug: 
 *  \warning: 
 */

    function Principal()
    {
      if( !isset( $_REQUEST['opcion'] ) )
        $this -> Filtros();
      else
        switch( $_REQUEST['opcion'] )
        { 
          default:
            $this -> Filtros();
          break;
        }
    }
    
    /*! funcion: Filtros
 *  \brief: Maqueta todo el html inicial y redirige al archivo JS info_proyec_noveda.js cuando se ejecuta el form
 *  \author: Ing. Carlos Nieto
 *  \author: carlos.nieto@intrared.net
 *  \version: 1.0
 *  \date: 17/06/2021
 *  \bug: 
 *  \warning: 
 */
    function Filtros()
    { 
      @include_once( '../'.DIR_APLICA_CENTRAL.'/lib/general/functions.inc' );
      @include_once( "../lib/ajax.inc" );
      @include_once( "../lib/general/constantes.inc" );
      @include_once( '../'.DIR_APLICA_CENTRAL.'/inform/class_despac_trans3.php' );


      $mTD = array("class"=>"cellInfo1", "width"=>"20%");
      $mAs = '<label style="color: red">* </label>';
      $cNull = array( array('', '-----') );
      $mTransp = $this -> getTransports();
      $tipServicio = $this -> getTipoServicio();

      if( sizeof($mTransp) != 1 ){
        $mTransp = array_merge($cNull, $mTransp);
        $tipServicio = array_merge($cNull, $tipServicio);
      }
  
      IncludeJS( 'jquery.js' );
      IncludeJS( 'jquery.blockUI.js' );
      IncludeJS( 'inf_proyec_noveda.js' );
      IncludeJS( 'functions.js' );
      IncludeJS( 'validator.js' );
      IncludeJS( 'jquery.multiselect.min.js', '../'.DIR_APLICA_CENTRAL.'/js/multiselect/' );
      IncludeJS( 'jquery.multiselect.filter.min.js', '../'.DIR_APLICA_CENTRAL.'/js/multiselect/' );
      
  
      $mHtml = new Formlib(2);
      
      $mHtml->SetCss("jquery");
      $mHtml->SetCss("informes");
      $mHtml->SetCss("validator");
      $mHtml->SetBody("<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/multiselect/jquery.multiselect.css' type='text/css'>\n");
      $mHtml->SetBody("<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/multiselect/jquery.multiselect.filter.css' type='text/css'>\n");
  
      $mHtml->CloseTable('tr');

      #Acordion
      $mHtml->OpenDiv("class:accordion");
        $mHtml->SetBody("<h3 style='padding:6px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>Informe Proyección de Novedades</b></h3>");
        $mHtml->OpenDiv("id:secID");
          $mHtml->SetBody('<form name="form_InfProyecNoveda" id="form_InfProyecNovedaID" action="" method="post">');
          $mHtml->OpenDiv("id:formID; class:Style2DIV");

            $mHtml->Table('tr');
              $mHtml->SetBody('<tr><th class="CellHead" colspan="4" style="text-align:left">Filtros Generales</th></tr>');
              $mHtml->Label( $mAs."Transportadoras: ", $mTD );
              $mHtml->Select2( $mTransp, array("name"=>"busq_transp","multiple"=>"multiple", "id"=>"busq_transp", "width"=>"30%", "class"=>"cellInfo1 multiSel") );
              $mHtml->Label( "Tipo se servicio: ", $mTD );
              $mHtml->Select2( $tipServicio, array("name"=>"tip_service","multiple"=>"multiple", "id"=>"tip_service", "width"=>"30%", "class"=>"cellInfo1 multiSel", "end"=>true) );
                                                                      
            $mHtml->CloseTable('tr');
            $mHtml->Hidden( array("name"=>"standa", "id"=>"standaID", "value"=>DIR_APLICA_CENTRAL) );
						$mHtml->Hidden( array("name"=>"window", "id"=>"windowID", "value"=>$_REQUEST['window']) );
            $mHtml->Hidden( array("name"=>"cod_servic", "id"=>"cod_servicID", "value"=>$_REQUEST['cod_servic']) );

            $mHtml->SetBody('<ul>');
            $mHtml->SetBody('<li><button  style="cursor:pointer; margin-left:50%; background:#346405; color:white;" type="submit">Generar</button></li>');
          $mHtml->SetBody('</ul>');
          $mHtml->CloseDiv();
          $mHtml->SetBody('</form>');
        $mHtml->CloseDiv();
      $mHtml->CloseDiv();


      echo $mHtml->MakeHtml();
  
    }
   
     /*! funcion: getTipoServicio
 *  \brief: consulta de DB de tipos de servicios par listarlos en el select
 *  \author: Ing. Carlos Nieto
 *  \author: carlos.nieto@intrared.net
 *  \version: 1.0
 *  \date: 17/06/2021
 *  \bug: 
 *  \warning: 
 */
    function getTipoServicio()
    {
      $mSql = "SELECT *  FROM `tab_genera_tipser` 
      WHERE `nom_tipser` LIKE 'OAL' 
      OR `nom_tipser` LIKE 'MA' 
      OR `nom_tipser` LIKE 'OAL/MA'
      ORDER BY nom_tipser DESC";
      
      $mConsult = new Consulta( $mSql, $this -> conexion );      
      $mReport = $mConsult -> ret_matriz( 'a' ) ;
      
      return $mReport;
    }
    

    /*! funcion: getTransports
 *  \brief: consulta de DB de transportadoras par listarlos en el select
 *  \author: Ing. Carlos Nieto
 *  \author: carlos.nieto@intrared.net
 *  \version: 1.0
 *  \date: 17/06/2021
 *  \bug: 
 *  \warning: 
 */
    function getTransports( $cod_transp = NULL )
    {    
        
     $mSql = "SELECT a.cod_tercer, CONCAT(a.cod_tercer,' - ', a.abr_tercer ) as abr_tercer, e.cod_tipser
                 FROM ".BASE_DATOS.".tab_tercer_tercer a,
                      ".BASE_DATOS.".tab_tercer_activi b,
                      ".BASE_DATOS.".tab_despac_vehige c,
                      ".BASE_DATOS.".tab_transp_tipser e
                WHERE a.cod_tercer = b.cod_tercer AND
                      a.cod_tercer = c.cod_transp AND
                      a.cod_tercer = e.cod_transp AND
                      e.num_consec = ( SELECT MAX( num_consec ) 
                                         FROM ".BASE_DATOS.".tab_transp_tipser
                                        WHERE cod_transp = a.cod_tercer AND
                                              ind_estado = '1'
                                      ) AND
                      b.cod_activi = ".COD_FILTRO_EMPTRA."
                      ";
      if( $cod_transp && $cod_transp != NULL )
      {
        $mSql .= " AND c.cod_transp = '". $cod_transp ."' ";
      }  
      $mSql .= "GROUP BY 1 
                ORDER BY 2 ASC ";
      

  
      $mConsult = new Consulta( $mSql, $this -> conexion );      
      $mReport = $mConsult -> ret_matriz( 'a' ) ;

      $mReturn = array();
      
      foreach( $mReport as $row )
      {
        if( $row['cod_tipser'] != '1' )
        {
          $mReturn[] = $row;
        }
      }
  
      return $mReturn;
    }
  }
  $proceso = new Informe_Proyeccion($_SESSION['conexion']);

?>