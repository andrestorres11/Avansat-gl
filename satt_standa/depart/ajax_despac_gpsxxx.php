<?php
/***********************************************************************************************************
 * @brief Clase para gestión Asíncrona del Modulo de Notas Contables                                       *
 * @class Ajax                                                                                             *
 * @version 0.1                                                                                            *
 * @ultima_modificacion 28 de Enero de 2010                                                                *
 * @author Christiam Barrera Arango                                                                        *
 * @company Intrared.net LTDA                                                                              *
 ***********************************************************************************************************/
ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE); 
class Ajax
{
  var $conexion      = NULL;
  var $cNull         = array( array( NULL, "--" ) );
  var $cPerfil       = NULL;
  var $cUsrApl       = NULL;


  /************************************************************************************************************
   * Metodo Publico Inicializa las variables de la clase y setea los casos de petición                        *               
   * @fn Ajax                                                                                                 *
   * @brief Inicializa las variables de la clase e invoca las acciones provenientes de las opciones de modulo *
   ************************************************************************************************************/
  function Ajax()
  {
    include( "../lib/ajax.inc" );
    include( "../lib/general/constantes.inc" );
    $this -> conexion = $AjaxConnection;
    $this -> cPerfil  = $_SESSION["COD_PERFIL"];
    $this -> cUsrApl  = $_SESSION["datos_usuario"]["cod_usuari"]; 
    
    switch( $_AJAX["Case"] )
    {
      case 'Insert_Data_Despac_GPS' :
        
       
         /* $mUpdateDataGps = $this -> UpdateDataGps( $_AJAX );
          if( $mUpdateDataGps != true)
            $html = "<b><span style='font-size:17px; color:red;'>Ha ocurrido un error en la actualización de los datos del GPS para el despacho ".$_AJAX['num_despac']."</span></b>";
          else*/
    $html = "<b><span style='font-size:17px; color:red;'>Se ha Insertado los datos GPS del despacho: ".$_AJAX['num_despac'].", de manera Exitosa.</span></b>
             <br>
             <input class='crmButton small save' value='Regresar' onclick= 'location.href=\"?window=central&cod_servic=\".$GLOBALS[cod_servic].\"&num_despac=\".$_AJAX['num_despac'].\"\" />
             ";
        
       
        
        echo  $html ;
      
      break;
      
      case 'Update_Data_Despac_GPS' :
        
       $mDataGps = $this -> GetDataDespacGps( $_AJAX['num_despac']);
        
        if($mDataGps == NULL)
        $html = "<b><span style='font-size:17px; color:red;'>No se ha encontrado el despacho ".$_AJAX['num_despac']."en ruta con GPS</span></b>";
        else
        {
          $mUpdateDataGps = $this -> UpdateDataGps( $_AJAX );
          if( $mUpdateDataGps != true)
            $html = "<b><span style='font-size:17px; color:red;'>Ha ocurrido un error en la actualización de los datos del GPS para el despacho ".$_AJAX['num_despac']."</span></b>";
          else
            $html = "<b><span style='font-size:17px; color:red;'>Se ha actualizado los datos del GPS para el Despacho: ".$_AJAX['num_despac'].", de manera Exitosa.</span></b>";
        }
        
        echo  $html ;
      
      break;
    }
  }
  
  // -------------------------------------------------------------------------------------------------------------------------------------------------------
  // ----------------------------------------------------------------------- Functions  --------------------------------------------------------------------
  // -------------------------------------------------------------------------------------------------------------------------------------------------------

  function GetDataDespacGps( $mNumDespac )
  {
   if($mNumDespac != NULL)
      $mQuery = "a.num_despac = '".$mNumDespac."'";
    
    $mQueryDataGps = "SELECT g.num_despac,  
                                 d.num_placax,
                                 g.cod_opegps AS cod_operad,
                                 IF(g.idx_gpsxxx IS NULL , 'NULL', g.idx_gpsxxx) AS idx_gpsxxx , 
                                 g.nom_usrgps AS usr_gpsxxx,  
                                 g.clv_usrgps AS clv_gpsxxx, 
                                 d.cod_transp, a.cod_manifi,
                                 h.nom_operad
                            FROM 
                                 " .BASE_DATOS. ".tab_despac_despac a LEFT JOIN " .BASE_DATOS. ".tab_tercer_tercer n ON n.cod_tercer = a.cod_asegur,
                                 " .BASE_DATOS. ".tab_despac_vehige d,
                                 " .BASE_DATOS. ".tab_tercer_tercer e,
                                 " .BASE_DATOS. ".tab_tercer_tercer f,
                                 " .BASE_DATOS. ".tab_despac_gpsxxx g,
                                 " .CENTRAL.".tab_genera_opegps h
                                 
                           WHERE a.num_despac = d.num_despac AND
                                 d.cod_conduc = e.cod_tercer AND
                                 d.cod_transp = f.cod_tercer AND
                                 a.num_despac = g.num_despac AND
                                 g.cod_opegps = h.cod_operad AND
                                 $mQuery
                                 GROUP BY 1 ORDER BY g.fec_creaci DESC LIMIT 1             
                                 ";
    
    $consulta = new Consulta($mQueryDataGps, $this -> conexion);   
    $mDataGPS = $consulta -> ret_matriz(); 
    return $mDataGPS[0];
  }
  
  function InsertDataGps ( $mData )
  {
    $mCodConsec = "SELEC MAX(cod_consec + 1) FROM ".BASE_DATOS.".tab_despac_gpsxxx  ";
    $consulta = new Consulta($mCodConsec, $this -> conexion); 
    $mMaxCons = $consulta -> ret_matriz(); 
    
    $mQuery = "INSERT ".BASE_DATOS.".tab_despac_gpsxxx 
                      ( num_despac, cod_consec ) 
               VALUES 
                      ()
                      ";
  }
  function UpdateDataGps ( $mData )
  {
    $mQuery = "UPDATE ".BASE_DATOS.".tab_despac_gpsxxx 
                  SET idx_gpsxxx = '".$mData["idx_gpsxxx"]."', cod_opegps = '".$mData["cod_operad"]."', 
                      nom_usrgps = '".$mData["nom_usuari"]."', clv_usrgps = '".base64_encode($mData["clv_usrgps"])."', 
                      usr_modifi = '".$this -> cUsrApl."', fec_modifi = NOW()
                WHERE num_despac = '".$mData["num_despac"]."' ";
    $consulta = new Consulta($mQuery, $this -> conexion);   
    if($consulta != true)
      return false;
    else
      return true; 
  }
  
}

$ajax = new Ajax();
?>