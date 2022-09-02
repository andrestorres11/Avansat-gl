 <?php
error_reporting(0);

class AjaxInsertDespacho
{
  var $conexion;
  public function __construct()
  {
    $_AJAX = $_REQUEST;
    include_once('../lib/bd/seguridad/aplica_filtro_perfil_lib.inc');
    include_once('../lib/bd/seguridad/aplica_filtro_usuari_lib.inc');
    include_once('../lib/ajax.inc');
    include_once('../lib/general/constantes.inc');
    include_once('../lib/general/functions.inc');
    $this->conexion = $AjaxConnection;
    $this->$_AJAX['option']( $_AJAX );
  }

  protected function getTransp( $_AJAX ){
    $mSql = "SELECT a.cod_tercer, UPPER( a.abr_tercer ) as abr_tercer
               FROM ".BASE_DATOS.".tab_tercer_tercer a,
                    ".BASE_DATOS.".tab_tercer_activi b
              WHERE a.cod_tercer = b.cod_tercer AND
                    b.cod_activi = ".$_AJAX['filter']." AND
                    CONCAT( a.cod_tercer ,' - ', UPPER( a.abr_tercer ) ) LIKE '%". $_AJAX['term'] ."%'
           ORDER BY 2";
		
		$consulta = new Consulta( $mSql, $this->conexion );
		$transpor = $consulta->ret_matriz();
    
    $data = array();
    for($i=0, $len = count($transpor); $i<$len; $i++){
       $data [] = '{"label":"'.$transpor[$i][0].' - '.$transpor[$i][1].'","value":"'. $transpor[$i][0].' - '.$transpor[$i][1].'", "id":"'. $transpor[$i][0].'"}'; 
    }
    echo '['.join(', ',$data).']';
    
  }
  
  protected function getNomDestin( $_AJAX ){
    $mSql = "SELECT a.cod_remdes,  
                    CONCAT ( UPPER (a.nom_remdes),'(',UPPER (b.nom_ciudad), ', ', UPPER (a.dir_remdes) ,')') AS nom_remdes,
                    UPPER (a.dir_remdes),
                    b.cod_ciudad
              FROM ".BASE_DATOS.".tab_genera_remdes a
        INNER JOIN ".BASE_DATOS.".tab_genera_ciudad b
                ON  a.cod_ciudad = b.cod_ciudad AND b.ind_estado = 1
             WHERE  a.ind_remdes = 2
                    AND a.ind_estado = 1 
                    AND a.cod_remdes != 0 
                    AND CONCAT( a.cod_remdes ,' - ', UPPER( a.nom_remdes ) ) LIKE '%". $_AJAX['term'] ."%'      
          GROUP BY  1
          ORDER BY  2";

		$consulta = new Consulta( $mSql, $this->conexion );
		$nomDestin = $consulta->ret_matriz();
    
    $data = array();
    for($i=0, $len = count($nomDestin); $i<$len; $i++){
       $data [] = '{"label":"'.$nomDestin[$i][0].' - '.$nomDestin[$i][1].'","value":"'. $nomDestin[$i][0].' - '.$nomDestin[$i][1].'", "id":"'. $nomDestin[$i][0].'", "dir":"'. $nomDestin[$i][2].'", "ciu":"'. $nomDestin[$i][3].'"}'; 
    }
    echo '['.join(', ',$data).']';
    
  }

  protected function getSitCar( $_AJAX ){
    $mSql = "SELECT a.cod_remdes,  
                    CONCAT( UPPER (replace(replace(a.nom_remdes,'\n',' '),'\r',' ')),'(',UPPER (replace(replace(b.nom_ciudad,'\n',' '),'\r',' ')), ', ', UPPER (replace(replace(a.dir_remdes,'\n',' '),'\r',' ')) ,')') AS nom_remdes
              FROM ".BASE_DATOS.".tab_genera_remdes a
        INNER JOIN ".BASE_DATOS.".tab_genera_ciudad b
                ON  a.cod_ciudad = b.cod_ciudad AND b.ind_estado = 1
             WHERE  a.ind_remdes = 1 
                    AND a.ind_estado = 1 
                    AND a.cod_remdes != 0 
                    AND CONCAT( a.cod_remdes ,' - ', UPPER( a.nom_remdes ) ) LIKE '%". $_AJAX['term'] ."%'   
          GROUP BY  1
          ORDER BY  2";

		$consulta = new Consulta( $mSql, $this->conexion );
		$sitCar = $consulta->ret_matriz();
    
    $data = array();
    for($i=0, $len = count($sitCar); $i<$len; $i++){
       $data [] = '{"label":"'.$sitCar[$i][0].' - '.$sitCar[$i][1].'","value":"'. $sitCar[$i][0].' - '.$sitCar[$i][1].'", "id":"'. $sitCar[$i][0].'"}'; 
    }
    echo '['.join(', ',$data).']';
    
  }
  
  private function getAgencias( $cod_transp, $cod_agenci = NULL )
  {
    $datos_usuario = $_SESSION['datos_usuario'];
    
    $query = "SELECT a.cod_agenci,a.nom_agenci
                FROM ".BASE_DATOS.".tab_genera_agenci a,
               		   ".BASE_DATOS.".tab_transp_agenci b
               WHERE a.cod_agenci = b.cod_agenci AND
                     b.cod_transp = '".$cod_transp."'";
    
    if( $datos_usuario["cod_perfil"] == "" )
    {
     $filtro = new Aplica_Filtro_Usuari( 1 ,COD_FILTRO_AGENCI, $datos_usuario["cod_usuari"] );
     if( $filtro->listar( $this->conexion ) )
     {
      $datos_filtro = $filtro->retornar();
      $query .= " AND a.cod_agenci = '".$datos_filtro['clv_filtro']."' ";
     }
    }
    else
    {
     $filtro = new Aplica_Filtro_Perfil( 1, COD_FILTRO_AGENCI, $datos_usuario["cod_perfil"] );
     if( $filtro->listar($this->conexion ) )
     {
      $datos_filtro = $filtro->retornar();
      $query .= " AND a.cod_agenci = '".$datos_filtro['clv_filtro']."' ";
     }
    }
    
    if( $cod_agenci != NULL )
    {
      $query .= " AND a.cod_agenci = '".$cod_agenci."' ";
    }
    
    $query .= " ORDER BY 2";
    
    //echo '<hr>'.$query;

    $consulta = new Consulta( $query, $this->conexion );
    return $consulta->ret_matriz();
  
  }
  
  private function getGenera( $cod_transp, $cod_genera = NULL )
  {
    $query = "SELECT a.cod_tercer, UPPER(a.abr_tercer) AS nom_tercer
                FROM ".BASE_DATOS.".tab_tercer_tercer a,
                     ".BASE_DATOS.".tab_tercer_activi b,
                     ".BASE_DATOS.".tab_transp_tercer c
               WHERE a.cod_tercer = b.cod_tercer AND
                     a.cod_tercer = c.cod_tercer AND
                     c.cod_transp = '".$cod_transp."' AND
                     b.cod_activi = ".COD_FILTRO_CLIENT."";
    if( $cod_genera != NULL )
    {
      $query .= " AND a.cod_tercer = '".$cod_genera."'";
    }
    $query .= " ORDER BY 2 ASC";
    
    $consulta = new Consulta( $query, $this->conexion );
    return $consulta->ret_matriz();
  }

  private function getEmpaqu(){
    $sql = "SELECT nom_empaqu, nom_empaqu FROM ".BASE_DATOS.".tab_genera_empaqu WHERE ind_activa = 1";
    $consulta = new Consulta( $sql, $this->conexion );
    return $consulta->ret_matriz();

  }
  
  private function getCenOpe( $cod_transp, $cod_cenope = NULL )
  {
    
    $query = "SELECT a.cod_cenope, a.nom_cenope
                FROM ".BASE_DATOS.".tab_genera_cenope a
               WHERE a.cod_transp = '".$cod_transp."'";
    
    if( $cod_cenope != NULL )
    {
      $query .= " AND a.cod_cenope = '".$cod_cenope."' ";
    }
    
    $query .= " ORDER BY 2";
    
    $consulta = new Consulta( $query, $this->conexion );
    return $consulta->ret_matriz();
  
  }
  
  private function getCenNot( $cod_transp, $cod_cenope = NULL )
  {
    
    $query = "SELECT a.cod_cennot, a.nom_cennot
                FROM ".BASE_DATOS.".tab_genera_cennot a
               WHERE a.cod_transp = '".$cod_transp."'";
    
    if( $cod_cenope != NULL )
    {
      $query .= " AND a.cod_cennot = '".$cod_cenope."' ";
    }
    
    $query .= " ORDER BY 2";
    
    $consulta = new Consulta( $query, $this->conexion );
    return $consulta->ret_matriz();
  
  }
  
  protected function viewConduc( $_AJAX )
  {
    
  }
  
  private function getOpegps( $cod_opegps = NULL )
  {
    $paramGPS = getParameOpeGPS($this->conexion);
    $parOpeSt = $paramGPS[0];
    $parOpePr = $paramGPS[1];
    $parCodPais = $paramGPS[2];
    $opegpsPropio = NULL;
    $opegpsStanda = NULL;

    if( $cod_opegps != NULL && $cod_opegps != '' )
    {
      $condi .= " AND a.cod_operad = '".$cod_opegps."' ";
    }

    if($parOpeSt){
      $query = "SELECT a.cod_operad, CONCAT(a.nom_operad, ' [INTEGRADOR ESTANDAR]') as 'nom_operad' 
             FROM ".BD_STANDA.".tab_genera_opegps a
             INNER JOIN ".BD_STANDA.".tab_opegps_paisxx b ON a.cod_operad = b.cod_operad AND b.cod_paisgl = $parCodPais
             WHERE a.ind_estado = '1'
             ".$condi."
         ORDER BY a.nom_operad ASC ";
      $consulta = new Consulta($query, $this->conexion);
      $opegpsStanda = $consulta->ret_matriz("a");
      
    }

    if($parOpePr){
      $query = "SELECT cod_operad,nom_operad
             FROM ".BASE_DATOS.".tab_genera_opegps
             WHERE ind_estado = '1'
             ".$condi."
         ORDER BY nom_operad ASC ";
      $consulta = new Consulta($query, $this->conexion);
      $opegpsPropio = $consulta->ret_matriz("a");
    }
    $operadores = SortMatrix(arrayMergeIgnoringNull($opegpsStanda,$opegpsPropio), 'nom_operad', 'ASC') ;
    return $operadores;
  
  }
  
  private function getInfoVehiculo( $cod_transp = NULL, $num_placax = NULL, $flag = NULL)
  { 
    $mQuery = "SELECT  a.num_placax,g.abr_tercer,g.num_telef1,h.abr_tercer,
                       h.num_telmov,b.nom_marcax,c.nom_lineax,d.nom_colorx,
                       e.nom_carroc,a.ano_modelo,a.ind_estado,a.fec_creaci,
                       a.num_config,g.cod_tercer,h.cod_tercer,k.cod_tercer,
                       k.abr_tercer,a.cod_opegps,a.usr_gpsxxx,a.clv_gpsxxx,
                       a.idx_gpsxxx
                  FROM ".BASE_DATOS.".tab_vehicu_vehicu a
            LEFT JOIN ".BASE_DATOS.".tab_genera_marcas b
                    ON a.cod_marcax = b.cod_marcax
            LEFT JOIN ".BASE_DATOS.".tab_vehige_carroc e
                    ON a.cod_carroc = e.cod_carroc
            LEFT JOIN ".BASE_DATOS.".tab_vehige_colore d
                    ON a.cod_colorx = d.cod_colorx
            LEFT JOIN ".BASE_DATOS.".tab_vehige_lineas c
                    ON a.cod_marcax = c.cod_marcax 
                   AND a.cod_lineax = c.cod_lineax
            LEFT JOIN ".BASE_DATOS.".tab_vehige_config i
                    ON a.num_config = i.num_config
            LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer g
                    ON a.cod_tenedo = g.cod_tercer
            LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer h
                    ON a.cod_conduc = h.cod_tercer";
                       if( $cod_transp != NULL )
                       {
                         $mQuery .= " INNER JOIN ".BASE_DATOS.".tab_transp_vehicu j 
                         ON a.num_placax = j.num_placax AND j.cod_transp = '".$cod_transp."'";
                       }
                       
           $mQuery .= " LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer k 
                       ON a.cod_propie = k.cod_tercer
                 WHERE 1=1";
    
    if( $cod_transp != NULL )
    {
      $mQuery .= " AND a.ind_estado = '1' ";
    }
    if( $num_placax != NULL )
    {
      $mQuery .= " AND a.num_placax = '".$num_placax."'";
    }
    $consulta = new Consulta( $mQuery, $this->conexion );
    if($flag != NULL){
      return $consulta->ret_matriz();
    }else{
      return $mQuery;
    }
  }
  
  public function LoadVehiculos( $_AJAX )
  {
    try{
          $_VEHICU = $this->getInfoVehiculo( $_AJAX['cod_transp'],NULL, false);
          $mHtml = new Formlib(2, "yes",TRUE);
          $mHtml->OpenDiv("id:tab_vehicu");
              $mHtml->Table("tr",array("class"=>"displayDIV2"));
                $mHtml->Label( "LISATDO DE VEHICULOS", array("colspan"=>sizeof($titulos['Nivel1']), "align"=>"rigth", "width"=>"25%", "class"=>"CellHead") );
              $mHtml->CloseTable('tr');
            $mHtml->OpenDiv("id:tabvehicu1");
              $mHtml->SetBody($this->getDinamiList($_VEHICU));
            $mHtml->CloseDiv();
          $mHtml->CloseDiv(); 

                $mHtml->SetBody('<style>
                        #tab_vehicu{
                border: 1px solid rgb(201, 201, 201);
                padding: 3px;
                width: 200%;
                min-height: 50px;
                border-radius: 5px;
                background-color: rgb(240, 240, 240);
              }
                      </style>');

          echo $mHtml->MakeHtml();
      } catch (Exception $e) {
        echo "error LoadVehiculos :".$e;
      }
  }

  /*! \fn: getDinamiList
   *  \brief: identifica el formulario correspondiete y lo pinta
   *  \author: Edward Serrano
   *  \date:  18/01/2017
   *  \date modified: dia/mes/aï¿½o
  */
  function getDinamiList($datos)
  { 
    try
    {   
      IncludeJS( '../js/dinamic_list.js' );
      IncludeJS( '../js/new_ajax.js' );
      echo "<link  href='../" . DIR_APLICA_CENTRAL . "/dinamic_list.php' type='script'>\n";
      echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/dinamic_list.css' type='text/css'>\n";
      echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";
      $_SESSION["queryXLS"] = $datos;

      if (!class_exists(DinamicList)) 
      {
          include_once("../" . DIR_APLICA_CENTRAL . "/lib/general/dinamic_list.inc");
      }
      $list = new DinamicList($this->conexion, $datos);
      $list->SetClose('no');
      $list->SetCreate("Nuevo Vehiculo", "onclick:newVehiculo()");
      $list->SetHeader("Placa", "field:a.num_placax; width:1%;type:link; onclick:getVehiculo($(this),".$_REQUEST['cod_transp'].",0)");
      $list->SetHeader("Tenedor", "field:g.abr_tercer; width:1%");
      $list->SetHeader("Telefono", "field:g.num_telef1; width:1%");
      $list->SetHeader("Conductor", "field:h.abr_tercer");
      $list->SetHeader("Celular", "field:h.num_telmov");
      $list->SetHeader("marca", "field:b.nom_marcax");
      $list->SetHeader("linea", "field:c.nom_lineax");
      $list->SetHeader("color", "field:d.nom_colorx");
      $list->SetHeader("carroceria", "field:e.nom_carroc");
      $list->SetHeader("modelo", "field:a.ano_modelo");
      $list->SetHidden("num_placax", "num_placax");
      $list->SetHidden("abr_tercer", "abr_tercer");
      $list->SetHidden("num_telef1", "num_telef1");
      $list->SetHidden("abr_tercer", "abr_tercer");
      $list->SetHidden("num_telmov", "num_telmov");
      $list->SetHidden("nom_marcax", "nom_marcax");
      $list->SetHidden("nom_lineax", "nom_lineax");
      $list->SetHidden("nom_colorx", "nom_colorx");
      $list->SetHidden("nom_carroc", "nom_carroc");
      $list->SetHidden("ano_modelo", "ano_modelo");
      $list->Display($this->conexion);

      $_SESSION["DINAMIC_LIST"] = $list;

      $Html = $list->GetHtml();

      if($_REQUEST["Ajax"] === 'on' )
      {
        echo $Html;
      }
      else
      {
        return $Html;
      }
    } catch (Exception $e) {
      echo "error getDinamiList :".$e;
    }
  }
  
  protected function MainForm( $_AJAX )
  {
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";
    $mHtml  = '
    <script>
      $(function() {
        $( ".accordion" ).accordion({
          collapsible : true,
          /*active : false,*/
          heightStyle : "content",
          icons: { "header" : "ui-icon-circle-arrow-e", "activeHeader": "ui-icon-circle-arrow-s" }
        });  
        $(".date").datepicker();
        $(".date").datepicker("option", {
            dateFormat: "yy-mm-dd", 
            minDate: new Date('.(date('Y')).','. (date('m')-1) .','.(date('d')).')
        });
        
        /*$( ".time" ).timepicker();
        
        $.mask.definitions["A"]="[12]";
        $.mask.definitions["M"]="[01]";
        $.mask.definitions["D"]="[0123]";

        $.mask.definitions["H"]="[012]";
        $.mask.definitions["N"]="[012345]";
        $.mask.definitions["n"]="[0123456789]";

        $( ".date" ).mask("Annn-Mn-Dn");
        $( ".time" ).mask("Hn:Nn:Nn");*/

      });
    </script>';
    $mHtml  .= '<center><table width="100%" cellspacing="2px" cellpadding="0">';
    
    $mHtml .= '<tr>';
    $mHtml .= '<td colspan="4" style=" text-align:center;border-bottom:1px solid #000000; font-family:Trebuchet MS, Verdana, Arial; font-size:15px; font-weight:bold; padding-bottom:5px; color: #285C00;" >Creaci&oacute;n de Despacho Para <i>'.utf8_decode($_AJAX['nom_transp']).'</i></td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '</table></center>';
    
    $mHtml .= '<form id="MainFormID" action="index.php" method="post" name="MainForm">';
    #pregunto si tiene configurados cargue y descargue
    $TransTipser = $this -> getTransTipser(  $_AJAX['cod_transp'] );

 
    $ind_segcarg = $TransTipser[0]['ind_segcar'];
    $ind_segdeca = $TransTipser[0]['ind_segdes'];

    $DAT_BASICO = $this->GetFormDatosBasicos( $_AJAX );
    $DAT_CARGUE = $this->GetFormDatosCarguex( $_AJAX );
    //$DAT_OPERAC = $this->GetFormDatosOperaci( $_AJAX );
    $DAT_SEGURO = $this->GetFormDatosSeguros( $_AJAX );
    $DAT_VEHICU = $this->GetFormDatosVehicxx( $_AJAX );
    $DAT_DESTIN = $this->GetFormDatosDestinx( $_AJAX );
    $DAT_ADICIO = $this->GetFormDatosAdicion( $_AJAX );
    $mHtml .= $this->GenerateDynamicDiv( 'Datos B&aacute;sicos del Despacho', $DAT_BASICO,180, 'datbasID', 'accbasID' );
    if($ind_segdeca == 1){
      $mHtml .= $this->GenerateDynamicDiv( 'Datos del Cargue', $DAT_CARGUE,100 , 'datcarID', 'acccarID' );
    }
    //$mHtml .= $this->GenerateDynamicDiv( 'Operaciones', $DAT_OPERAC,100 , 'datopeID', 'accopeID' );
    $mHtml .= $this->GenerateDynamicDiv( 'GPS y Seguros del Despacho', $DAT_SEGURO, 160 , 'datsegID', 'accsegID');
    $mHtml .= $this->GenerateDynamicDiv( 'Recursos del Despacho', $DAT_VEHICU,190 , 'datvehID', 'accvehID');
    if($ind_segdeca == 1){
      $mHtml .= $this->GenerateDynamicDiv( 'Datos de Descargue y Destinatarios', $DAT_DESTIN, 130  , 'datdesID', 'accdesID');
    }
    $mHtml .= $this->GenerateDynamicDiv( 'Informaci&oacute;n Adicional del Despcho', $DAT_ADICIO, 100  , 'AdidesID', 'accdesID');

    $mHtml .= '<br><input class="crmButton small save" type="button" id="InsertID" value="Aceptar" onclick="InsertDespac();"/>';
    $mHtml .= '</form>';
    
    echo $mHtml; 
  }

  private function getTransTipser( $mCodTransp = NULL)
  {
     $mSql = "  SELECT  a.ind_segcar, a.ind_segtra,  a.ind_segdes
                      FROM ".BASE_DATOS.".tab_transp_tipser a
                      WHERE num_consec = (
                                                SELECT MAX(c.num_consec) AS num_consec
                                                  FROM ".BASE_DATOS.".tab_transp_tipser c 
                                                 WHERE c.cod_transp = '".$mCodTransp."'
                                             
                                         ) AND
                            cod_transp = '".$mCodTransp."'   ";  

      $mConsult = new Consulta( $mSql, $this->conexion );
      return $mResult = $mConsult -> ret_matrix('a');
  }
  
  private function SetDestinos( $_AJAX )
  {    
    $query = "SELECT a.cod_ciudad,CONCAT(a.abr_ciudad,' (',LEFT(d.abr_depart,4),') - ',LEFT(e.nom_paisxx,3))
                FROM ".BASE_DATOS.".tab_genera_ciudad a,
                     ".BASE_DATOS.".tab_genera_rutasx b,
                     ".BASE_DATOS.".tab_genera_ruttra c,
                     ".BASE_DATOS.".tab_genera_depart d,
                     ".BASE_DATOS.".tab_genera_paises e
               WHERE a.cod_ciudad = b.cod_ciudes AND
                     b.cod_depdes = d.cod_depart AND
                     b.cod_paides = d.cod_paisxx AND
                     d.cod_paisxx = e.cod_paisxx AND
                     b.cod_ciuori = '".$_AJAX['cod_ciuori']."' AND
                     b.cod_rutasx = c.cod_rutasx AND
                     c.cod_transp = '".$_AJAX['cod_transp']."' AND
                     b.ind_estado = '".COD_ESTADO_ACTIVO."'
            GROUP BY 1 ORDER BY 2";

    $consulta = new Consulta($query, $this->conexion);
    $destino = $consulta->ret_matriz();

    $mHtml = '';
    $mHtml .= '<option value="">- Seleccione -</option>'; 
    foreach( $destino as $row )
    {
      $mHtml .= '<option value="'.$row[0].'">'.$row[1].'</option>'; 
    }
    echo $mHtml;
  }
  
  private function getMarcas( $cod_marcax = NULL )
  {
    
    $query = "SELECT cod_marcax, nom_marcax
					  FROM ".BASE_DATOS.".tab_genera_marcas ";
			if( $cod_marcax != NULL )
			{
				$query .= " WHERE cod_marcax = '".$cod_marcax."' ";
			}
			
			$query .= " ORDER BY 2 ";

    $consulta = new Consulta( $query, $this->conexion );
    return $consulta->ret_matriz();
  
  }
  
  private function getColores( $cod_colorx = NULL )
  {
    
    $query = "SELECT cod_colorx, nom_colorx 
                FROM ".BASE_DATOS.".tab_vehige_colore ";

    if( $cod_colorx != NULL )
      $query .= " WHERE cod_colorx = '".$cod_colorx."' ";

    $query .= " ORDER BY 2 ";

    $consulta = new Consulta( $query, $this->conexion );
    return $consulta->ret_matriz();
  }
  
  private function getCarroc( $cod_carroc = NULL )
  {
    
    $query = "SELECT cod_carroc, nom_carroc
						  FROM ".BASE_DATOS.".tab_vehige_carroc ";
				
    if( $cod_carroc != NULL )
      $query .= " WHERE cod_carroc = '".$cod_carroc."'";
    
    $query .= " ORDER BY 2 ";

    $consulta = new Consulta( $query, $this->conexion );
    return $consulta->ret_matriz();
  }
  private function getAsegur( $cod_tercer = NULL )
  {
    
    $query = "SELECT a.cod_tercer,a.abr_tercer
  		      FROM ".BASE_DATOS.".tab_tercer_tercer a,
                 ".BASE_DATOS.".tab_tercer_activi b
  		     WHERE a.cod_tercer = b.cod_tercer AND
                 b.cod_activi = 7 ";
    if( $cod_tercer != NULL )
    {
      $query .= "AND a.cod_tercer ='".$cod_tercer."' ";
    }

    $consulta = new Consulta( $query, $this->conexion );
    return $consulta->ret_matriz();
  }
  
  private function getConfig( $cod_config = NULL )
  {
    $query = "SELECT a.num_config,a.num_config
						  FROM ".BASE_DATOS.".tab_vehige_config a
                          WHERE a.ind_estado = '1'";
								
    if( $cod_config != NULL )
      $query .= " AND a.num_config = '".$cod_config."' ";
				
    $query .= " ORDER BY 2 ";

    $consulta = new Consulta( $query, $this->conexion );
    return $consulta->ret_matriz();
  }
  
  function getRemolque( $cod_transp = NULL, $num_remolq = NULL )
  {
    $query = "SELECT a.num_trayle, a.num_trayle
						  FROM ".BASE_DATOS.".tab_vehige_trayle a,
						       ".BASE_DATOS.".tab_transp_trayle b  
					      WHERE a.num_trayle = b.num_trayle ";
						  
    if( $num_remolq != NULL )
      $query .= " AND a.num_trayle = '".$num_remolq."' ";
    if( $cod_transp != NULL )	
      $query .= " AND b.cod_transp = '".$cod_transp."' ";
   
    $consulta = new Consulta( $query, $this->conexion );
    return $consulta->ret_matriz();
  }
  
  protected function FormnewVehiculo( $_AJAX )
  {
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";
    $marcas = $this->getMarcas();
    $colore = $this->getColores();
    $carroc = $this->getCarroc();
    $config = $this->getConfig();
    
    $remolq = $this->getRemolque( $_AJAX['cod_transp'] );
    $mHtml  = '
    <script>
      $(function() {
        $(".date").datepicker();
         $(".date").datepicker("option", {
            dateFormat: "yy-mm-dd", 
            minDate: new Date('.(date('Y')).','. (date('m')-1) .','.(date('d')).')
        });

       /* $.mask.definitions["A"]="[12]";
        $.mask.definitions["M"]="[01]";
        $.mask.definitions["D"]="[0123]";

        $.mask.definitions["H"]="[012]";
        $.mask.definitions["N"]="[012345]";
        $.mask.definitions["n"]="[0123456789]";

        $( ".date" ).mask("Annn-Mn-Dn");*/

      });
    </script>';
    $mHtml .= '<div align="center" style="background-color: #f0f0f0; border: 1px solid #c9c9c9; padding: 5px; width: 99%; min-height: 100px; -moz-border-radius: 5px 5px 5px 5px; -webkit-border-radius: 5px 5px 5px 5px; border-top-left-radius: 5px; border-top-right-radius: 5px; border-bottom-right-radius: 5px; border-bottom-left-radius: 5px; color:#000000;" >';
    $mHtml .= '<center><table width="100%" cellspacing="2px" cellpadding="0">';
    
    $mHtml .= '<tr>';
    $mHtml .= '<td colspan="4" style="text-align:center;border-bottom:1px solid #000000; font-family:Trebuchet MS, Verdana, Arial; font-size:15px; font-weight:bold; padding-bottom:5px; color: #285C00;" >Creaci&oacute;n de Veh&iacute;culo</td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '<tr>';
    $mHtml .= '<td colspan="4" style="text-align:left;border-bottom:1px solid #000000; font-family:Trebuchet MS, Verdana, Arial; font-size:11px; font-weight:bold; padding-bottom:5px; color: #000000;" >Datos del Veh&iacute;culo</td>';
    $mHtml .= '</tr>';
     
    $mHtml .= '<tr>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">* Placa:&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr"><input name="nue_placax" id="nue_placaxID" type="text" maxlength="6" size="9" onfocus="this.className=\'campo_texto_on\'" onchange="ValidateVehiculo( this );this.className=\'campo_texto\'" /></td>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">* Marca:&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr">'.$this->GenerateSelect( $marcas, 'cod_marcax', NULL, 'onchange="SetLineas();"' ).'</td>';
    $mHtml .= '</tr>';
      
    $mHtml .= '<tr>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">* L&iacute;nea:&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr">'.$this->GenerateSelect( NULL, 'cod_lineax', NULL, NULL ).'</td>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">* Color:&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr">'.$this->GenerateSelect( $colore, 'cod_colorx', NULL, NULL ).'</td>';
    $mHtml .= '</tr>';
       
    $mHtml .= '<tr>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">* Carrocer&iacute;a:&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr">'.$this->GenerateSelect( $carroc, 'cod_carroc', NULL, NULL ).'</td>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">* Configuraci&oacute;n:&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr">'.$this->GenerateSelect( $config, 'cod_config', NULL, NULL ).'</td>';
    $mHtml .= '</tr>';
       
    $mHtml .= '<tr>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">* Capacidad(Tn):&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr"><input type="text" name="val_capaci" onkeypress="return NumericInput( event );" id="val_capaciID" size="10" maxlength="3" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">* Modelo:&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr"><input type="text" name="num_modelo" onkeypress="return NumericInput( event );" id="num_modeloID" size="10" maxlength="4" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
    $mHtml .= '</tr>';
        
    $mHtml .= '<tr>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">* SOAT:&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr"><input type="text" name="num_soatxx" id="num_soatxxID" size="30" +onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr">&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '</tr>';
         
    $mHtml .= '<tr>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">* Aseguradora:&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr"><input type="text" name="nom_asesoa" id="nom_asesoaID" size="50" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">* Fecha Vencimiento:&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr"><input type="text" name="fec_vencim" id="fec_vencimID" class="date" size="20" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
    $mHtml .= '</tr>';

    
    
    /********************/
    
    /*    CONDUCTOR     */
    $mHtml .= '<tr>';
    $mHtml .= '<td colspan="4" style="text-align:left; border-bottom:1px solid #000000; border-top:1px solid #000000; font-family:Trebuchet MS, Verdana, Arial; font-size:11px; font-weight:bold; padding-bottom:5px; padding-top:3px; color: #000000;" >Datos del Conductor</td>';
    $mHtml .= '</tr>';
    
    $query = "SELECT cod_tipdoc, nom_tipdoc 
                FROM ".BASE_DATOS.".tab_genera_tipdoc ";
    
    $query .= " WHERE cod_tipdoc != 'N'";
    
    $consulta = new Consulta($query, $this->conexion);  
    $tipdoc  = $consulta->ret_matriz();
    
    $mHtml .= '<tr>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">* Documento:&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr"><input type="text" name="num_doccon" onkeypress="return NumericInput( event );" onchange="verifyTercero( this.value, \'num_divconID\', \'con\'); verifyConduc( this.value, \'num_divconID\', \'con\');" onkeyup="$(\'#num_divconID\').val( GenerateDV( this.value ) );" id="num_docconID" size="20" maxlength="11" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /> - <input type="text" name="num_divcon" readonly id="num_divconID" size="3" maxlength="1" /></td>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">* Tipo Documento:&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr">'.$this->GenerateSelect( $tipdoc, 'tip_doccon', NULL, NULL ).'</td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '<tr>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">* Nombres:&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr"><input type="text" name="nom_tercon" id="nom_terconID" size="50" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">* Apellidos:&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr"><input type="text" name="pae_tercon" id="ape_terconID" size="50" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
    $mHtml .= '</tr>';


    $mHtml .= '<tr>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">* Celular:&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr"><input type="text" name="cel_tercon" onkeypress="return NumericInput( event );" id="cel_terconID" size="25" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">* E-mail:&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr"><input type="text" name="ema_tercon" id="ema_terconID" size="45" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
    $mHtml .= '</tr>';
    
   
    $mHtml .= '<tr>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">* No. Licencia:&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr"><input type="text" name="lic_conduc" id="lic_conducID" size="45" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">* Fecha Vencimiento:&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr"><input type="text" name="fec_venlic" id="fec_venlicID" class="date" size="20" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
    $mHtml .= '</tr>';

    $mHtml .= "<tr>";
    $mHtml .= '<td align="right" width="20%" class="label-tr">Propietario</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr"> <input type="checkBox" value="1" onClick="propietario()" name="con_propie" id="con_propieID"> </td>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">Poseedor</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr"> <input type="checkBox" value="1" onClick="poseedor()" name="con_poseed" id="con_poseedID"> </td>';
    $mHtml .= "</tr>";
    
    
    /********************/
    /*   PROPIETARIO    */
    $mHtml .= '<tr id="propietario">';
    $mHtml .= '<td colspan="4" style="text-align:left; border-bottom:1px solid #000000; border-top:1px solid #000000; font-family:Trebuchet MS, Verdana, Arial; font-size:11px; font-weight:bold; padding-bottom:5px; padding-top:3px; color: #000000;" >Datos del Propietario</td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '<tr id="dataPropietario">';
    $mHtml .= '<td align="right" width="20%" class="label-tr">JUR&Iacute;DICA&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr"><input type="radio" value="N" name="tip_perpro" onclick="setFormTercero(\'N\', \'dat_perproID\',\'pro\')"></td>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">NATURAL&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr"><input type="radio" value="J" name="tip_perpro" onclick="setFormTercero(\'J\', \'dat_perproID\',\'pro\')"></td>';
    $mHtml .= '</tr>';
     
    $mHtml .= '<tr>';
    $mHtml .= '<td colspan="4" style="text-align:left; font-family:Trebuchet MS, Verdana, Arial; font-size:11px; font-weight:bold; padding-bottom:5px; padding-top:3px; color: #000000;" ><div id="dat_perproID"></div></td>';
    $mHtml .= '</tr>';
    
    /********************/
    
    /*     POSEEDOR     */
    $mHtml .= '<tr id="poseedor">';
    $mHtml .= '<td colspan="4" style="text-align:left; border-bottom:1px solid #000000; border-top:1px solid #000000; font-family:Trebuchet MS, Verdana, Arial; font-size:11px; font-weight:bold; padding-bottom:5px; padding-top:3px; color: #000000;" >Datos del Poseedor</td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '<tr id="dataPoseedor">';
    $mHtml .= '<td align="right" width="20%" class="label-tr">JUR&Iacute;DICA&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr"><input type="radio"  value="N" name="tip_perten" name="tip_pertenID" onclick="setFormTercero(\'N\', \'dat_pertenID\',\'ten\')"></td>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">NATURAL&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr"><input type="radio"  value="J" name="tip_perten" name="tip_pertenID" onclick="setFormTercero(\'J\', \'dat_pertenID\',\'ten\')"></td>';
    $mHtml .= '</tr>';
     
    $mHtml .= '<tr>';
    $mHtml .= '<td colspan="4" style="text-align:left; font-family:Trebuchet MS, Verdana, Arial; font-size:11px; font-weight:bold; padding-bottom:5px; padding-top:3px; color: #000000;" ><div id="dat_pertenID"></div></td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '</table></center>';
    
    $mHtml .= '<form id="MainFormID" action="index.php" method="post" name="MainForm">';
    
    $mHtml .= '<br><input class="crmButton small save" type="button" id="InsertID" value="Aceptar" onclick="InsertVehiculo();"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="crmButton small save" type="button" id="CancelID" value="Cancelar" onclick="PopupVehiculos();"/>';
    $mHtml .= '</form>';
     
    $mHtml .= '</div>';
    echo $mHtml;
  }
  
  protected function SaveNewConductor( $_AJAX )
  {
    $consulta = new Consulta("SELECT NOW()", $this->conexion, "BR");
    
    if( !$this->VerifyTercerExist( $_AJAX['num_doccon'] ) )
    {
      
        $mInsert = "INSERT INTO ".BASE_DATOS.".tab_tercer_tercer
                              ( cod_tercer,	num_verifi, cod_tipdoc, nom_apell1,
                                nom_tercer, abr_tercer, dir_domici, num_telmov, 
                                dir_emailx, cod_estado, obs_tercer, usr_creaci, 
                                fec_creaci )
                        VALUES( '".$_AJAX['num_doccon']."', '".$_AJAX['num_divcon']."', '".$_AJAX['tip_doccon']."', '".$_AJAX['ape_tercon']."',
                                '".$_AJAX['nom_tercon']."', '".( $_AJAX['ape_tercon'].' '.$_AJAX['nom_tercon'] )."', 'N/A', '".$_AJAX['cel_tercon']."', '".$_AJAX['ema_tercon']."',
                                '1', 'Tercero creado por el módulo de despachos', '".$_SESSION['datos_usuario']['cod_usuari']."', NOW() )";
      // echo "<hr>".$mInsert;
      $consulta = new Consulta( $mInsert, $this->conexion, "R" );
    }
    else
    {
      $mUpdate = "UPDATE ".BASE_DATOS.".tab_tercer_tercer 
                     SET num_verifi = '".$_AJAX['num_divcon']."',
                         cod_tipdoc = '".$_AJAX['tip_doccon']."',
                         nom_apell1 = '".$_AJAX['ape_tercon']."',
                         nom_tercer = '".$_AJAX['nom_tercon']."',
                         abr_tercer = '".( $_AJAX['ape_tercon'].' '.$_AJAX['nom_tercon'] )."',
                         num_telmov = '".$_AJAX['cel_tercon']."',
                         dir_emailx = '".$_AJAX['ema_tercon']."',
                         cod_estado = '1', 
                         obs_aproba = 'Tercero activado por el módulo de despachos',
                         usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
                         fec_modifi = NOW()
                   WHERE cod_tercer = '".$_AJAX['num_doccon']."'";
      // echo "<hr>".$mUpdate;
      $consulta = new Consulta( $mUpdate, $this->conexion, "R" );
    }
    
    if( !$this->VerifyTercerConduc( $_AJAX['num_doccon'] ) )
    {
      
        $mInsert = "INSERT INTO ".BASE_DATOS.".tab_tercer_conduc
                              ( cod_tercer,	cod_tipsex, num_licenc, fec_venlic,
                                obs_conduc, usr_creaci, fec_creaci )
                        VALUES( '".$_AJAX['num_doccon']."', '1', '".$_AJAX['lic_conduc']."', '".$_AJAX['fec_venlic']."',
                                'Conductor creado por el módulo de despachos', '".$_SESSION['datos_usuario']['cod_usuari']."', NOW() )";
      // echo "<hr>".$mInsert;
      $consulta = new Consulta( $mInsert, $this->conexion, "R" );
    }
    else
    {
      $mUpdate = "UPDATE ".BASE_DATOS.".tab_tercer_conduc SET
                         num_licenc = '".$_AJAX['lic_conduc']."',
                         fec_venlic = '".$_AJAX['fec_venlic']."',
                         usr_creaci = '".$_SESSION['datos_usuario']['cod_usuari']."',
                         fec_creaci = NOW()
                   WHERE cod_tercer = '".$_AJAX['num_doccon']."'";
      // echo "<hr>".$mInsert;
      $consulta = new Consulta( $mUpdate, $this->conexion, "R" );
    }

    
    if( !$this->VerifyTercerTransp( $_AJAX['num_doccon'], $_AJAX['transp'] ) )
    {
      $mInsert = "INSERT INTO ".BASE_DATOS.".tab_transp_tercer
                              ( cod_transp, cod_tercer, usr_creaci, fec_creaci )
                        VALUES( '".$_AJAX['transp']."', '".$_AJAX['num_doccon']."', '".$_SESSION['datos_usuario']['cod_usuari']."' , NOW() )";             
      // echo "<hr>".$mInsert;                  
      $consulta = new Consulta( $mInsert, $this->conexion, "R" );
    }
    
    if( !$this->VerifyTercerActivi( $_AJAX['num_doccon'], COD_FILTRO_CONDUC ) )
    {
      $mInsert = "INSERT INTO ".BASE_DATOS.".tab_tercer_activi
                              ( cod_tercer, cod_activi )
                        VALUES( '".$_AJAX['num_doccon']."', '".COD_FILTRO_CONDUC."' )";
      
      // echo "<hr>".$mInsert;                  
      $consulta = new Consulta( $mInsert, $this->conexion, "R" );
    }
    $insercion = new Consulta( "COMMIT" , $this->conexion );
    
    echo $_AJAX['ape_tercon'].' '.$_AJAX['nom_tercon'];
  }
  
  protected function SaveNewVehiculo( $_AJAX )
  {
    /*echo "<pre>";
    print_r( $_AJAX );
    echo "</pre>";
    die;*/
    $consulta = new Consulta("SELECT NOW()", $this->conexion, "BR");
    
    /*  INSERCION PROPIETARIO  */
    if($_AJAX["con_propie"] != 1){
      if( !$this->VerifyTercerExist( $_AJAX['num_docpro'] ) )
      {
        if( $_AJAX['des_perpro'] == 'N' )
        {
          $mInsert = "INSERT INTO ".BASE_DATOS.".tab_tercer_tercer
                                ( cod_tercer,	num_verifi, cod_tipdoc, nom_tercer, 
                                  abr_tercer, dir_domici, num_telmov, dir_emailx, 
                                  cod_estado, obs_tercer, usr_creaci, fec_creaci )
                          VALUES( '".$_AJAX['num_docpro']."', '".$_AJAX['num_divpro']."', '".$_AJAX['tip_docpro']."', '".$_AJAX['nom_terpro']."',
                                  '".$_AJAX['nom_terpro']."', 'N/A', '".$_AJAX['cel_terpro']."', '".$_AJAX['ema_terpro']."',
                                  '1', 'Tercero creado por el módulo de despachos', '".$_SESSION['datos_usuario']['cod_usuari']."', NOW() )";
        }
        else
        {
          $mInsert = "INSERT INTO ".BASE_DATOS.".tab_tercer_tercer
                                ( cod_tercer,	num_verifi, cod_tipdoc, nom_apell1,
                                  nom_tercer, abr_tercer, dir_domici, num_telmov, 
                                  dir_emailx, cod_estado, obs_tercer, usr_creaci, 
                                  fec_creaci )
                          VALUES( '".$_AJAX['num_docpro']."', '".$_AJAX['num_divpro']."', '".$_AJAX['tip_docpro']."', '".$_AJAX['ape_terpro']."',
                                  '".$_AJAX['nom_terpro']."', '".( $_AJAX['ape_terpro'].' '.$_AJAX['nom_terpro'] )."', 'N/A', '".$_AJAX['cel_terpro']."', '".$_AJAX['ema_terpro']."',
                                  '1', 'Tercero creado por el módulo de despachos', '".$_SESSION['datos_usuario']['cod_usuari']."', NOW() )";
        }
         // echo "<hr>".$mInsert;
        $consulta = new Consulta( $mInsert, $this->conexion, "R" );
      }
      else
      {
        $mUpdate = "UPDATE ".BASE_DATOS.".tab_tercer_tercer 
                       SET cod_estado = '1', 
                           obs_aproba = 'Tercero activado por el módulo de despachos',
                           usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
                           fec_modifi = NOW()
                     WHERE cod_tercer = '".$_AJAX['num_docpro']."'";
         // echo "<hr>".$mUpdate;
        $consulta = new Consulta( $mUpdate, $this->conexion, "R" );
      }
      
      if( !$this->VerifyTercerTransp( $_AJAX['num_docpro'], $_AJAX['transp'] ) )
      {
        $mInsert = "INSERT INTO ".BASE_DATOS.".tab_transp_tercer
                                ( cod_transp, cod_tercer, usr_creaci, fec_creaci )
                          VALUES( '".$_AJAX['transp']."', '".$_AJAX['num_docpro']."', '".$_SESSION['datos_usuario']['cod_usuari']."' , NOW() )";             
         // echo "<hr>".$mInsert;                  
        $consulta = new Consulta( $mInsert, $this->conexion, "R" );
      }
      
      if( !$this->VerifyTercerActivi( $_AJAX['num_docpro'], COD_FILTRO_PROPIE ) )
      {
        $mInsert = "INSERT INTO ".BASE_DATOS.".tab_tercer_activi
                                ( cod_tercer, cod_activi )
                          VALUES( '".$_AJAX['num_docpro']."', '".COD_FILTRO_PROPIE."' )";
        
         // echo "<hr>".$mInsert;                  
        $consulta = new Consulta( $mInsert, $this->conexion, "R" );
      }
    }
    
    /*  INSERCION POSEEDOR  */
    if($_AJAX["con_poseed"] != 1){
      echo "poseedor";
      if( !$this->VerifyTercerExist( $_AJAX['num_docten'] ) )
      {
        if( $_AJAX['des_perten'] == 'N' )
        {
          $mInsert = "INSERT INTO ".BASE_DATOS.".tab_tercer_tercer
                                ( cod_tercer,	num_verifi, cod_tipdoc, nom_tercer, 
                                  abr_tercer, dir_domici, num_telmov, dir_emailx, 
                                  cod_estado, obs_tercer, usr_creaci, fec_creaci )
                          VALUES( '".$_AJAX['num_docten']."', '".$_AJAX['num_divten']."', '".$_AJAX['tip_docten']."', '".$_AJAX['nom_terten']."',
                                  '".$_AJAX['nom_terten']."', 'N/A', '".$_AJAX['cel_terten']."', '".$_AJAX['ema_terten']."',
                                  '1', 'Tercero creado por el módulo de despachos', '".$_SESSION['datos_usuario']['cod_usuari']."', NOW() )";
        }
        else
        {
          $mInsert = "INSERT INTO ".BASE_DATOS.".tab_tercer_tercer
                                ( cod_tercer,	num_verifi, cod_tipdoc, nom_apell1,
                                  nom_tercer, abr_tercer, dir_domici, num_telmov, 
                                  dir_emailx, cod_estado, obs_tercer, usr_creaci, 
                                  fec_creaci )
                          VALUES( '".$_AJAX['num_docten']."', '".$_AJAX['num_divten']."', '".$_AJAX['tip_docten']."', '".$_AJAX['ape_terten']."',
                                  '".$_AJAX['nom_terten']."', '".( $_AJAX['ape_terten'].' '.$_AJAX['nom_terten'] )."', 'N/A', '".$_AJAX['cel_terten']."', '".$_AJAX['ema_terten']."',
                                  '1', 'Tercero creado por el módulo de despachos', '".$_SESSION['datos_usuario']['cod_usuari']."', NOW() )";
        }
         // echo "<hr>".$mInsert;
        $consulta = new Consulta( $mInsert, $this->conexion, "R" );
      }
      else
      {
        $mUpdate = "UPDATE ".BASE_DATOS.".tab_tercer_tercer 
                       SET cod_estado = '1', 
                           obs_aproba = 'Tercero activado por el módulo de despachos',
                           usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
                           fec_modifi = NOW()
                     WHERE cod_tercer = '".$_AJAX['num_docten']."'";
         // echo "<hr>".$mUpdate;
        $consulta = new Consulta( $mUpdate, $this->conexion, "R" );
      }
      
      if( !$this->VerifyTercerTransp( $_AJAX['num_docten'], $_AJAX['transp'] ) )
      {
        $mInsert = "INSERT INTO ".BASE_DATOS.".tab_transp_tercer
                                ( cod_transp, cod_tercer, usr_creaci, fec_creaci )
                          VALUES( '".$_AJAX['transp']."', '".$_AJAX['num_docten']."', '".$_SESSION['datos_usuario']['cod_usuari']."' , NOW() )";             
        // echo "<hr>".$mInsert;                  
        $consulta = new Consulta( $mInsert, $this->conexion, "R" );
      }
      
      if( !$this->VerifyTercerActivi( $_AJAX['num_docten'], COD_FILTRO_POSEED ) )
      {
        $mInsert = "INSERT INTO ".BASE_DATOS.".tab_tercer_activi
                                ( cod_tercer, cod_activi )
                          VALUES( '".$_AJAX['num_docten']."', '".COD_FILTRO_POSEED."' )";
        
        // echo "<hr>".$mInsert;                  
        $consulta = new Consulta( $mInsert, $this->conexion, "R" );
      }
    }
    
    /*  INSERCION CONDUCTOR  */
    
    if( !$this->VerifyTercerExist( $_AJAX['num_doccon'] ) )
    {

        $mInsert = "INSERT INTO ".BASE_DATOS.".tab_tercer_tercer
                              ( cod_tercer,	num_verifi, cod_tipdoc, nom_apell1,
                                nom_tercer, abr_tercer, dir_domici, num_telmov, 
                                dir_emailx, cod_estado, obs_tercer, usr_creaci, 
                                fec_creaci )
                        VALUES( '".$_AJAX['num_doccon']."', '".$_AJAX['num_divcon']."', '".$_AJAX['tip_doccon']."', '".$_AJAX['ape_tercon']."',
                                '".$_AJAX['nom_tercon']."', '".( $_AJAX['ape_tercon'].' '.$_AJAX['nom_tercon'] )."', 'N/A', '".$_AJAX['cel_tercon']."', '".$_AJAX['ema_tercon']."',
                                '1', 'Tercero creado por el módulo de despachos', '".$_SESSION['datos_usuario']['cod_usuari']."', NOW() )";
       // echo "<hr>".$mInsert;
      $consulta = new Consulta( $mInsert, $this->conexion, "R" );
    }
    else
    {
      $mUpdate = "UPDATE ".BASE_DATOS.".tab_tercer_tercer 
                     SET cod_estado = '1', 
                         obs_aproba = 'Tercero activado por el módulo de despachos',
                         usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
                         fec_modifi = NOW()
                   WHERE cod_tercer = '".$_AJAX['num_doccon']."'";
       // echo "<hr>".$mUpdate;
      $consulta = new Consulta( $mUpdate, $this->conexion, "R" );
    }
    
    if( !$this->VerifyTercerConduc( $_AJAX['num_doccon'] ) )
    {
      
        $mInsert = "INSERT INTO ".BASE_DATOS.".tab_tercer_conduc
                              ( cod_tercer,	cod_tipsex, num_licenc, fec_venlic,
                                obs_conduc, usr_creaci, fec_creaci )
                        VALUES( '".$_AJAX['num_doccon']."', '1', '".$_AJAX['lic_conduc']."', '".$_AJAX['fec_venlic']."',
                                'Conductor creado por el módulo de despachos', '".$_SESSION['datos_usuario']['cod_usuari']."', NOW() )";
       // echo "<hr>".$mInsert;
      $consulta = new Consulta( $mInsert, $this->conexion, "R" );
    }
    #en caso de que el condutor sea el mismo propietario
    if($_AJAX["con_propie"] == 1){
      $_AJAX['num_docpro'] = $_AJAX['num_doccon'];
      if( !$this->VerifyTercerActivi( $_AJAX['num_doccon'], COD_FILTRO_PROPIE ) ){
        $mInsert = "INSERT INTO ".BASE_DATOS.".tab_tercer_activi
                                ( cod_tercer, cod_activi )
                          VALUES( '".$_AJAX['num_doccon']."', '".COD_FILTRO_PROPIE."' )";
        
         // echo "<hr>".$mInsert;                  
        $consulta = new Consulta( $mInsert, $this->conexion, "R" );
      }
    }
    #en caso de que el condutor sea el mismo poseedor
    if($_AJAX["con_poseed"] == 1){
      $_AJAX['num_docten']  = $_AJAX['num_doccon'];
       if( !$this->VerifyTercerActivi( $_AJAX['num_doccon'], COD_FILTRO_POSEED ) ){
        $mInsert = "INSERT INTO ".BASE_DATOS.".tab_tercer_activi
                                ( cod_tercer, cod_activi )
                          VALUES( '".$_AJAX['num_doccon']."', '".COD_FILTRO_POSEED."' )";
        
         // echo "<hr>".$mInsert;                  
        $consulta = new Consulta( $mInsert, $this->conexion, "R" );
      }
    }
    

    
    if( !$this->VerifyTercerTransp( $_AJAX['num_doccon'], $_AJAX['transp'] ) )
    {
      $mInsert = "INSERT INTO ".BASE_DATOS.".tab_transp_tercer
                              ( cod_transp, cod_tercer, usr_creaci, fec_creaci )
                        VALUES( '".$_AJAX['transp']."', '".$_AJAX['num_doccon']."', '".$_SESSION['datos_usuario']['cod_usuari']."' , NOW() )";             
       // echo "<hr>".$mInsert;                  
      $consulta = new Consulta( $mInsert, $this->conexion, "R" );
    }
    
    if( !$this->VerifyTercerActivi( $_AJAX['num_doccon'], COD_FILTRO_CONDUC ) )
    {
      $mInsert = "INSERT INTO ".BASE_DATOS.".tab_tercer_activi
                              ( cod_tercer, cod_activi )
                        VALUES( '".$_AJAX['num_doccon']."', '".COD_FILTRO_CONDUC."' )";
      
       // echo "<hr>".$mInsert;                  
      $consulta = new Consulta( $mInsert, $this->conexion, "R" );
    }
    
    // -------------------- INSERCION DEL VEHICULO ------------------------//
    $mInsert = "INSERT INTO ".BASE_DATOS.".tab_vehicu_vehicu
                            ( num_placax, cod_marcax, cod_lineax, ano_modelo, 
                              cod_colorx, cod_carroc, val_capaci, num_poliza, 
                              nom_asesoa, fec_vigfin, num_config, cod_propie, 
                              cod_tenedo, cod_conduc, ind_estado, obs_estado, 
                              usr_creaci, fec_creaci)
                      VALUES( '".$_AJAX['num_placax']."', '".$_AJAX['cod_marcax']."', '".$_AJAX['cod_lineax']."', '".$_AJAX['num_modelo']."',
                              '".$_AJAX['cod_colorx']."', '".$_AJAX['cod_carroc']."', '".$_AJAX['val_capaci']."', '".$_AJAX['num_soatxx']."',
                              '".$_AJAX['nom_asesoa']."', '".$_AJAX['fec_vencim']."', '".$_AJAX['cod_config']."', '".$_AJAX['num_docpro']."',
                              '".$_AJAX['num_docten']."', '".$_AJAX['num_doccon']."', '1', 'Vehiculo Creado y aprobado por el modulo de despachos',
                              '".$_SESSION['datos_usuario']['cod_usuari']."', NOW() )";
     // echo "<hr>".$mInsert;                  
    $consulta = new Consulta( $mInsert, $this->conexion, "R" );
    
    $mInsert = "INSERT INTO ".BASE_DATOS.".tab_transp_vehicu
                            ( cod_transp, num_placax, usr_creaci, fec_creaci )
                      VALUES( '".$_AJAX['transp']."', '".$_AJAX['num_placax']."', '".$_SESSION['datos_usuario']['cod_usuari']."', NOW() )";
     // echo "<hr>".$mInsert;                  
    $consulta = new Consulta( $mInsert, $this->conexion, "R" );
    
    // $mInsert = "INSERT INTO ".BASE_DATOS.".tab_trayle_placas
                            // ( num_placax, num_trayle, num_noveda, fec_asigna, ind_actual )
                      // VALUES( '".$_AJAX['num_placax']."', '".$_AJAX['num_remolq']."', '1', NOW(), 'S' )";
    // echo "<hr>".$mInsert;                  
    // $consulta = new Consulta( $mInsert, $this->conexion, "R" );
    // --------------------------------------------------------------------//
    $insercion = new Consulta( "COMMIT" , $this->conexion );
    
  }
  
  protected function verifyTercero( $_AJAX )
  {
    /*echo "<pre>";
    print_r( $_AJAX );
    echo "</pre>";*/
    
    $mSelect = "SELECT cod_tipdoc, num_verifi,";
    $mSelect .= " abr_tercer,";
    $mSelect .= " nom_tercer, CONCAT( nom_apell1,' ',nom_apell2 ) AS ape_tercer,";
    $mSelect .= " IF( num_telmov IS NOT NULL,num_telmov, 'N/A' ) AS num_telmov , IF( dir_emailx IS NOT NULL,dir_emailx, 'N/A' ) AS dir_emailx FROM ".BASE_DATOS.".tab_tercer_tercer WHERE cod_tercer = '".$_AJAX['cod_tercer']."'";
    
    //echo $mSelect;
    
    $consulta = new Consulta($mSelect, $this->conexion);
    $_TERCERO = $consulta->ret_matriz();
    if( sizeof( $_TERCERO ) > 0 )
    {
      echo "yes-".$_TERCERO[0]['cod_tipdoc']."-".$_TERCERO[0]['num_verifi']."-".$_TERCERO[0]['abr_tercer']."-".$_TERCERO[0]['nom_tercer']."-".$_TERCERO[0]['ape_tercer']."-".$_TERCERO[0]['num_telmov']."-".$_TERCERO[0]['dir_emailx'];
    }
    else
    {
      echo "no";
    }
    
  }
 
 protected function verifyConduc( $_AJAX )
  {
    /*echo "<pre>";
    print_r( $_AJAX );
    echo "</pre>";*/
    
    $mSelect = "SELECT num_licenc, fec_venlic";
    $mSelect .= " FROM ".BASE_DATOS.".tab_tercer_conduc WHERE cod_tercer = '".$_AJAX['cod_tercer']."'";
    
    //echo $mSelect;
    
    $consulta = new Consulta($mSelect, $this->conexion);
    $_TERCERO = $consulta->ret_matriz();
    if( sizeof( $_TERCERO ) > 0 )
    {
      echo "yes/".$_TERCERO[0]['num_licenc']."/".$_TERCERO[0]['fec_venlic'];
    }
    else
    {
      echo "no";
    }
    
  }
  
  protected function setFormTercero( $_AJAX )
  {
    /*echo "<pre>";
    print_r( $_AJAX );
    echo "</pre>";*/
    
    $query = "SELECT cod_tipdoc, nom_tipdoc 
                FROM ".BASE_DATOS.".tab_genera_tipdoc ";
    if( $_AJAX['type'] == 'N' )
    {
      $query .= " WHERE cod_tipdoc = '".$_AJAX['type']."'";
    }
    else
    {
      $query .= " WHERE cod_tipdoc != 'N'";
    }
    
    $consulta = new Consulta($query, $this->conexion);
    $tipdoc  = $consulta->ret_matriz();

    $mHtml = '<center><table width="100%" cellspacing="2px" cellpadding="0">';
    
    $mHtml .= '<tr>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">* Documento:&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr"><input type="text" name="num_doc'.$_AJAX['tercer'].'" onkeypress="return NumericInput( event );" onchange="verifyTercero( this.value, \'num_div'.$_AJAX['tercer'].'ID\', \''.$_AJAX['tercer'].'\' );" onkeyup="$(\'#num_div'.$_AJAX['tercer'].'ID\').val( GenerateDV( this.value ) );" id="num_doc'.$_AJAX['tercer'].'ID" size="20" maxlength="11" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /> - <input type="text" name="num_div'.$_AJAX['tercer'].'" readonly id="num_div'.$_AJAX['tercer'].'ID" size="3" maxlength="1" /></td>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">* Tipo Documento:&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr">'.$this->GenerateSelect( $tipdoc, 'tip_doc'.$_AJAX['tercer'], NULL, NULL ).'</td>';
    $mHtml .= '</tr>';
    
    if( $_AJAX['type'] == 'N' )
    {
      $mHtml .= '<tr>';
      $mHtml .= '<td align="right" width="20%" class="label-tr">* Nombre:&nbsp;&nbsp;&nbsp;</td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr"><input type="text" name="nom_ter'.$_AJAX['tercer'].'" id="nom_ter'.$_AJAX['tercer'].'ID" size="50" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
      $mHtml .= '<td align="right" width="20%" class="label-tr">&nbsp;</td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr">&nbsp;</td>';
      $mHtml .= '</tr>';
    }
    else
    {
      $mHtml .= '<tr>';
      $mHtml .= '<td align="right" width="20%" class="label-tr">* Nombres:&nbsp;&nbsp;&nbsp;</td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr"><input type="text" name="nom_ter'.$_AJAX['tercer'].'" id="nom_ter'.$_AJAX['tercer'].'ID" size="50" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
      $mHtml .= '<td align="right" width="20%" class="label-tr">* Apellidos:&nbsp;&nbsp;&nbsp;</td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr"><input type="text" name="pae_ter'.$_AJAX['tercer'].'" id="ape_ter'.$_AJAX['tercer'].'ID" size="50" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
      $mHtml .= '</tr>';
    }
    
    $mHtml .= '<tr>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">* Celular:&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr"><input type="text" name="cel_ter'.$_AJAX['tercer'].'" onkeypress="return NumericInput( event );" id="cel_ter'.$_AJAX['tercer'].'ID" size="25" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">* E-mail:&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr"><input type="text" name="ema_ter'.$_AJAX['tercer'].'" id="ema_ter'.$_AJAX['tercer'].'ID" size="45" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '</table></center>';
    echo $mHtml;
  }
  
  protected function FormAsignacionVehiculo( $_AJAX )
  {
    $_VEHICU = $this->getInfoVehiculo( NULL, $_AJAX['num_placax'], true);

    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";
    $mHtml  = '<div align="center" style="background-color: #f0f0f0; border: 1px solid #c9c9c9; padding: 5px; width: 99%; min-height: 100px; -moz-border-radius: 5px 5px 5px 5px; -webkit-border-radius: 5px 5px 5px 5px; border-top-left-radius: 5px; border-top-right-radius: 5px; border-bottom-right-radius: 5px; border-bottom-left-radius: 5px; color:#000000;" >';
    $mHtml .= '<center><table width="100%" cellspacing="2px" cellpadding="0">';
    
    $mHtml .= '<tr>';
    $mHtml .= '<td colspan="4" style="text-align:center;border-bottom:1px solid #000000; font-family:Trebuchet MS, Verdana, Arial; font-size:15px; font-weight:bold; padding-bottom:5px; color: #285C00;" >Asignaci&oacute;n de Veh&iacute;culo</td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '<tr>';
    $mHtml .= '<td colspan="4" style="text-align:left;font-family:Trebuchet MS, Verdana, Arial; font-size:11px; padding-bottom:5px; color: #000000;" >El Veh&iacute;culo con Placa <b>'.$_AJAX['num_placax'].'</b> ya se encuentra registrado, Si desea asignarlo a la Transportadora, haga click en \'Aceptar\', de lo contrario haga click en \'Cancelar\'</b></td>';
    $mHtml .= '</tr>';
    
    
    $mHtml .= '<tr>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">Marca:&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr">'.$_VEHICU[0][5].'</td>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">L&iacute;nea:&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr">'.$_VEHICU[0][6].'</td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '<tr>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">Color:&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr">'.$_VEHICU[0][7].'</td>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">Carrocer&iacute;a:&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr">'.$_VEHICU[0][8].'</td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '<tr>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">Configuraci&oacute;n:&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr">'.$_VEHICU[0][12].'</td>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">Modelo:&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr">'.$_VEHICU[0][9].'</td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '<tr>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">Documento Propietario:&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr">'.$_VEHICU[0][15].'</td>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">Nombre:&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr">'.$_VEHICU[0][16].'</td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '<tr>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">Documento Tenedor:&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr">'.$_VEHICU[0][13].'</td>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">Nombre:&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr">'.$_VEHICU[0][1].'</td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '<tr>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">Documento Conductor:&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr">'.$_VEHICU[0][14].'</td>';
    $mHtml .= '<td align="right" width="20%" class="label-tr">Nombre:&nbsp;&nbsp;&nbsp;</td>';
    $mHtml .= '<td align="left" width="30%" class="label-tr">'.$_VEHICU[0][3].'</td>';
    $mHtml .= '</tr>';
    
    

    $mHtml .= '</table></center>';
    
    $mHtml .= '<form id="MainFormID" action="index.php" method="post" name="MainForm">';
    
    $mHtml .= '<br><input class="crmButton small save" type="button" id="InsertID" value="Aceptar" onclick="SaveAsignVehicu(\''.$_AJAX['cod_transp'].'\', \''.$_AJAX['num_placax'].'\');"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="crmButton small save" type="button" id="CancelID" value="Cancelar" onclick="PopupVehiculos();"/>';
    $mHtml .= '</form>';
     
    $mHtml .= '</div>';
    echo $mHtml;
  }
  
  private function VerifyTercerActivi( $cod_tercer, $cod_activi )
  {
    $mSelect = "SELECT 1 
                  FROM ".BASE_DATOS.".tab_tercer_activi 
                 WHERE cod_tercer = '".$cod_tercer."' 
                   AND cod_activi = '".$cod_activi."'";
    $consulta = new Consulta($mSelect, $this->conexion);
    $resultad = $consulta->ret_matriz();
    return sizeof( $resultad ) > 0 ? true : false ;
  }
  
  private function VerifyTercerExist( $cod_tercer )
  {
    $mSelect = "SELECT 1 
                  FROM ".BASE_DATOS.".tab_tercer_tercer
                 WHERE cod_tercer = '".$cod_tercer."'";
    $consulta = new Consulta($mSelect, $this->conexion);
    $resultad = $consulta->ret_matriz();
    return sizeof( $resultad ) > 0 ? true : false ;
  }
   private function VerifyTercerConduc( $cod_tercer )
  {
    $mSelect = "SELECT 1 
                  FROM ".BASE_DATOS.".tab_tercer_conduc
                 WHERE cod_tercer = '".$cod_tercer."'";
    $consulta = new Consulta($mSelect, $this->conexion);
    $resultad = $consulta->ret_matriz();
    return sizeof( $resultad ) > 0 ? true : false ;
  }
  
  private function VerifyTercerTransp( $cod_tercer, $cod_transp )
  {
    $mSelect = "SELECT 1 
                  FROM ".BASE_DATOS.".tab_transp_tercer 
                 WHERE cod_tercer = '".$cod_tercer."' 
                   AND cod_transp = '".$cod_transp."'";
    $consulta = new Consulta($mSelect, $this->conexion);
    $resultad = $consulta->ret_matriz();
    return sizeof( $resultad ) > 0 ? true : false ;
  }
  
  protected function SaveAsignVehicu( $_AJAX )
  {
    echo "<pre>";
    print_r( $_AJAX );
    echo "</pre>";
    
    $_VEHICU = $this->getInfoVehiculo( NULL, $_AJAX['num_placax'], true);
    $cod_propie = $_VEHICU[0][15];
    $cod_tenedo = $_VEHICU[0][13];
    $cod_conduc = $_VEHICU[0][14];
    
    $consulta = new Consulta("SELECT NOW()", $this->conexion, "BR");
    
    /*  ACTIVACION PARA EL PROPIETARIO  */
    if( !$this->VerifyTercerActivi( $cod_propie, COD_FILTRO_PROPIE ) )
    {
      $mInsert = "INSERT INTO ".BASE_DATOS.".tab_tercer_activi
                              ( cod_tercer, cod_activi )
                        VALUES( '".$cod_propie."', '".COD_FILTRO_PROPIE."' )";
                        
      $consulta = new Consulta( $mInsert, $this->conexion, "R" );
    }
    if( !$this->VerifyTercerTransp( $cod_propie, $_AJAX['cod_transp'] ) )
    {
      $mInsert = "INSERT INTO ".BASE_DATOS.".tab_transp_tercer
                              ( cod_transp, cod_tercer, usr_creaci, fec_creaci )
                        VALUES( '".$_AJAX['cod_transp']."', '".$cod_propie."', '".$_SESSION['datos_usuario']['cod_usuari']."' , NOW() )";
                        
      $consulta = new Consulta( $mInsert, $this->conexion, "R" );
    }
    $mUpdate = "UPDATE ".BASE_DATOS.".tab_tercer_tercer 
                   SET cod_estado = '1',
                       obs_aproba = 'Activado automáticamente por el nuevo módulo de despachos',
                       usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
                       fec_modifi = NOW()
                 WHERE cod_tercer = '".$cod_propie."'
                        ";
    $consulta = new Consulta( $mUpdate, $this->conexion, "R" );
    /************************************/
    
    /*  ACTIVACION PARA EL TENEDOR  */
    if( !$this->VerifyTercerActivi( $cod_tenedo, COD_FILTRO_POSEED ) )
    {
      $mInsert = "INSERT INTO ".BASE_DATOS.".tab_tercer_activi
                              ( cod_tercer, cod_activi )
                        VALUES( '".$cod_tenedo."', '".COD_FILTRO_POSEED."' )";
                        
      $consulta = new Consulta( $mInsert, $this->conexion, "R" );
    }
    if( !$this->VerifyTercerTransp( $cod_tenedo, $_AJAX['cod_transp'] ) )
    {
      $mInsert = "INSERT INTO ".BASE_DATOS.".tab_transp_tercer
                              ( cod_transp, cod_tercer, usr_creaci, fec_creaci )
                        VALUES( '".$_AJAX['cod_transp']."', '".$cod_tenedo."', '".$_SESSION['datos_usuario']['cod_usuari']."' , NOW() )";
                        
      $consulta = new Consulta( $mInsert, $this->conexion, "R" );
    }
    $mUpdate = "UPDATE ".BASE_DATOS.".tab_tercer_tercer 
                   SET cod_estado = '1',
                       obs_aproba = 'Activado automáticamente por el nuevo módulo de despachos',
                       usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
                       fec_modifi = NOW()
                 WHERE cod_tercer = '".$cod_tenedo."'
                        ";
    $consulta = new Consulta( $mUpdate, $this->conexion, "R" );
    /************************************/
    
    /*  ACTIVACION PARA EL CONDUCTOR  */
    if( !$this->VerifyTercerActivi( $cod_conduc, COD_FILTRO_CONDUC ) )
    {
      $mInsert = "INSERT INTO ".BASE_DATOS.".tab_tercer_activi
                              ( cod_tercer, cod_activi )
                        VALUES( '".$cod_conduc."', '".COD_FILTRO_CONDUC."' )";
                        
      $consulta = new Consulta( $mInsert, $this->conexion, "R" );
    }
    if( !$this->VerifyTercerTransp( $cod_conduc, $_AJAX['cod_transp'] ) )
    {
      $mInsert = "INSERT INTO ".BASE_DATOS.".tab_transp_tercer
                              ( cod_transp, cod_tercer, usr_creaci, fec_creaci )
                        VALUES( '".$_AJAX['cod_transp']."', '".$cod_conduc."', '".$_SESSION['datos_usuario']['cod_usuari']."' , NOW() )";
                        
      $consulta = new Consulta( $mInsert, $this->conexion, "R" );
    }
    $mUpdate = "UPDATE ".BASE_DATOS.".tab_tercer_tercer 
                   SET cod_estado = '1',
                       obs_aproba = 'Activado automáticamente por el nuevo módulo de despachos',
                       usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
                       fec_modifi = NOW()
                 WHERE cod_tercer = '".$cod_conduc."' ";
    
    $consulta = new Consulta( $mUpdate, $this->conexion, "R" );
    /************************************/
    
    /*  ACTIVACION PARA EL VEHICULO  */
    $mInsert = "INSERT INTO ".BASE_DATOS.".tab_transp_vehicu
                              ( cod_transp, num_placax, usr_creaci, fec_creaci )
                        VALUES( '".$_AJAX['cod_transp']."', '".$_AJAX['num_placax']."', '".$_SESSION['datos_usuario']['cod_usuari']."' , NOW() )";
                        
    $consulta = new Consulta( $mInsert, $this->conexion, "R" );
      
    $mUpdate = "UPDATE ".BASE_DATOS.".tab_vehicu_vehicu 
                 SET ind_estado = '1',
                     obs_estado = 'Activado automáticamente por el nuevo módulo de despachos',
                     usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
                     fec_modifi = NOW()
               WHERE num_placax = '".$_AJAX['num_placax']."' ";
    
    $consulta = new Consulta( $mUpdate, $this->conexion, "R" );
    
    $insercion = new Consulta( "COMMIT" , $this->conexion );
  }
  
  protected function ValidateVehiculo( $_AJAX )
  {
    /*echo "<pre>";
    print_r( $_AJAX );
    echo "</pre>";*/
    $validacion_existe = $this->verifyExistenciaVehiculo( $_AJAX['num_placax'] );
    $validacion_asigna = $this->verifyAsignacionVehiculo( $_AJAX['num_placax'], $_AJAX['cod_transp'] );
    
    if( sizeof( $validacion_existe ) > 0 && sizeof( $validacion_asigna ) > 0 )
    {
      echo "existe";
    }
    else if( sizeof( $validacion_existe ) > 0  && sizeof( $validacion_asigna ) <= 0  )
    {
      echo "asignar";
    }
    else
    {
      echo "no_existe";
    }
  }
  
  private function verifyExistenciaVehiculo( $num_placax )
  {
    $mQuery = "SELECT num_placax, cod_marcax, cod_lineax,
                      cod_colorx, cod_carroc, val_capaci,
                      num_config, ano_modelo
                 FROM ".BASE_DATOS.".tab_vehicu_vehicu
                WHERE num_placax = '".$num_placax."'";
  
    $consulta = new Consulta($mQuery, $this->conexion);
    return $consulta->ret_matriz();
  }
   
  private function verifyAsignacionVehiculo( $num_placax, $cod_transp )
  {
    $mQuery = "SELECT cod_transp, num_placax
                 FROM ".BASE_DATOS.".tab_transp_vehicu
                WHERE num_placax = '".$num_placax."' 
                  AND cod_transp = '".$cod_transp."'";
  
    $consulta = new Consulta($mQuery, $this->conexion);
    return $consulta->ret_matriz();
  } 
  
  private function SetRutas( $_AJAX )
  {

    $query = "SELECT a.cod_rutasx,a.nom_rutasx
                  FROM ".BASE_DATOS.".tab_genera_rutasx a,
                 	     ".BASE_DATOS.".tab_genera_ruttra b
                 WHERE a.cod_rutasx = b.cod_rutasx AND
                       b.cod_transp = '".$_AJAX['cod_transp']."' AND
                       a.cod_ciuori = '".$_AJAX['cod_ciuori']."' AND
                       a.cod_ciudes = '".$_AJAX['cod_ciudes']."' AND
		      		   a.ind_estado = '".COD_ESTADO_ACTIVO."'
                       GROUP BY 1 ORDER BY 2 ";

    $consulta = new Consulta($query, $this->conexion);
    $destino = $consulta->ret_matriz();

    $mHtml = '';
    $mHtml .= '<option value="">- Seleccione -</option>'; 
    foreach( $destino as $row )
    {
      $mHtml .= '<option value="'.$row[0].'">'.$row[1].'</option>'; 
    }
    echo $mHtml;
  }
  private function SetLineas( $_AJAX )
  {

    $query = "SELECT cod_lineax, nom_lineax
						  FROM ".BASE_DATOS.".tab_vehige_lineas
						  WHERE cod_marcax = '".$_AJAX['cod_marcax']."' ";

	  $query .= " ORDER BY 2";

    $consulta = new Consulta($query, $this->conexion);
    $destino = $consulta->ret_matriz();

    $mHtml = '';
    $mHtml .= '<option value="">- Seleccione -</option>'; 
    foreach( $destino as $row )
    {
      $mHtml .= '<option value="'.$row[0].'">'.$row[1].'</option>'; 
    }
    echo $mHtml;
  }
  
  private function GenerateSelect( $arr_select, $name, $key = NULL, $events = NULL, $obl = NULL  )
  {
    if($obl != null){
      $obl = " validate='select' obl='1'";
    }
    $mHtml  = '<select style="width:100%" name="'.$name.'" id="'.$name.'ID" '.$events.$obl.'>';
    $mHtml .= '<option value="">- Seleccione -</option>';
    foreach ($arr_select as $key => $row) {
      $selected = '';
      if( $row[0] == $key )
        $selected = 'selected="selected"';
      
      $mHtml .= '<option value="'.$row[0].'" '.$selected.'>'.$row[1].'</option>';
    }
    $mHtml .= '</select>';
    return $mHtml;
  }
  
  private function GetCiuori( $cod_transp )
  {
    $query = "SELECT a.cod_ciudad,CONCAT(a.abr_ciudad,' (',LEFT(d.abr_depart,4),') - ',LEFT(e.nom_paisxx,3))
                FROM ".BASE_DATOS.".tab_genera_ciudad a,
                     ".BASE_DATOS.".tab_genera_rutasx b,
                     ".BASE_DATOS.".tab_genera_ruttra c,
                     ".BASE_DATOS.".tab_genera_depart d,
                     ".BASE_DATOS.".tab_genera_paises e
               WHERE a.cod_ciudad = b.cod_ciuori AND
                     b.cod_depori = d.cod_depart AND
                     b.cod_paiori = d.cod_paisxx AND
                     d.cod_paisxx = e.cod_paisxx AND
                     b.cod_rutasx = c.cod_rutasx AND
                     c.cod_transp = '".$cod_transp."' AND
                     b.ind_estado = '".COD_ESTADO_ACTIVO."'
            GROUP BY 1 
            ORDER BY 2";

    $consulta = new Consulta( $query, $this->conexion );
    return $consulta->ret_matriz();
  }
  
  private function GetTipdes()
  {
    $query = "SELECT a.cod_tipdes, UPPER( a.nom_tipdes ) AS nom_tipdes
     		        FROM ".BASE_DATOS.".tab_genera_tipdes a
               WHERE 1
     		    GROUP BY 1
            ORDER BY 2";

    $consulta = new Consulta( $query, $this->conexion );
    return $consulta->ret_matriz();
  }
  
  private function GetFormDatosSeguros( $_AJAX )
  {
    
    $opegps = $this->getOpegps();
    $asegur = $this->getAsegur();
    $mHtml  = '<table width="100%" cellspacing="0" cellpadding="0" border="0">';
    
    $mHtml .= '<tr>';
      $mHtml .= '<td align="right" width="20%" class="label-tr">Operador GPS:&nbsp;&nbsp;&nbsp;</td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr">'.$this->GenerateSelect( $opegps, 'cod_opegps', NULL, 'onchange="habIdOperaGps(this)"', NULL ).'</td>';
      $mHtml .= '<td align="right" width="20%" class="label-tr">Otro GPS:&nbsp;&nbsp;&nbsp;</td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr"><input style="width:100%" type="text" name="gps_otroxx" id="gps_otroxxID" size="30" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
    $mHtml .= '</tr>';
    $mHtml .= '<tr>';
      $mHtml .= '<td align="right" width="20%" class="label-tr">Usuario GPS:&nbsp;&nbsp;&nbsp;</td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr"><input style="width:100%" type="text" name="usr_gpsxxx" id="usr_gpsxxxID" size="30" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
      $mHtml .= '<td align="right" width="20%" class="label-tr">Clave GPS:&nbsp;&nbsp;&nbsp;</td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr"><input style="width:100%" type="text" name="clv_gpsxxx" id="clv_gpsxxxID" size="25" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
    $mHtml .= '</tr>';
    $mHtml .= '<tr>';
      $mHtml .= '<td align="right" width="20%" class="label-tr">ID GPS:&nbsp;&nbsp;&nbsp;</td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr"><input style="width:100%" type="text" name="gps_idxxxx" id="gps_idxxxxID" size="30" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
      $mHtml .= '<td align="right" width="20%" class="label-tr">&nbsp;&nbsp;&nbsp;</td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr">&nbsp;</td>';
    $mHtml .= '</tr>';
    $mHtml .= '<tr>';
      $mHtml .= '<td align="right" width="20%" class="label-tr">Aseguradora:&nbsp;&nbsp;&nbsp;</td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr">'.$this->GenerateSelect( $asegur, 'nom_asegur', NULL, NULL, NULL ).'</td>';
      $mHtml .= '<td align="right" width="20%" class="label-tr">No. P&oacute;liza:&nbsp;&nbsp;&nbsp;</td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr"><input style="width:100%" type="text" name="num_poliza" id="num_polizaID" size="19" maxlength="19" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
    $mHtml .= '</tr>';
       
    $mHtml .= '</table>';
    return $mHtml;
  }
  
  private function GetFormDatosDestinx( $_AJAX )
  {

    $mHtml  .= '<table width="100%" cellspacing="0" cellpadding="0" border="0">';
    
    $mHtml .= '<tr>';
      $mHtml .= '<td align="left" width="100%" class="label-info" colspan="10">Remesas asignados al Despacho. Para agregar otra haga click <a href="#" style="color:#285C00; text-decoration:none; cursor:pointer;" onclick="AddGrid();">aqu&iacute;</a><br>&nbsp;</td>';
    $mHtml .= '</tr>';
    
    $mHtml  .= '</table>';
    $mHtml .= '<input type="hidden" id="counterID" value="0" name="counter" />';
    $mHtml  .= $this->ShowDestin( $_AJAX );

    return $mHtml;
  }

  private function GetFormDatosAdicion( $_AJAX ){

    $mHtml  .= '<table width="100%" cellspacing="0" cellpadding="0" border="0">';
    
    $mHtml .= '<tr>';
      $mHtml .= '<td align="left" >Observaciones Generales:</td>';
      $mHtml .= '<td align="left" ><textarea  name="obs_genera" id="obs_generaID" validate="dir" minlength="1" maxlength="500"></textarea></td>';
      $mHtml .= '<td align="left" >Otros Medios de Comunicaci&oacute;n:</td>';
      $mHtml .= '<td align="left" ><textarea name="otr_comuni" id="otr_comuniID" validate="dir" minlength="5" maxlength="200"></textarea></td>';
    $mHtml .= '</tr>';
    
    $mHtml  .= '</table>';
    
    return $mHtml;

  }
  
  protected function InsertDespacho( $_AJAX )
  {
    $datos = (object) $_AJAX;
    

    #consulta el ultimo consecutivo del despacho
    $mSelect = "SELECT MAX( num_despac ) AS maximo
                  FROM ".BASE_DATOS.".tab_despac_despac ";
		
    $consec = new Consulta( $mSelect, $this->conexion, "BR" );
		$ultimo = $consec->ret_matriz();
		
    #incrementa el consecutivo
		$ultimo_consec = $ultimo[0][0];
		$datos->num_despac = $ultimo_consec + 1;    
    
    #consulta el pais y el departamento de la ciudad origen
    $mSelect = "SELECT a.cod_paisxx, a.cod_depart
                  FROM ".BASE_DATOS.".tab_genera_ciudad a
                 WHERE a.cod_ciudad = '$datos->cod_ciuori'";
		
		$consulta = new Consulta( $mSelect, $this->conexion, "R" );
		$paidepori = $consulta->ret_matriz();
    $datos->cod_paiori = $paidepori[0][0];
    $datos->cod_depori = $paidepori[0][1];
    
    $mSelect = "SELECT a.cod_paisxx,a.cod_depart
                  FROM ".BASE_DATOS.".tab_genera_ciudad a
                 WHERE a.cod_ciudad = '$datos->cod_ciudes' ";

		$consulta = new Consulta($mSelect, $this->conexion, "R");
		$paidepdes = $consulta->ret_matriz();
    $datos->cod_paides = $paidepdes[0][0];
    $datos->cod_depdes = $paidepdes[0][1];
    $datos->usr_creaci = $_SESSION['datos_usuario']['cod_usuari'];

    //$nombre_opegps = $this->getOpegps( $datos->cod_opegps);
    //$datos->gps_operad = $nombre_opegps[0][0];
    
    $datos->val_declar = str_replace( '.', '', $datos->val_declar );
    
    $urlGps = $this->getUrlGps($datos->cod_opegps);
    $mInsert = "INSERT INTO ".BASE_DATOS.".tab_despac_despac
                          ( 
                            num_despac, cod_manifi, fec_despac, cod_tipdes,
                            cod_client,	cod_paiori,	cod_depori,	cod_ciuori,
                            cod_paides,	cod_depdes,	cod_ciudes,	cod_cenope,
                            cod_operad, fec_citcar, hor_citcar, nom_sitcar,
                            val_flecon, val_despac, val_antici, val_retefu, 
                            nom_carpag, nom_despag, cod_agedes, fec_pagoxx, 
                            obs_despac, val_declara,usr_creaci, fec_creaci, 
                            val_pesoxx, gps_operad, gps_usuari, gps_paswor,
                            gps_idxxxx, gps_otroxx, cod_asegur, num_poliza,
                            gps_urlxxx
                          ) 
                   VALUES ('$datos->num_despac','$datos->cod_manifi','$datos->fec_despac ".DATE('H:i:s')."','$datos->cod_tipdes',
                           ".($datos->cod_client != '' ?"'$datos->cod_client'":"NULL").",'$datos->cod_paiori','$datos->cod_depori','$datos->cod_ciuori',
                           '$datos->cod_paides','$datos->cod_depdes','$datos->cod_ciudes','$datos->cod_cenope',
                           '$datos->cod_operad','$datos->fec_citcar','$datos->hor_citcar','$datos->sit_cargue',
                            NULL,NULL,NULL,NULL,
                            NULL,NULL, '$datos->cod_agenci',NULL,
                           '$datos->obs_genera','$datos->val_declar','$datos->usr_creaci', NOW(),
                           '$datos->val_pesoxx','$datos->cod_opegps','$datos->usr_gpsxxx','$datos->clv_gpsxxx',
                           '$datos->gps_idxxxx','$datos->gps_otroxx','$datos->nom_asegur','$datos->num_poliza',
                           '$urlGps')"; 
    $consulta = new Consulta($mInsert, $this->conexion, "R");
    
    $mInsert = "INSERT INTO ".BASE_DATOS.".tab_despac_corona
                          ( 
                            num_dessat, num_despac, cod_manifi, fec_despac, cod_tipdes,
                            cod_paiori,	cod_depori,	cod_ciuori,
                            cod_paides,	cod_depdes,	cod_ciudes,
                            cod_operad, fec_citcar, hor_citcar, nom_sitcar,
                            val_flecon, val_despac, val_antici, val_retefu, 
                            nom_carpag, nom_despag, cod_agedes,  
                            obs_despac,usr_creaci, fec_creaci, 
                            val_pesoxx, gps_operad, gps_usuari, gps_paswor,
                            gps_idxxxx, cod_asegur, num_poliza, num_placax, cod_mercan
                          ) 
                   VALUES ('$datos->num_despac','$datos->cod_manifi','$datos->cod_manifi','$datos->fec_despac ".DATE('H:i:s')."','$datos->cod_tipdes',
                           ".($datos->cod_paiori != '' ?"'$datos->cod_paiori'":"NULL").",
                           ".($datos->cod_depori != '' ?"'$datos->cod_depori'":"NULL").",".($datos->cod_ciuori != '' ?"'$datos->cod_ciuori'":"NULL").",
                           ".($datos->cod_paides != '' ?"'$datos->cod_paides'":"NULL").",".($datos->cod_depdes != '' ?"'$datos->cod_depdes'":"NULL").",
                           ".($datos->cod_ciudes != '' ?"'$datos->cod_ciudes'":"NULL").",
                           ".($datos->cod_operad != '' ? "'$datos->cod_operad'":"NULL").",'$datos->fec_citcar','$datos->hor_citcar','$datos->sit_cargue',
                            NULL,NULL,NULL,NULL,NULL,'', '$datos->cod_agenci',
                           '$datos->obs_genera','$datos->usr_creaci', NOW(),
                           ".($datos->val_pesoxx != '' ? "'$datos->val_pesoxx'":"NULL").",".($datos->cod_opegps != '' ? "'$datos->cod_opegps'":"NULL").",
                           '$datos->usr_gpsxxx','$datos->clv_gpsxxx','$datos->gps_otroxx','$datos->nom_asegur',
                           '$datos->num_poliza', '$datos->num_placax', '$datos->cod_mercan')"; 
    $consulta = new Consulta($mInsert, $this->conexion, "R");
    
    if( $datos->cod_desext != '' ){
      $mInsert = "INSERT INTO ".BASE_DATOS.".tab_despac_sisext
                            ( num_despac, num_desext )
                      VALUES( '$datos->num_despac','$datos->cod_desext')";
      $consulta = new Consulta($mInsert, $this->conexion, "R");
    }
    // echo "<pre>";
    // print_r( $mInsert );
    // echo "</pre>";
    
    if( $_AJAX['cod_remolq'] == 'not' ){
      $num_remolq = NULL ;
    }else{
      $mSelect = "SELECT a.	num_trayle
                  FROM ".BASE_DATOS.".tab_vehige_trayle a
                 WHERE a.num_trayle = '$datos->cod_remolq'
                 LIMIT 1";

      $consulta = new Consulta($mSelect, $this->conexion, "R");
      $trayle = $consulta->ret_matriz();
      
      if( sizeof( $trayle ) <= 0 ){
        $mInsert= "INSERT INTO ".BASE_DATOS.".tab_vehige_trayle
                             ( num_trayle, cod_marcax, cod_colore, dir_fottra, 
                               cod_carroc, ano_modelo, nro_ejes, nom_propie, 
                               cod_config, ind_estado, usr_creaci, fec_creaci) 
                       VALUES( '$datos->cod_remolq', 'NN', '0', NULL,
                               '0', '".date("Y")."', '4', 'DESCONOCIDO',
                               '2', '1', '$datos->usr_creaci', NOW() 
                               )";
        
        $consulta = new Consulta( $mInsert, $this->conexion, "R" );
        
        $num_remolq = $datos->cod_remolq;
      }else{
        $num_remolq = $trayle[0][0] != '' ? $trayle[0][0] : NULL ;
      }
    }
    
    if( $num_remolq != NULL  ){
      $mInsert = "INSERT INTO ".BASE_DATOS.".tab_despac_vehige
                          (
                            num_despac, cod_transp, cod_agenci, 
                            cod_rutasx, cod_conduc, num_placax, 
                            num_trayle, obs_medcom, ind_activo, 
                            usr_creaci, fec_creaci
                          )
                     VALUES 
                          (
                            '$datos->num_despac', '$datos->cod_transp', '$datos->cod_agenci', 
                           '$datos->cod_rutaxx', '$datos->cod_conduc', '$datos->num_placax', 
                            '".$num_remolq."', '$datos->otr_comuni', 'R',
                            '$datos->usr_creaci', NOW() 
                          )";
    }else{
      $mInsert = "INSERT INTO ".BASE_DATOS.".tab_despac_vehige
                          (
                            num_despac, cod_transp, cod_agenci, 
                            cod_rutasx, cod_conduc, num_placax, 
                            num_trayle, obs_medcom, ind_activo, 
                            usr_creaci, fec_creaci
                          )
                     VALUES 
                          (
                            '$datos->num_despac', '$datos->cod_transp', '$datos->cod_agenci', 
                            '$datos->cod_rutaxx', '$datos->cod_conduc', '$datos->num_placax', 
                            NULL, '$datos->otr_comuni', 'R',
                            '$datos->usr_creaci', NOW() 
                          )";  
      
    }
		
		
		$consulta = new Consulta( $mInsert, $this->conexion, "R" );
    
    /*echo "<pre>";
    print_r( $_AJAX );
    echo "</pre>";*/
    
    $cod_genera = $_AJAX['cod_client'];
    $nom_genera = strtoupper($this->getInfoTercer($cod_genera)['nom_tercer']);
    for( $k = 0; $k <= $_AJAX['counter']; $k++ ){
      if( $_AJAX[ 'cod_remesa'.$k ] != NULL ){

        //$cod_genera = $_AJAX[ 'cod_genera'.$k ] != "" && $_AJAX[ 'cod_genera'.$k ] != NULL ? "'".$_AJAX[ 'cod_genera'.$k ]."'" : "NULL" ;
        //$cod_ciudad = $_AJAX[ 'cod_ciudad'.$k ] != "" && $_AJAX[ 'cod_ciudad'.$k ] != NULL ? "'".$_AJAX[ 'cod_ciudad'.$k ]."'" : "NULL" ;

        $cod_ciudes = $_AJAX['cod_ciudes'] != "" && $_AJAX['cod_ciudes'] != NULL ? "'".$_AJAX['cod_ciudes']."'" : "NULL" ;
        //Extrae informaci? del destinatario
        $nom_destin = strtoupper($this->getInfoRemdes($_AJAX['nom_destin'.$k ])['nom_remdes']);
        $nit_destin = strtoupper($this->getInfoRemdes($_AJAX['nom_destin'.$k ])['num_remdes']);
        $num_destin = strtoupper($this->getInfoRemdes($_AJAX['nom_destin'.$k ])['num_telefo']);

        $mInsert = "INSERT INTO ".BASE_DATOS.".tab_despac_destin
                              (
                                num_despac, num_docume, num_docalt, cod_genera, nom_genera,
                                nom_destin, cod_ciudad, dir_destin, num_destin, 
                                fec_citdes, hor_citdes, usr_creaci, fec_creaci,
                                cod_remdes, nit_destin
                              )
                         VALUES
                              (
                                '$datos->num_despac', '".$_AJAX['cod_remesa'.$k ]."','".$_AJAX['num_docalt'.$k ]."', '".$cod_genera."', '".$nom_genera."', 
                                '".$nom_destin."', ".$cod_ciudes.",'".$_AJAX['dir_destin'.$k ]."', '".$num_destin."',  
                                '".$_AJAX['fec_citdes'.$k ]."', '".$_AJAX['hor_citdes'.$k ]."','$datos->usr_creaci', NOW(), 
                                '".$_AJAX['nom_destin'.$k ]."', '".$nit_destin."')";
        $consulta = new Consulta( $mInsert, $this->conexion, "R" );
      
        #segun nuevas especificaciones debe insertar en la tabla  tab_despac_remesa
        $mInsert = "INSERT INTO ".BASE_DATOS.".tab_despac_remesa (num_despac,cod_remesa,fec_estent,val_pesoxx,
                                                                  val_volume,des_empaqu,des_mercan,abr_client,
                                                                  usr_creaci,fec_creaci)
                                                           VALUES('$datos->num_despac','".$_AJAX['cod_remesa'.$k]."','".$_AJAX['fec_citdes'.$k ]." ".$_AJAX['hor_citdes'.$k ]."','".$_AJAX['val_pesoxx'.$k ]."',
                                                                  '".$_AJAX['val_volume'.$k ]."','".$_AJAX['cod_empaqu'.$k ]."','".$_AJAX['nom_mercan'.$k ]."','".$_AJAX['nom_destin'.$k ]."',
                                                                  '$datos->usr_creaci', NOW())";
            
      
        $consulta = new Consulta( $mInsert, $this->conexion, "R" );
      }
    }




    # Agrega los datos de viaje en caso de que exista
    if( $_REQUEST["cod_desext"] != '' )
    {
      $mInsert = "INSERT INTO  ".BASE_DATOS.".tab_despac_viajex 
                  ( num_despac, num_placax, num_viajex, cod_transp, usr_creaci, fec_creaci ) 
                  VALUES 
                  ('$datos->num_despac', '$datos->num_placax', '$datos->cod_desext', '$datos->cod_transp', '$datos->usr_creaci', NOW() ) ";
      $consulta = new Consulta($mInsert, $this -> conexion, "R");
    }

    
    //die();
    
    /****** VERIFICACION DE TERCEROS *********************************************************/
    $mSelect = "SELECT cod_propie, cod_tenedo, cod_conduc 
                  FROM ".BASE_DATOS.".tab_vehicu_vehicu
                 WHERE num_placax = '$datos->num_placax'";
    
    $consulta = new Consulta($mSelect, $this->conexion, "R");
		$terceros = $consulta->ret_matriz();
    
    if( !$this->VerifyTercerTransp( $terceros[0][0], $datos->cod_transp) ){
      $mInsert = "INSERT INTO ".BASE_DATOS.".tab_transp_tercer
                              ( cod_transp, cod_tercer, usr_creaci, fec_creaci )
                        VALUES( '$datos->cod_transp', '".$terceros[0][0]."', '$datos->usr_creaci' , NOW() )";
      $consulta = new Consulta( $mInsert, $this->conexion, "R" );
    }
    
    if( !$this->VerifyTercerTransp( $terceros[0][1], $datos->cod_transp ) ){
      $mInsert = "INSERT INTO ".BASE_DATOS.".tab_transp_tercer
                              ( cod_transp, cod_tercer, usr_creaci, fec_creaci )
                        VALUES( '$datos->cod_transp', '".$terceros[0][1]."', '$datos->usr_creaci' , NOW() )";
      $consulta = new Consulta( $mInsert, $this->conexion, "R" );
    }
    
    if( !$this->VerifyTercerTransp( $terceros[0][2], $datos->cod_transp ) ){
      $mInsert = "INSERT INTO ".BASE_DATOS.".tab_transp_tercer
                              ( cod_transp, cod_tercer, usr_creaci, fec_creaci )
                        VALUES( '$datos->cod_transp', '".$terceros[0][2]."', '$datos->usr_creaci' , NOW() )";
      $consulta = new Consulta( $mInsert, $this->conexion, "R" );
    }
    
    /**********************************************************************/
    
    if($datos->cod_opegps != NULL && $datos->cod_opegps != '')
    {
      $mSql = "SELECT MAX(cod_consec) FROM ".BASE_DATOS.".tab_despac_gpsxxx";
      $consulta = new Consulta($mSql, $this->conexion, "R");
      $cod_consec = $consulta->ret_matriz();
      $mInsert = "INSERT INTO ".BASE_DATOS.".tab_despac_gpsxxx
                              ( num_despac, cod_consec, idx_gpsxxx, cod_opegps, nom_usrgps, clv_usrgps, usr_creaci, fec_creaci )
                        VALUES( '$datos->num_despac', '".$cod_consec[0][0]."', '$datos->gps_idxxxx', '$datos->cod_opegps', '$datos->usr_gpsxxx', '$datos->clv_gpsxxx', '$datos->usr_creaci' , NOW() )";
      $consulta = new Consulta( $mInsert, $this->conexion, "R" );
    }
      
    if( $insercion = new Consulta( "COMMIT", $this->conexion ) ){
      $mHtml  .= '<center><table width="100%" cellspacing="2px" cellpadding="0">';
    
      $mHtml .= '<tr>';
      $mHtml .= '<td colspan="4" style=" text-align:center;border-bottom:1px solid #000000; font-family:Trebuchet MS, Verdana, Arial; font-size:15px; font-weight:bold; padding-bottom:5px; color: #285C00;" >El Despacho No. <b> '.$datos->num_despac.' </b> ha sido Creado Exitosamente.</i></td>';
      $mHtml .= '</tr>';
      $mHtml .= '</table></center>';
      echo $mHtml;
    }else{
      ?>
      <div class="col-md-12">
        <div class="col-md-6"></div>
        <div class="col-md-6"></div>
      </div>
      <?php
    }

    //Parche de estado a homologar a clientes diferentes a satt_faro
    if(BASE_DATOS != 'satt_faro'){
      //Inserta historial de Estados a homologar
      setTrackingDespac($this->conexion, $datos->num_despac, 2, 1, null);
    }
    
  }

  private function getInfoTercer($cod_tercer){
    $mSql = "SELECT a.nom_tercer
               FROM ".BASE_DATOS.".tab_tercer_tercer a
              WHERE a.cod_tercer = '".$cod_tercer."'";
    $consulta = new Consulta( $mSql, $this->conexion );
		$respuesta = $consulta->ret_matriz();
    if(count($respuesta)>0){
      return $respuesta[0];
    }else{
      return '';
    }
  }

  private function getInfoRemdes($cod_remdes){
    $mSql = "SELECT a.nom_remdes, a.num_remdes, a.dir_remdes,
                    a.num_telefo
               FROM ".BASE_DATOS.".tab_genera_remdes a
              WHERE a.cod_remdes = '".$cod_remdes."'";
    $consulta = new Consulta( $mSql, $this->conexion );
		$respuesta = $consulta->ret_matriz();
    if(count($respuesta)>0){
      return $respuesta[0];
    }else{
      return '';
    }
  }

  private function getUrlGps( $cod_opegps = NULL )
  {
    $mSql = "SELECT url_gpsxxx
               FROM ".BASE_DATOS.".tab_genera_opegps
              WHERE (cod_operad = '".$cod_opegps."' OR nit_operad = '".$cod_opegps."')";
    $consulta = new Consulta( $mSql, $this->conexion );
		$respuesta = $consulta->ret_matriz();
    if(count($respuesta)>0){
      return $respuesta[0]['url_gpsxxx'];
    }
    return null;
    
  }
  
  protected function ShowDestin( $_AJAX )
  {
    if( $_AJAX['counter'] == '' )
    {
      $_AJAX['counter'] = 0;
    }
    
    $style = $_AJAX['counter'] % 2 == 0 ? 'cellInfo1' : 'cellInfo2' ;
    
    $ciudad = $this->getCiudad();
    $genera = $this->getGenera( $_AJAX['cod_transp'] );
    $empaqu = $this->getEmpaqu();
    
    $mHtml  .= '
    <script>
      $(function() {
         $(".date").datepicker();
         $(".date").datepicker("option", {
            dateFormat: "yy-mm-dd", 
            minDate: new Date('.(date('Y')).','. (date('m')-1) .','.(date('d')).')
        });
         $(".time").timepicker({
            showSecond: false
        });
       /* $( ".time" ).timepicker();
        
        $.mask.definitions["A"]="[12]";
        $.mask.definitions["M"]="[01]";
        $.mask.definitions["D"]="[0123]";

        $.mask.definitions["H"]="[012]";
        $.mask.definitions["N"]="[012345]";
        $.mask.definitions["n"]="[0123456789]";

        $( ".date" ).mask("Annn-Mn-Dn");
        $( ".time" ).mask("Hn:Nn:Nn");*/

      });
    </script>';
    
    $mHtml .= '<div id="DestinID">';
      $mHtml .= '<table width="100%" cellspacing="0" cellpadding="0" border="0">';
      $mHtml .= '<tr>';
        $mHtml .= '<td align="left" colspan="6" class="cellHead" width="10%">REMESA No. '. ( $_AJAX['counter'] + 1 ) .'</td>';   
      $mHtml .= '</tr>';
      $mHtml .= '<tr>';
        $mHtml .= '<td align="center" class="cellHead" width="10%">No. REMESA</td>';
        $mHtml .= '<td align="center" class="cellHead" width="10%">PESO (TN)</td>';
        $mHtml .= '<td align="center" class="cellHead" width="10%">VOLUMEN</td>';
        $mHtml .= '<td align="center" class="cellHead" width="10%">EMPAQUE</td>';
        $mHtml .= '<td align="center" class="cellHead" width="40%">MERCANCIA</td>';
        $mHtml .= '<td align="center" class="cellHead" width="20%">CLIENTE</td>';
        
      $mHtml .= '</tr>';

      $mHtml .= '<tr>';
        $mHtml .= '<td align="center" class="'.$style.'"><input type="text" style="width:100%" size="10" name="cod_remesa'.$_AJAX['counter'].'" id="cod_remesa'.$_AJAX['counter'].'ID" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
        $mHtml .= '<td align="center" class="'.$style.'"><input type="text" style="width:100%" size="10" name="val_pesoxx'.$_AJAX['counter'].'" id="val_pesoxx'.$_AJAX['counter'].'ID" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
        $mHtml .= '<td align="center" class="'.$style.'"><input type="text" style="width:100%" size="10" name="val_volume'.$_AJAX['counter'].'" id="val_volume'.$_AJAX['counter'].'ID" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
        $mHtml .= '<td align="center" class="'.$style.'">'.$this->GenerateSelect( $empaqu, 'cod_empaqu'.$_AJAX['counter'] ).'</td>';
        $mHtml .= '<td align="center" class="'.$style.'"><input type="text" style="width:100%" size="30" name="nom_mercan'.$_AJAX['counter'].'" id="nom_mercan'.$_AJAX['counter'].'ID" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
        $mHtml .= '<td align="center" class="'.$style.'"><input type="hidden" name="nom_destin'.$_AJAX['counter'].'" id="nom_destin'.$_AJAX['counter'].'ID"><input type="text" style="width:100%" size="30" name="nom_destinVal'.$_AJAX['counter'].'" id="nom_destinVal'.$_AJAX['counter'].'ID" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
        
      $mHtml .= '</tr>';
      
      $mHtml  .= '</table>';
      
      $mHtml  .= '<table width="100%" cellspacing="0" cellpadding="0" border="0">';
       
      $mHtml .= '<tr>';
        $mHtml .= '<td align="center" class="cellHead" width="20%">CIUDAD</td>';
        $mHtml .= '<td align="center" class="cellHead" width="30%">DIRECCI&Oacute;N</td>';
        $mHtml .= '<td align="center" class="cellHead" width="20%">CONTACTO</td>';
        $mHtml .= '<td align="center" class="cellHead" width="15%">FECHA CITA DESCARGUE</td>';
        $mHtml .= '<td align="center" class="cellHead" width="15%">HORA CITA DESCARGUE</td>';
      $mHtml .= '</tr>';
      
      $mHtml .= '<tr>';
        $mHtml .= '<td align="center" class="'.$style.'">'.$this->GenerateSelect( $ciudad, 'cod_ciudad'.$_AJAX['counter'] ).'</td>';
        $mHtml .= '<td align="center" class="'.$style.'"><input style="width:100%" minlength="5" maxlength="40" type="text" size="40" name="dir_destin'.$_AJAX['counter'].'" id="dir_destin'.$_AJAX['counter'].'ID" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
        $mHtml .= '<td align="center" class="'.$style.'"><input validate="numero" style="width:100%" minlength="5" maxlength="15" type="text" size="15" name="nom_contac'.$_AJAX['counter'].'" id="nom_contac'.$_AJAX['counter'].'ID" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
        $mHtml .= '<td align="center" class="'.$style.'"><input validate="fecha" style="width:100%" minlength="10" maxlength="10" type="text" name="fec_citdes'.$_AJAX['counter'].'" id="fec_citdes'.$_AJAX['counter'].'ID" class="date" size="20" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
        $mHtml .= '<td align="center" class="'.$style.'"><input validate="hora" style="width:100%" minlength="8" maxlength="8" type="text" name="hor_citdes'.$_AJAX['counter'].'" id="hor_citcar'.$_AJAX['counter'].'ID" class="time" size="15" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
      $mHtml .= '</tr>';
      
      $mHtml .= '</table><br>&nbsp;';

    $mHtml .= '</div>';
    if( $_AJAX['counter'] == 0 )
    {
      return $mHtml;
    }
    else
    {
      echo $mHtml;
    }
  }
  
  private function GetFormDatosVehicxx( $_AJAX )
  { 

    $mHtml  = '<table width="100%" cellspacing="0" cellpadding="0" border="0">';
    
    $mHtml .= '<tr>';
      $mHtml .= '<td align="left" width="100%" class="label-info" colspan="4"><b>Nota:</b> Al hacer click sobre el icono <img height="18px" width="18px" src="../satt_standa/imagenes/find.png">, aparecer&aacute; una lista con todos los veh&iacute;culos asignados a la transportadora.</td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '<tr>';
      $mHtml .= '<td align="right" width="20%" class="label-tr">* Placa:&nbsp;&nbsp;&nbsp;</td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr"><input name="num_placax" id="num_placaxID" type="text" obl="1" validate="placa" minlength="6"  maxlength="6" size="9" onfocus="this.className=\'campo_texto_on\'" onblur="getVehiculo($(this), '.$_AJAX['cod_transp'].', 1)" /> <img height="18px" width="18px" style="cursor:pointer" calss="popupButton2" id="Pnum_placaxID" onclick="PopupVehiculos()" src="../satt_standa/imagenes/find.png" title="Buscar" disabled="disabled"></td>';
      $mHtml .= '<td align="right" width="20%" class="label-tr"><label id="des_marcaxID">&nbsp;</label></td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr"><label id="nom_marcaxID">&nbsp;</label></td>';
    $mHtml .= '</tr>';
     
    $mHtml .= '<tr>';
      $mHtml .= '<td align="right" width="20%" class="label-tr"><label id="des_colorxID">&nbsp;</label></td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr"><label id="nom_colorxID">&nbsp;</label></td>';
      $mHtml .= '<td align="right" width="20%" class="label-tr"><label id="des_carrocID">&nbsp;</label></td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr"><label id="nom_carrocID">&nbsp;</label></td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '<tr>';
      $mHtml .= '<td align="right" width="20%" class="label-tr"><label id="des_modeloID">&nbsp;</label></td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr"><label id="nom_modeloID">&nbsp;</label></td>';
      $mHtml .= '<td align="right" width="20%" class="label-tr"><label id="des_configID">&nbsp;</label></td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr"><label id="nom_configID">&nbsp;</label></td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '<tr>';
      $mHtml .= '<td align="right" width="20%" class="label-tr"><label id="des_codproID">&nbsp;</label></td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr"><label id="nom_codproID">&nbsp;</label></td>';
      $mHtml .= '<td align="right" width="20%" class="label-tr"><label id="des_nomproID">&nbsp;</label></td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr"><label id="nom_nomproID">&nbsp;</label></td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '<tr>';
      $mHtml .= '<td align="right" width="20%" class="label-tr"><label id="des_codtenID">&nbsp;</label></td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr"><label id="nom_codtenID">&nbsp;</label></td>';
      $mHtml .= '<td align="right" width="20%" class="label-tr"><label id="des_nomtenID">&nbsp;</label></td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr"><label id="nom_nomtenID">&nbsp;</label></td>';
    $mHtml .= '</tr>';
    
    /*$mHtml .= '<tr>';
      $mHtml .= '<td align="left" width="100%" class="label-info" colspan="4"><label id="lab_conducID">&nbsp;</label></td>';
    $mHtml .= '</tr>';*/
    
    $mHtml .= '<tr>';
      $mHtml .= '<td align="right" width="20%" class="label-tr"><label id="des_codconID">&nbsp;</label></td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr"><label id="nom_codconID">&nbsp;</label></td>';
      $mHtml .= '<td align="right" width="20%" class="label-tr"><label id="des_nomconID">&nbsp;</label></td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr"><label id="nom_nomconID">&nbsp;</label></td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '<tr>';
      $mHtml .= '<td align="right" width="20%" class="label-tr"><label id="des_numremID">&nbsp;</label></td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr"><label id="nom_numremID">&nbsp;</label></td>';
      $mHtml .= '<td align="right" width="20%" class="label-tr"><label id="xxxx">&nbsp;</label></td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr"><label id="xxxx">&nbsp;</label></td>';
    $mHtml .= '</tr>';
       
    $mHtml .= '</table>';
    return $mHtml;
  }
  
  protected function ValidateExistConduc( $_AJAX )
  {
    /*echo "<pre>";
    print_r( $_AJAX );
    echo "</pre>";
    */
    if( !$this->VerifyTercerConduc( $_AJAX['num_conduc'] ) || !$this->VerifyTercerTransp( $_AJAX['num_conduc'], $_AJAX['cod_transp'] ) )
      echo "no";
    else
      echo "yes";
  }
  
  private function GetFormDatosOperaci( $_AJAX )
  {
    $cennot = $this->getCenNot( $_AJAX['cod_transp'] );
    $operad = $this->getCenOpe( $_AJAX['cod_transp'] );
    $mHtml  = '<table width="100%" cellspacing="0" cellpadding="0" border="0">';
    
    $mHtml .= '<tr>';
      $mHtml .= '<td align="right" width="20%" class="label-tr">Centro de Operaciones:&nbsp;&nbsp;&nbsp;</td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr">'.$this->GenerateSelect( $cennot, 'cod_cenope', NULL, NULL ).'</td>';
      $mHtml .= '<td align="right" width="20%" class="label-tr">Operador:&nbsp;&nbsp;&nbsp;</td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr">'.$this->GenerateSelect( $operad, 'cod_operad', NULL, NULL ).'</td>';
    $mHtml .= '</tr>';
     
    $mHtml .= '<tr>';
      $mHtml .= '<td align="right" width="20%" class="label-tr">Fecha Cita Cargue:&nbsp;&nbsp;&nbsp;</td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr"><input style="width:100%" type="text" name="fec_citcar" id="fec_citcarID" class="date" size="20" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
      $mHtml .= '<td align="right" width="20%" class="label-tr">Hora Cargue:&nbsp;&nbsp;&nbsp;</td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr"><input style="width:100%" type="text" name="hor_citcar" id="hor_citcarID" class="time" size="15" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '<tr>';
      $mHtml .= '<td align="right" width="20%" class="label-tr">Sitio Cargue:&nbsp;&nbsp;&nbsp;</td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr"><input style="width:100%" type="text" name="sit_cargue" id="sit_cargueID" size="50" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
      $mHtml .= '<td align="right" width="20%" class="label-tr">&nbsp;</td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr">&nbsp;</td>';
    $mHtml .= '</tr>';
       
    $mHtml .= '</table>';
    return $mHtml;
  }
  
  private function GetFormDatosBasicos( $_AJAX )
  {
    /*echo "<pre>";
    print_r( $_AJAX );
    echo "</pre>";*/
    $agenci = $this->getAgencias( $_AJAX['cod_transp'] );
    $tipdes = $this->getTipDes();
    $ciuori = $this->GetCiuori( $_AJAX['cod_transp'] );
    $geneCa = $this->GetGeneCarga( $_AJAX['cod_transp'] );
    $geneMe = $this->GetGeneMerca();
    
    $mHtml  = '<table width="100%" cellspacing="0" cellpadding="0" border="0">';
    
    $mHtml .= '<tr>';
      $mHtml .= '<td align="right" width="20%" class="label-tr">* No. Documento:&nbsp;&nbsp;&nbsp;</td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr"><input style="width:100%" type="text" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'; validaDespacho(this)"  minlength="5" maxlength="14" size="14" obl="1" validate="dir" name="cod_manifi" id="cod_manifiID" /></td>';
      $mHtml .= '<td align="right" width="20%" class="label-tr"> * No. interno de la transportadora:&nbsp;&nbsp;&nbsp;</td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr"><input style="width:100%" obl="1" validate="dir" type="text" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" minlength="5"  maxlength="20" size="20" name="cod_desext" id="cod_desextID" /></td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '<tr>';
      $mHtml .= '<td align="right" width="20%" class="label-tr"> * Agencia:&nbsp;&nbsp;&nbsp;</td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr">'.$this->GenerateSelect( $agenci, 'cod_agenci', NULL, NULL, 1 ).'</td>';
      $mHtml .= '<td align="right" width="20%" class="label-tr"> * Fecha:&nbsp;&nbsp;&nbsp;</td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr"><input style="width:100%" type="text" name="fec_despac" class="date" obl="1" validate="date" minlength="10" maxlength="10" value="'.date("Y-m-d").'" id="fec_despacID" size="20" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '<tr>';
      $mHtml .= '<td align="right" width="20%" class="label-tr"> * Origen:&nbsp;&nbsp;&nbsp;</td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr">'.$this->GenerateSelect( $ciuori, 'cod_ciuori', NULL, 'onchange="SetDestinos();"', 1 ).'</td>';
      $mHtml .= '<td align="right" width="20%" class="label-tr"> * Destino Final:&nbsp;&nbsp;&nbsp;</td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr">'.$this->GenerateSelect( NULL, 'cod_ciudes', NULL, 'onchange="SetRutas();"', 1 ).'</td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '<tr>';
      $mHtml .= '<td align="right" width="20%" class="label-tr"> * Ruta:&nbsp;&nbsp;&nbsp;</td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr2">'.$this->GenerateSelect( NULL, 'cod_rutaxx', NULL, NULL, 1 ).'</td>';
      $mHtml .= '<td align="right" width="20%" class="label-tr"> * Tipo de Despacho:&nbsp;&nbsp;&nbsp;</td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr">'.$this->GenerateSelect( $tipdes, 'cod_tipdes', NULL, NULL, 1 ).'</td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '<tr>';
      $mHtml .= '<td align="right" width="20%" class="label-tr"> Valor Declarado:&nbsp;&nbsp;&nbsp;</td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr"><input style="width:100%" style="width:100%" type="text" name="val_declar" onkeyup="puntos(this,this.value.charAt(this.value.length-1));" onkeypress="return NumericInput( event );" id="val_declarID" minlength="5" maxlength="11" size="35" validate="numero" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
      $mHtml .= '<td align="right" width="20%" class="label-tr"> * Peso(Tn):&nbsp;&nbsp;&nbsp;</td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr"><input style="width:100%" style="width:100%" type="text" name="val_pesoxx" onkeypress="return NumericInput( event );" id="val_pesoxxID" obl="1" minlength="1" maxlength="5" validate="numero" size="35" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
    $mHtml .= '</tr>';
    #Nuevo campo de generador de carga
    $mHtml .= '<tr>';
      $mHtml .= '<td align="right" width="20%" class="label-tr"> Generador de carga:&nbsp;&nbsp;&nbsp;</td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr2">'.$this->GenerateSelect( $geneCa, 'cod_client', NULL, NULL, NULL ).'</td>';
      $mHtml .= '<td align="right" width="20%" class="label-tr"> &nbsp;&nbsp;&nbsp;</td>';
      $mHtml .= '<td align="left" width="30%" class="label-tr"> &nbsp;</td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '</table>';
    return $mHtml;
  }

  private function GetFormDatosCarguex($_AJAX){

    $mHtml = '<div id="tableCargue">
                <input type="hidden" name="cantidad" id="cantidadID" value="0">
                <table width="100%"  cellspacing="0" cellpadding="0" border="0">
                  <tr>
                    <td align="right" width="20%" class="label-tr">* Fecha de cita de cargue: </td>
                    <td class="label-tr"><input type="text" style="width:100%" id="fec_citcar" readonly name="fec_citcar" obl="1" validate="date" maxlength="10" minlength="10"></td>
                    <td align="right" width="20%" class="label-tr">* Hora de cita de cargue:</td>
                    <td class="label-tr"><input type="text" style="width:100%" id="hor_citcar" name="hor_citcar" readonly obl="1" validate="hora" maxlength="8" minlength="8"></td>
                  </tr>
                  <tr>
                    <td align="right" width="20%" class="label-tr">* Sitio de Cargue</td>
                    <td class="label-tr">
                      <input type="hidden" id="sit_cargue" name="sit_cargue" validate="numero" obl="1" minlength="1" maxlength="20">
                      <input type="text" validate="textarea" style="width:100%" id="sit_cargueVal" name="sit_cargueVal" obl="1" minlength="5" maxlength="255">
                    </td>
                    <td class="label-tr" colspan="2"></td>
                  </tr>
                </table>
              </div>
              <table width="100%"  cellspacing="0" cellpadding="0" border="0">
                <!--<tr>
                <td colspan="4" style="text-align:center"><input type="button" value="Otro" class="small save ui-button ui-widget ui-state-default ui-corner-all" onclick="otroSitioCargue()"></td>
                </tr>-->
              </table>';
    return $mHtml;
     
  }
  
  private function getCiudad( $cod_ciudad = NULL )
  {
    $mSql = "SELECT cod_ciudad, UPPER( nom_ciudad ) AS nom_ciudad
               FROM ".BASE_DATOS.".tab_genera_ciudad 
              WHERE ind_estado = '1'";
    if( $cod_ciudad != NULL )
    {
      $mSql .= " AND cod_ciudad = ".$cod_ciudad;      
    }
    $mSql .= " ORDER BY 2";
    $consulta = new Consulta( $mSql, $this->conexion );
		return $consulta->ret_matriz();
  }
  
  protected function FormInsertNewConduc( $_AJAX )
  {
    /*echo "<pre>";
    print_r( $_AJAX );
    echo "</pre>";*/
    $mSelect = "SELECT a.cod_tercer, a.num_verifi, a.cod_tipdoc, 
                       CONCAT(a.nom_apell1, ' ', a.nom_apell2 ) AS ape_tercer, a.nom_tercer, a.num_telmov, 
                       a.dir_emailx, b.num_licenc, b.fec_venlic
                  FROM ".BASE_DATOS.".tab_tercer_tercer a, 
                       ".BASE_DATOS.".tab_tercer_conduc b 
                 WHERE a.cod_tercer = b.cod_tercer
                   AND a.cod_tercer = '".$_AJAX['cod_conduc']."'";
    
    $consulta = new Consulta($mSelect, $this->conexion);
    $_INFORES = $consulta->ret_matriz();
    $_TERCERO = $_INFORES[0];
    
    /*echo "<pre>";
    print_r( $_TERCERO );
    echo "</pre>";*/
    
     $query = "SELECT cod_tipdoc, nom_tipdoc 
                FROM ".BASE_DATOS.".tab_genera_tipdoc ";
    
    $query .= " WHERE cod_tipdoc != 'N'";
    
    $consulta = new Consulta($query, $this->conexion);
    $tipdoc  = $consulta->ret_matriz();
    
    $mHtml  = '
    <script>
      $(function() {
        $(".date").datepicker();
        $(".date").datepicker("option", {
            dateFormat: "yy-mm-dd", 
            minDate: new Date('.(date('Y')).','. (date('m')-1) .','.(date('d')).')
        });
       /*
        $.mask.definitions["A"]="[12]";
        $.mask.definitions["M"]="[01]";
        $.mask.definitions["D"]="[0123]";

        $.mask.definitions["H"]="[012]";
        $.mask.definitions["N"]="[012345]";
        $.mask.definitions["n"]="[0123456789]";

        $( ".date" ).mask("Annn-Mn-Dn");*/

      });
    </script>';
    
    $mHtml .= '<div align="center" style="background-color: #f0f0f0; border: 1px solid #c9c9c9; padding: 5px; width: 98%; min-height: 50px; -moz-border-radius: 5px 5px 5px 5px; -webkit-border-radius: 5px 5px 5px 5px; border-top-left-radius: 5px; border-top-right-radius: 5px; border-bottom-right-radius: 5px; border-bottom-left-radius: 5px; color:#000000;" >';
      $mHtml .= '<table width="100%" cellspacing="2px" cellpadding="0">';
      
      if( sizeof( $_INFORES ) > 0 )
        $title = "Edici&oacute;n y/o Asignaci&oacute;n de Conductor";
      else
        $title = "Inserci&oacute;n y Asignaci&oacute;n de Conductor";
      
      $mHtml .= '<tr>';
        $mHtml .= '<td colspan="4" style=" text-align:center;border-bottom:1px solid #000000; font-family:Trebuchet MS, Verdana, Arial; font-size:15px; font-weight:bold; padding-bottom:5px; color: #285C00;" >'.$title.'</td>';
      $mHtml .= '</tr>';
      
      $mHtml .= '<tr>';
        $mHtml .= '<td align="right" width="20%" class="label-tr">* Documento:&nbsp;&nbsp;&nbsp;</td>';
        $mHtml .= '<td align="left" width="30%" class="label-tr"><input type="text" value="'.$_AJAX['cod_conduc'].'" name="num_doccon" readonly onkeypress="return NumericInput( event );" onkeyup="$(\'#num_divconID\').val( GenerateDV( this.value ) );" id="num_docconID" size="20" maxlength="11" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /> - <input type="text" name="num_divcon" value="'.$_TERCERO['num_verifi'].'" readonly id="num_divconID" size="3" maxlength="1" /></td>';
        $mHtml .= '<td align="right" width="20%" class="label-tr">* Tipo Documento:&nbsp;&nbsp;&nbsp;</td>';
        $mHtml .= '<td align="left" width="30%" class="label-tr">'.$this->GenerateSelect( $tipdoc, 'tip_doccon', $_TERCERO['cod_tipdoc'], NULL ).'</td>';
      $mHtml .= '</tr>';
      
      $mHtml .= '<tr>';
        $mHtml .= '<td align="right" width="20%" class="label-tr">* Nombres:&nbsp;&nbsp;&nbsp;</td>';
        $mHtml .= '<td align="left" width="30%" class="label-tr"><input type="text" value="'.$_TERCERO['nom_tercer'].'" name="nom_tercon" id="nom_terconID" size="50" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
        $mHtml .= '<td align="right" width="20%" class="label-tr">* Apellidos:&nbsp;&nbsp;&nbsp;</td>';
        $mHtml .= '<td align="left" width="30%" class="label-tr"><input type="text" value="'.$_TERCERO['ape_tercer'].'" name="ape_tercon" id="ape_terconID" size="50" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
      $mHtml .= '</tr>';


      $mHtml .= '<tr>';
        $mHtml .= '<td align="right" width="20%" class="label-tr">* Celular:&nbsp;&nbsp;&nbsp;</td>';
        $mHtml .= '<td align="left" width="30%" class="label-tr"><input type="text" value="'.$_TERCERO['num_telmov'].'" name="cel_tercon" onkeypress="return NumericInput( event );" id="cel_terconID" size="25" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
        $mHtml .= '<td align="right" width="20%" class="label-tr">* E-mail:&nbsp;&nbsp;&nbsp;</td>';
        $mHtml .= '<td align="left" width="30%" class="label-tr"><input type="text" value="'.$_TERCERO['dir_emailx'].'" name="ema_tercon" id="ema_terconID" size="45" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
      $mHtml .= '</tr>';
      
     
      $mHtml .= '<tr>';
        $mHtml .= '<td align="right" width="20%" class="label-tr">* No. Licencia:&nbsp;&nbsp;&nbsp;</td>';
        $mHtml .= '<td align="left" width="30%" class="label-tr"><input type="text" value="'.$_TERCERO['num_licenc'].'" name="lic_conduc" id="lic_conducID" size="45" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
        $mHtml .= '<td align="right" width="20%" class="label-tr">* Fecha Vencimiento:&nbsp;&nbsp;&nbsp;</td>';
        $mHtml .= '<td align="left" width="30%" class="label-tr"><input type="text" value="'.$_TERCERO['fec_venlic'].'" name="fec_venlic" id="fec_venlicID" class="date" size="20" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
      $mHtml .= '</tr>';
      
      $mHtml .= '</table>';
      $mHtml .= '<br><input class="crmButton small save" type="button" id="InsertID" value="Aceptar" onclick="InsertConduc();"/>';
    $mHtml .= '</div>';
    
    echo $mHtml;
  }
  
  protected function setLabelConduc ( $_AJAX )
  {
    $mSelect = "SELECT UPPER(abr_tercer) AS abr_tercer FROM ".BASE_DATOS.".tab_tercer_tercer WHERE cod_tercer = '".$_AJAX['num_conduc']."'";

    $consulta = new Consulta($mSelect, $this->conexion);
    $_TERCERO = $consulta->ret_matriz();
    echo $_TERCERO[0][0];
  }
  
  private function GenerateDynamicDiv( $title, $content, $min, $id, $iddiv )
  {
    return $_DIV = '<div class="accordion" align="center" id="'.$iddiv.'">
                      <h3 style="padding:6px;">'.$title.'</h3>
                      <div>
                        <div align="left" id="'.$id.'" style="background-color: #f0f0f0; border: 1px solid #c9c9c9; padding: 5px; width: 100%; min-height: '.$min.'px; -moz-border-radius: 5px 5px 5px 5px; -webkit-border-radius: 5px 5px 5px 5px; border-top-left-radius: 5px; border-top-right-radius: 5px; border-bottom-right-radius: 5px; border-bottom-left-radius: 5px; color:#000000;" >
                        '.$content.'
                        </div>
                      </div>
                    </div>';
  }
  
  protected function ValidateTransp( $_AJAX ){
    $mSql = "SELECT 1
               FROM ".BASE_DATOS.".tab_tercer_tercer a,
                    ".BASE_DATOS.".tab_tercer_activi b
              WHERE a.cod_tercer = b.cod_tercer AND
                    b.cod_activi = ".$_AJAX['filter']." AND
                    a.cod_tercer = '". trim($_AJAX['cod_transp']) ."'";
		
		$consulta = new Consulta( $mSql, $this->conexion );
		$transpor = $consulta->ret_matriz();
    if( sizeof( $transpor ) > 0 ){
      echo 'y';
    }else{
      echo 'n';
    }
  }

  private function GetGeneCarga($transp)
  {
    try
    {
      $query = "SELECT a.cod_tercer,a.abr_tercer
             FROM ".BASE_DATOS.".tab_tercer_tercer a,
                  ".BASE_DATOS.".tab_tercer_activi b,
                  ".BASE_DATOS.".tab_transp_tercer c
            WHERE a.cod_tercer = b.cod_tercer AND
                  a.cod_tercer = c.cod_tercer AND
                  c.cod_transp = '".$transp."' AND
                  b.cod_activi = ".COD_FILTRO_CLIENT."
                  ORDER BY 2 ASC
          ";

      $consulta = new Consulta($query, $this->conexion);
      return $listgene = $consulta -> ret_matriz();
    }
    catch(Exception $e)
    {
      echo "<pre> Error Funcion GetGeneCarga:";print_r($e);echo "</pre>";
    }
  }

  private function GetGeneMerca()
  {
    try
    {
      $query = "SELECT  a.cod_produc, a.nom_produc
                  FROM  ".BASE_DATOS.".tab_genera_produc a
                 WHERE  a.ind_estado = '1'
              ORDER BY  2 ASC
          ";

      $consulta = new Consulta($query, $this->conexion);
      return $listMerc = $consulta -> ret_matriz();
    }
    catch(Exception $e)
    {
      echo "<pre> Error Funcion GetGeneMerca:";print_r($e);echo "</pre>";
    }
  }

 /* ! \fn: getVehiculo
   *  \brief: valida los vehiculos cuando se escriben
   *  \author: Andres Torres Vega
   *  \date: 20/12/2017
   *  \date modified: dd/mm/aaaa
   *  \param: obj input placa 
   *  \param: cod_transp nit de la empresa
   *  \return: type
 */
  private function getVehiculo($_AJAX){
    $_VEHICU = $this->getInfoVehiculo( $_AJAX['cod_transp'], $_AJAX['num_placax'], true);
    if (sizeof( $_VEHICU ) > 0 ) {
      // echo json_encode($_VEHICU, JSON_UNESCAPED_UNICODE);
      $mVehicu = [];
      foreach ($_VEHICU[0] AS $mIndex => $mData) 
      {
        $mVehicu[0][$mIndex] = utf8_encode($mData);
      }
      echo json_encode($mVehicu);
    }else{
      echo false;
    }
  }

   /* ! \fn: getRemolq
   *  \brief: valida los vehiculos cuando se escriben
   *  \author: Andres Torres Vega
   *  \date: 20/12/2017
   *  \date modified: dd/mm/aaaa
   *  \param: obj input placa 
   *  \param: cod_transp nit de la empresa
   *  \return: type
 */
  private function getRemolq($_AJAX){
    $_REMOLQ = $this->getInfoRemolq( $_AJAX['cod_transp'], $_AJAX['num_remolq'], true);
    if (sizeof( $_REMOLQ ) > 0 ) {
      echo json_encode($_REMOLQ);
    }else{
      echo false;
    }
  }

  public function LoadRemolques( $_AJAX )
  {
    try{
          $_REMOLQ = $this->getInfoRemolq( $_AJAX['cod_transp'], NULL, false);
          $mHtml = new Formlib(2, "yes",TRUE);
          $mHtml->OpenDiv("id:tabremolq");
              $mHtml->Table("tr",array("class"=>"displayDIV2"));
                $mHtml->Label( "LISATDO DE REMOLQUES", array("colspan"=>sizeof($titulos['Nivel1']), "align"=>"rigth", "width"=>"25%", "class"=>"CellHead") );
              $mHtml->CloseTable('tr');
            $mHtml->OpenDiv("id:tabremolq1");
              $mHtml->SetBody($this->getDinamiListRemolq($_REMOLQ));
            $mHtml->CloseDiv();
          $mHtml->CloseDiv(); 

                $mHtml->SetBody('<style>
                        #tabremolq{
                border: 1px solid rgb(201, 201, 201);
                padding: 3px;
                width: 200%;
                min-height: 50px;
                border-radius: 5px;
                background-color: rgb(240, 240, 240);
              }
                      </style>');

          echo $mHtml->MakeHtml();
      } catch (Exception $e) {
        echo "error LoadRemolques :".$e;
      }
  }

    private function getInfoRemolq( $cod_transp = NULL, $num_remolq = NULL, $flag = NULL)
  { 
    $mQuery = "SELECT a.num_trayle,a.nom_propie,b.nom_martra,d.nom_colorx,a.tra_capaci,e.nom_carroc,
                        IF(a.ind_estado = '1','Activo', 'Inactivo') cod_estado,
                        a.ind_estado cod_option
              FROM ".BASE_DATOS.".tab_vehige_trayle a
              INNER JOIN ".BASE_DATOS.".tab_vehige_martra b ON b.cod_martra = a.cod_marcax
              INNER JOIN ".BASE_DATOS.".tab_transp_trayle c ON c.num_trayle = a.num_trayle
              INNER JOIN ".BASE_DATOS.".tab_vehige_colore d ON d.cod_colorx = a.cod_colore
              INNER JOIN ".BASE_DATOS.".tab_vehige_carroc e ON e.cod_carroc = a.cod_carroc
              WHERE ";
    
    if( $cod_transp != NULL )
    {
      $mQuery .= " a.num_trayle = c.num_trayle AND a.ind_estado = '1' AND c.cod_transp = '".$cod_transp."'";
    }
    if( $num_remolq != NULL )
    {
      $mQuery .= " AND a.num_trayle = '".$num_remolq."'";
    }
    
    //echo $mQuery;
    $consulta = new Consulta( $mQuery, $this->conexion );
    if($flag != NULL){
      return $consulta->ret_matriz();
    }else{
      return $mQuery;
    }
  }

  /*! \fn: getDinamiListRemolq
   *  \brief: identifica el formulario correspondiete y lo pinta
   *  \author: Edward Serrano
   *  \date:  18/01/2017
   *  \date modified: dia/mes/aï¿½o
  */
  function getDinamiListRemolq($datos)
  { 
    try
    {   
      IncludeJS( '../js/dinamic_list.js' );
      IncludeJS( '../js/new_ajax.js' );
      echo "<link  href='../" . DIR_APLICA_CENTRAL . "/dinamic_list.php' type='script'>\n";
      echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/dinamic_list.css' type='text/css'>\n";
      echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";
      $_SESSION["queryXLS"] = $datos;

      if (!class_exists(DinamicList)) 
      {
          include_once("../" . DIR_APLICA_CENTRAL . "/lib/general/dinamic_list.inc");
      }
      $list = new DinamicList($this->conexion, $datos);
      $list->SetClose('no');
      $list->SetHeader(("Nro. de Remolque"), "field:a.num_trayle; width:1%; type:link; onclick:getRemolq($(this),".$_REQUEST['cod_transp'].",0) ");
      $list->SetHeader(("Poseedor"), "field:a.nom_propie; width:1%");
      $list->SetHeader(("Marca"), "field:a.nom_martra; width:1%");
      $list->SetHeader(("Color"), "field:a.nom_colorx" );
      $list->SetHeader(("Capacidad (TN)"), "field:a.tra_capaci" );
      $list->SetHeader(("Carroceria"), "field:a.nom_carroc" );
      $list->SetHidden("num_remolq", "0" );
      $list->SetHidden("nom_propie", "1" );
      $list->Display($this->conexion);

      $_SESSION["DINAMIC_LIST"] = $list;

      $Html = $list->GetHtml();

      if($_REQUEST["Ajax"] === 'on' )
      {
        echo $Html;
      }
      else
      {
        return $Html;
      }
    } catch (Exception $e) {
      echo "error getDinamiListRemolq :".$e;
    }
  }



  private function validaViaje(  )
  { 
    $mQuery = "SELECT a.num_despac, a.fec_despac
                 FROM ".BASE_DATOS.".tab_despac_despac a
           INNER JOIN ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac
                WHERE a.ind_anulad = 'R' AND
                      b.ind_activo = 'S' AND 
                      a.cod_manifi = '".trim( $_REQUEST['cod_manifi'] )."' AND 
                      b.cod_transp = '".trim( $_REQUEST['cod_transp'] )."' 

                  ";
  
    //echo $mQuery;
    $consulta = new Consulta( $mQuery, $this->conexion );
    $exists =  $consulta->ret_matriz('a');
    
    $m = sizeof($exists);
    header('Content-Type: application/json');
    echo json_encode(['status' => ($m <= 0 ? true : false), 'message' => ($m <= 0 ? 'ok' : 'Manifiesto ya esta registrado' ) ]);
  }



}

$proceso = new AjaxInsertDespacho();
 ?>
