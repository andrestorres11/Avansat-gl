<?php

 
  ini_set( "soap.wsdl_cache_enabled", "0" ); // disabling WSDL cache

  


 
  class TraficoPad
  {
    private $cConexion = NULL;
    private $cUrlWsdlA = "https://web5.intrared.net/ap/si_pad/ws/server.wsdl";

    function __construct()
    {
        if( !$_POST ) $_POST = $_GET;

        switch ($_POST["Opcion"]) 
        {
          case '1':
            TraficoPad::SetDataRecursos();
          break;
          case '2':
            TraficoPad::Processing();
          break;
         
          default:
            TraficoPad::Formulario();
          break;
        }
    }

    function JavaScript()
    {
      echo '  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">';
      echo '  <script src="//code.jquery.com/jquery-1.10.2.js"></script>';
      echo '  <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>';

      echo '
              <script>
                $(document).ready(function(){
                   $( "button" ).button().on("click",function(){
                       getWSDL();
                   });
                });
                function getWSDL()
                {
                  $.ajax({
                    type : "POST",
                    url  : "InterfPad.php",
                    data : "Opcion=1&numDespac="+$("#numDespacID").val(),
                    beforeSend: function(){
                      $("#ResponDataID").html( "<center><b>Consumiendo, Espere ...</b></center>" );
                    },
                    success: function( data ){
                      $("#ResponDataID").html( data );
                      var Result = JSON.parse( data );

                      if( Result.cod_respon != "1000" )
                      {
                        alert(Result.msg_respon);
                      }
                      else
                      {
                        $.ajax({
                          type : "POST",
                          url  : "InterfPad.php",
                          data : "Opcion=2&numDespac="+$("#numDespacID").val(),
                          beforeSend: function(){
                            $("#ResponDataID").html( Result.msg_respon+"<br>Solicitando Disponibilidad" );
                          },
                          success: function( data ){
                            $("#ResponDataID").html( data );
                          }
                        });
                        
                      }
                    }
                  });                  
                }
              </script>
           ';
    }

    function Formulario()
    {
      TraficoPad::JavaScript();

      $mHtml = '<table class="ui-tabs ui-widget ui-widget-content ui-corner-all" align="center" width="90%">';
        # Titulo -------------------------------------------------------------------------------------------------------------------------------------------------
        $mHtml .= '<tr>';
          $mHtml .= '<td colspan="8" class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" align="center"> Disponibilidad Vechiculos Trafico -> Pad<td>';
        $mHtml .= '</tr>';
        # --------------------------------------------------------------------------------------------------------------------------------------------------------
        $mHtml .= '<tr>';
          $mHtml .= '<td colspan="8">
                      <fieldset>
                        <legend>Despacho Trafico</legend>
                        <input type="text" id="numDespacID" name="numDespac" palceholder="Numero De Despacho" value="1127250"/>
                        <button>Consumir</button>
                        Placa:WTO756
                        Remolque:R84715
                      </fieldset><td>';
        $mHtml .= '</tr>';
        $mHtml .= '<tr>';
          $mHtml .= '<td colspan="8">
                      <fieldset>
                        <legend>Resultado:</legend>
                        <div id="ResponDataID">
                        <center>Esperando...</center>
                        </div>
                      </fieldset><td>';
        $mHtml .= '</tr>';



      $mHtml .= '</table>';
      echo $mHtml;
    }

    # Funcion para Traer datos de consultas ---------------------------------------------------------------------------------------------------------------------
    function getDataDespac( $mNumDespac )
    {
      $mQuery = "SELECT     a.num_despac, a.cod_manifi, a.fec_despac, 
                            a.cod_paiori, a.cod_depori, a.cod_ciuori, 
                            a.cod_paides, a.cod_depdes, a.cod_ciudes,
                            a.cod_agedes, a.fec_salida, a.obs_salida,
                            a.fec_llegad, a.con_telmov, 
                            b.cod_transp, b.cod_rutasx,
                            b.num_placax, 'R84715' AS num_trayle,
                            c.cod_propie, 
                            c.cod_tenedo,
                            b.cod_conduc 
                     FROM   tab_despac_despac a, 
                            tab_despac_vehige b,
                            tab_vehicu_vehicu c 
                    WHERE   a.num_despac = b.num_despac AND
                            b.num_placax = c.num_placax AND
                            a.num_despac = '{$mNumDespac}' ";      
      $mData = TraficoPad::Consulta( $mQuery, true);        
      return $mData[0];       
    }

    function getDataVehicu( $mNumPlacax = NULL)
    {
        $mQuery = "SELECT a.num_placax, a.cod_marcax, a.cod_lineax, a.ano_modelo,
                          a.cod_colorx, a.cod_carroc, a.num_motorx, a.num_seriex,
                          a.num_chasis, a.val_pesove, a.val_capaci, a.reg_nalcar,
                          a.num_poliza, a.nom_asesoa, a.cod_ciusoa, a.fec_vigfin,
                          a.ano_repote, a.num_config, a.cod_propie, a.cod_tenedo,
                          a.cod_conduc, a.nom_vincul, a.num_tarpro, a.num_tarope,
                          a.cod_califi, a.fec_vigvin, a.num_polirc, a.fec_venprc,
                          a.cod_aseprc, 
                          a.dir_fotfre,
                          a.dir_fotizq,
                          a.dir_fotder,
                          a.dir_fotpos,
                          a.cod_tipveh, a.ind_chelis, a.fec_revmec,
                          a.num_agases, a.fec_vengas, a.obs_vehicu, a.ind_estado,
                          a.obs_estado, a.cod_paisxx 
                     FROM tab_vehicu_vehicu a  
                    WHERE num_placax = '{$mNumPlacax}' ";         
        $mData = TraficoPad::Consulta( $mQuery, true);        
        return $mData[0];       
    }

    function getDataTrayle( $mNumTrayle = NULL )
    {
        $mQuery = "SELECT a.num_trayle, a.cod_marcax, a.cod_colore, a.dir_fottra,
                          a.cod_carroc, a.ano_modelo, a.nro_ejes, a.ser_chasis,
                          a.tra_anchox, a.tra_altoxx, a.tra_largox, a.tra_volpos,
                          a.tra_pesoxx, a.tra_capaci, a.tip_tramit, a.nom_propie,
                          a.cod_config, a.ind_estado 
                     FROM tab_vehige_trayle a  
                    WHERE num_trayle = '{$mNumTrayle}' ";         
        $mData = TraficoPad::Consulta( $mQuery, true);        
        return $mData[0];       
    }

    function getDataTercero($mType = NULL, $mCodTercer = NULL)
    {
        $mQuery = "SELECT a.cod_tercer, a.num_verifi, a.cod_tipdoc, a.cod_terreg,
                        a.nom_apell1, a.nom_apell2, a.nom_tercer, a.abr_tercer,
                        a.dir_domici, a.num_telef1, a.num_telef2, a.num_telmov,
                        a.num_faxxxx, a.cod_paisxx, a.cod_depart, a.cod_ciudad,
                        a.dir_emailx, a.dir_urlweb, a.cod_estado, a.dir_ultfot,
                        a.obs_tercer 
                   FROM tab_tercer_tercer a  
                  WHERE a.cod_tercer = '{$mCodTercer}' ";         
        $mData = TraficoPad::Consulta( $mQuery, true);        
        return $mData[0];
    }

    function getDataConduc($mCodConduc = NULL)
    {      

        $mQuery = "SELECT a.cod_tercer, a.cod_tipsex, a.cod_grupsa, a.num_licenc,
                        a.num_catlic, a.fec_venlic, a.cod_califi, a.num_libtri,
                        a.fec_ventri, a.obs_habili, a.nom_epsxxx, a.nom_arpxxx,
                        a.nom_pensio, a.num_pasado, a.fec_venpas, a.nom_refper,
                        a.tel_refper, a.obs_conduc, a.cod_operad  
                   FROM tab_tercer_conduc a  
                  WHERE a.cod_tercer = '{$mCodConduc}' ";         
        $mData = TraficoPad::Consulta( $mQuery, true);        
        return $mData[0];
    }

    function getBinFoto($mType = NULL, $mPosicion = NULL, $mDirUrlFot = NULL  )
    {
        switch ($mType) 
        {
          case 'vehiculo':      
                 if( file_exists($_SERVER["DOCUMENT_ROOT"]."/ap/satt_faro/fotveh/".$mDirUrlFot)){
                  return base64_encode(file_get_contents($_SERVER["DOCUMENT_ROOT"]."/ap/satt_faro/fotveh/".$mDirUrlFot) );
                 }
          break;
          case 'trayler':
                if( file_exists($_SERVER["DOCUMENT_ROOT"]."/ap/satt_faro/remolque/".$mDirUrlFot)){
                  return base64_encode(file_get_contents($_SERVER["DOCUMENT_ROOT"]."/ap/satt_faro/remolque/".$mDirUrlFot) );
                 }
          break; 
          case 'conductor':
                if( file_exists($_SERVER["DOCUMENT_ROOT"]."/ap/satt_faro/fotcon/".$mDirUrlFot)){
                  return base64_encode(file_get_contents($_SERVER["DOCUMENT_ROOT"]."/ap/satt_faro/fotcon/".$mDirUrlFot) );
                 }
          break;         
          
        }
    }

    # -----------------------------------------------------------------------------------------------------------------------------------------------------------
    # -----------------------------------------------------------------------------------------------------------------------------------------------------------
    # Funcion que inserta Los datos de los recursos -------------------------------------------------------------------------------------------------------------
    # -----------------------------------------------------------------------------------------------------------------------------------------------------------
    function SetDataRecursos()
    {
      //echo "<pre>"; print_r($_SERVER); echo "</pre>";
      # Datos del Despacho -------------------------------------------------------------------------------------------------------------------------
      $mDespac = TraficoPad::getDataDespac( $_POST["numDespac"]);
      # Datos del vehiculo -------------------------------------------------------------------------------------------------------------------------
      $mVehicu = TraficoPad::getDataVehicu( $mDespac["num_placax"] );
      $mVehicu["bin_fotfre"] = $mVehicu["dir_fotfre"] ? TraficoPad::getBinFoto( 'vehiculo', 'frentexxx',$mVehicu["dir_fotfre"] ) : '';
      $mVehicu["bin_fotizq"] = $mVehicu["dir_fotizq"] ? TraficoPad::getBinFoto( 'vehiculo', 'izquierda',$mVehicu["dir_fotizq"] ) : '';
      $mVehicu["bin_fotder"] = $mVehicu["dir_fotder"] ? TraficoPad::getBinFoto( 'vehiculo', 'derechaxx',$mVehicu["dir_fotder"] ) : '';
      $mVehicu["bin_fotpos"] = $mVehicu["dir_fotpos"] ? TraficoPad::getBinFoto( 'vehiculo', 'porterior',$mVehicu["dir_fotpos"] ) : '';
      
      # Datos del trayler --------------------------------------------------------------------------------------------------------------------------
      if( $mDespac["num_trayle"] ) {
        $mtrayle = TraficoPad::getDataTrayle( $mDespac["num_trayle"]); 
        $mtrayle["bin_fottra"] = $mtrayle["dir_fottra"] ? TraficoPad::getBinFoto( 'trayler', 'generalx', $mtrayle["dir_fottra"] ) : '';
      }
      # Datos Propietario --------------------------------------------------------------------------------------------------------------------------
      $mTerpro = TraficoPad::getDataTercero('Propietario',$mVehicu["cod_propie"]);      
      
      # Datos Tenedor ------------------------------------------------------------------------------------------------------------------------------
      $mTerten = TraficoPad::getDataTercero('Tenedor',$mVehicu["cod_tenedo"]);      


      # Datos Conductor ----------------------------------------------------------------------------------------------------------------------------
      $mTercon = TraficoPad::getDataTercero('Conductor',$mDespac["cod_conduc"]);  
      $mTercon["bin_ultfot"] = $mTercon["dir_ultfot"] ? TraficoPad::getBinFoto( 'conductor', 'frente', $mTercon["dir_ultfot"] ) : '';
      $mConduc = TraficoPad::getDataConduc($mTercon["cod_tercer"]);
        
      

      # Matriz de Datos ----------------------------------------------------------------------------------------------------------------------------
      $mParam = array(
                        "cod_usuari" => "faro-pad",
                        "clv_passwd" => "13f11366ba",                        
                        "cod_aplica" => "pad",                        
                        "dat_despac" => $mDespac,
                        "dat_vehicu" => $mVehicu,
                        "dat_trayle" => $mtrayle,
                        "dat_terpro" => $mTerpro,
                        "dat_terten" => $mTerten,
                        "dat_tercon" => $mTercon,
                        "dat_conduc" => $mConduc
                     );
        //echo "<pre>"; print_r($mParam); echo "</pre>";
       
      try
      {
        $oSoapClient = new soapclient( $this -> cUrlWsdlA, array( 'encoding'=>'ISO-8859-1' ) );       
        $mReturn = $oSoapClient -> __call( 'setRecursos', $mParam );

        //echo "<pre>"; print_r($mReturn); echo "</pre>";
        $mReturn = explode(";", $mReturn);
        $mCodere = explode(":", $mReturn[0]);
        $mMesage = explode(":", $mReturn[1]);

        
        echo json_encode(array('cod_respon'=>$mCodere[1], 'msg_respon' =>$mMesage[1]));

      }
      catch( SoapFault $e )
      {
        echo '<br/>Hubo un error: '.$e -> getMessage();
      }
    }


    


    # -----------------------------------------------------------------------------------------------------------------------------------------------------------
    # -----------------------------------------------------------------------------------------------------------------------------------------------------------
    # Funcion que inserta Los datos vehiculo Disponible ---------------------------------------------------------------------------------------------------------
    # -----------------------------------------------------------------------------------------------------------------------------------------------------------
    function Processing()
    { 
      try
      {

        $mDespac = TraficoPad::getDataDespac( $_POST["numDespac"] );      
        $parametros = array("inputs" => "num_placax:" . $mDespac["num_placax"] . "; cod_ciuori:" . $mDespac["cod_ciudes"],
                            "aplica" => "pad",
                            "module" => "dispo",
                            "action" => "insert",
                            "cod_usuari" => "faro-pad",
                            "clv_passwd" => "13f11366ba"
                            );
        echo "<pre>";
        print_r($parametros);
        echo "</pre>";
        
        $oSoapClient = new soapclient( $this -> cUrlWsdlA, array( 'encoding'=>'ISO-8859-1' ) );

        //Métodos disponibles en el WS
        $respuesta = $oSoapClient -> __call( 'Processing', $parametros );
        echo "<pre>";
        print_r($respuesta);
        echo "</pre>";
        //$respuesta .= " - num_placax:" . $mDespac[0][1] . "; cod_ciuori:" . $mDespac[0][0]; 
        
       
      }
      catch( SoapFault $e )
      {
        echo '<br/>Hubo un error: '.$e -> getMessage();
      }
      echo "<br/>Fin Prueba";
    }

    function Consulta( $mQuery = NULL, $mRetorna = true)
    {
      $mError = array();
      $mLink = mysql_connect("bd10.intrared.net:3306", "satt_faro", "sattfaro"); 
      if( !$mLink)
      {
        $mError["ID"] = false;
        $mError["Code"] = mysql_errno($mLink );
        $mError["Error"] = mysql_error($mLink );
        return $mError;
      }
      mysql_select_db("satt_faro");

      $mDataArray = array();
      $mCont = 0;
      $mData = mysql_query($mQuery);

      if( $mRetorna ) 
      {
        while ($mRow = mysql_fetch_assoc($mData)) 
        {
          $mDataArray[$mCont++] = $mRow;         
        }
      }

      mysql_close($mLink);
      return $mDataArray;
    }

  }

  /*
  ini_set('display_errors', true);
  error_reporting(E_ALL & ~E_NOTICE);
  $mCreaImg = fopen("imagenprueba/foto1_XVI269.jpg", "x+");

  if( !$mCreaImg )
  {
      echo "No hace Fopen";
  }


  $mImage = base64_decode("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");
  file_put_contents("imagenprueba/foto1_XVI269.jpg", $mImage);

  fclose($mCreaImg);

  chmod("imagenprueba/foto1_XVI269.jpg", 0777);*/
  $mPrueba = new TraficoPad();

?>