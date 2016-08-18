<?php

session_start();

//-------------------
ini_set( 'display_error', 1 );
error_reporting( E_ALL );
//-------------------

class Ajax
{
    var $conexion      = NULL;
    var $cNull         = array( array( "", "--" ) );

    function Ajax()
    {
        include_once( "../lib/ajax.inc" );
        $this -> conexion = $AjaxConnection;
        $this -> cPerfil = $_SESSION["datos_usuario"]["cod_perfil"];

//echo base64_decode( $_AJAX['cons'] );

        switch( $_AJAX["Case"] )
        {
            case "list_trayle" :
              echo "<h4>LISTADO DE REMOLQUES</h4>";
              $Traylers = $this -> GetTrayle( $_AJAX["cod_trayle"] );
              //----------------------------------------------------------------------------------------------------------------------------
              $cList = new DinamicList( $this -> conexion, $Traylers , 4 );
              $cList -> SetHeader( "Remolque", "field:a.num_trayle; type:link; onclick:SetTrayle()" );
              $cList -> SetHeader( "Marca", "field:b.nom_martra" );
              $cList -> SetHeader( "Modelo", "field:a.ano_modelo" );
              $cList -> SetHeader( "Configuraci&oacute;n", "field:c.nom_config" );
              $cList -> SetHeader( "Propietario", "field:a.nom_propie" );
              $cList -> SetHeader( "Creado por", "field:a.usr_creaci" );
              $cList -> SetHeader( "Fecha", "field:fec_creaci" );
              $cList -> SetClose( "yes" );
              $cList -> Display( $this -> conexion );
              echo $cList -> GetHtml();
              $_SESSION["DINAMIC_LIST"]   = $cList;
            break; 
            
            case "get_trayle" :
              
              $mData = array( 
                              array( "text", "traylerID", $_AJAX['cod_trayle'], NULL ) 
                            );

              $xml = new Xml( $mData );
            break;      
            
            case "list_tenedo" :
              echo "<h4>LISTADO DE TENEDORES</h4>";

              //$Tenedo = $this -> GetListado( '3' );   //EL PARAMETRO DEBIERA SER '3' o '18'
              //$Tenedo = $this -> GetListadoTenedor();
              $Tenedo = base64_decode( $_AJAX['cons'] );
              
              //----------------------------------------------------------------------------------------------------------------------------
              $cList = new DinamicList( $this -> conexion, $Tenedo , 4 );
              $cList -> SetHeader( "Nit o CC", "field:a.cod_tercer; type:link; onclick:SetListado( 'tenedo' )" );
              $cList -> SetHeader( "Nombre", "field:a.abr_tercer" );
              $cList -> SetHeader( "Abreviatura", "field:a.abr_tercer" );
              $cList -> SetHeader( "Telefono", "field:a.num_telmov" );
              $cList -> SetHeader( "Direccion", "field:a.dir_domici" );
              $cList -> SetClose( "yes" );
              $cList -> Display( $this -> conexion );
              echo $cList -> GetHtml();
              $_SESSION["DINAMIC_LIST"]   = $cList;
            break;   
            
            case "get_tenedo" :
              
              $mData = array( 
                              array( "text", "tenedo" ,  $_AJAX['cod_tercer'], NULL ) ,
                              array( "text", "nomtene", trim($_AJAX['nom_tercer']), NULL )
                            );
                            
              $xml = new Xml( $mData );
            break;               
            
            case "list_propie" :
              echo "<h4>LISTADO DE PROPIETARIOS</h4>";
              //$Tenedo = $this -> GetListado( '15' );
              $Tenedo = base64_decode( $_AJAX['cons'] );
              //----------------------------------------------------------------------------------------------------------------------------
              $cList = new DinamicList( $this -> conexion, $Tenedo , 3 );
              $cList -> SetHeader( "Nit o CC", "field:a.cod_tercer; type:link; onclick:SetListado( 'propie' )" );
              $cList -> SetHeader( "Nombre", "field:a.abr_tercer" );
              $cList -> SetHeader( "Abreviatura", "field:a.abr_tercer" );
              $cList -> SetHeader( "Telefono", "field:a.num_telef1" );
              $cList -> SetHeader( "Direccion", "field:a.dir_domici" );
              $cList -> SetClose( "yes" );
              $cList -> Display( $this -> conexion );
              echo $cList -> GetHtml();
              $_SESSION["DINAMIC_LIST"]   = $cList;
            break;  
            
            case "get_propie" :
                            
              $mData = array( 
                              array( "text", "propie" ,  $_AJAX['cod_tercer'], NULL ) ,
                              array( "text", "nomprop", trim($_AJAX['nom_tercer']), NULL )
                            );   
                            
              $xml = new Xml( $mData );
            break;                              
            
            case "list_conduc" :
              echo "<h4>LISTADO DE CONDUCTORES</h4>";
              //$Conduc = $this -> GetListado( '16' );
              $Conduc = base64_decode( $_AJAX['cons'] );
              //----------------------------------------------------------------------------------------------------------------------------
              $cList = new DinamicList( $this -> conexion, $Conduc , 3 );
              $cList -> SetHeader( "Nit o CC", "field:a.cod_tercer; type:link; onclick:SetListado( 'conduc' )" );
              $cList -> SetHeader( "Nombre", "field:a.abr_tercer" );
              $cList -> SetHeader( "Abreviatura", "field:a.abr_tercer" );
              $cList -> SetHeader( "Telefono", "field:a.num_telef1" );
              $cList -> SetHeader( "Direccion", "field:a.dir_domici" );
              $cList -> SetClose( "yes" );
              $cList -> Display( $this -> conexion );
              echo $cList -> GetHtml();
              $_SESSION["DINAMIC_LIST"]   = $cList;
            break;                                  
            
            case "get_conduc" :
              
              $mData = array( 
                              array( "text", "conduc", $_AJAX['cod_tercer'], NULL  )  ,  
                              array( "text", "nomcond", trim($_AJAX['nom_tercer']), NULL )
                            );  

              $xml = new Xml( $mData );
            break;    
            
            case "list_vehicu" :
              echo "<h4>LISTADO DE VEHICULOS</h4>";
              $Vehicu = $this -> GetVehicu();
              //----------------------------------------------------------------------------------------------------------------------------
              $cList = new DinamicList( $this -> conexion, $Vehicu , 4 );

              $cList -> SetHeader( "Placa", "field:a.cod_tercer; type:link; onclick:SetPlacaConten()" );
              $cList -> SetHeader( "C.C. Tenedor", "field:" );
              $cList -> SetHeader( "Tenedor", "field:" );
              $cList -> SetHeader( "Telefono", "field:" );
              $cList -> SetHeader( "C.C. Propietario", "field:" );
              $cList -> SetHeader( "Propietario", "field:" );
              $cList -> SetHeader( "C.C. Conduc", "field:" );
              $cList -> SetHeader( "Conductor", "field:" );
              $cList -> SetHeader( "No. Licencia", "field:" );
              $cList -> SetHeader( "Celular", "field:" );
              $cList -> SetHeader( "Marca", "field:" );
              $cList -> SetHeader( "Linea", "field:" );
              $cList -> SetHeader( "T.Vinculacion", "field:" );
              $cList -> SetHeader( "Capacidad", "field:" );
              $cList -> SetHeader( "Peso Vacio", "field:" );
              $cList -> SetHeader( "Color", "field:" );
              $cList -> SetHeader( "Carroceria", "field:" );
              $cList -> SetHeader( "Modelo", "field:" );
              $cList -> SetHeader( "Config.", "field:" );
              $cList -> SetHeader( "Repotenciado", "field:" );
              $cList -> SetHeader( "No. de Motor", "field:" );
              $cList -> SetHeader( "No. de Chasis", "field:" );
              $cList -> SetHeader( "Insp.Vehicular", "field:" );
              $cList -> SetHeader( "Fec.Insp.Vehicular", "field:" );
              $cList -> SetHeader( "Rev. Mecanica y Gases", "field:" );
              $cList -> SetHeader( "Fec.Rev.Mecanica Y Gases", "field:" );
              $cList -> SetHeader( "A.SOAT", "field:" );
              $cList -> SetHeader( "Fecha", "field:" );
              $cList -> SetHeader( "Operador GPS", "field:" );
              $cList -> SetHeader( "ID GPS", "field:" );
              $cList -> SetHeader( "Organizacion de Transito", "field:" );
              $cList -> SetHeader( "No de certificado aduana", "field:" );
              $cList -> SetHeader( "Ingreso al servicio Pub.", "field:" );
              $cList -> SetHeader( "Creado por", "field:" );                            
              $cList -> SetHeader( "Fecha Creacion", "field:" );

              $cList -> SetClose( "yes" );
              $cList -> Display( $this -> conexion );
              echo $cList -> GetHtml();
              $_SESSION["DINAMIC_LIST"]   = $cList;
            break;                                  
            
            
            case "list_vehicu_remath" :
              echo "<h4>LISTADO DE VEHICULOS</h4>";
              $Vehicu = $this -> GetVehicu();
              //----------------------------------------------------------------------------------------------------------------------------
              $cList = new DinamicList( $this -> conexion, $Vehicu , 4 );

              $cList -> SetHeader( "Placa", "field:a.cod_tercer; type:link; onclick:setPlacaSemima()" );
              $cList -> SetHeader( "C.C. Tenedor", "field:" );
              $cList -> SetHeader( "Tenedor", "field:" );
              $cList -> SetHeader( "Telefono", "field:" );
              $cList -> SetHeader( "C.C. Propietario", "field:" );
              $cList -> SetHeader( "Propietario", "field:" );
              $cList -> SetHeader( "C.C. Conduc", "field:" );
              $cList -> SetHeader( "Conductor", "field:" );
              $cList -> SetHeader( "No. Licencia", "field:" );
              $cList -> SetHeader( "Celular", "field:" );
              $cList -> SetHeader( "Marca", "field:" );
              $cList -> SetHeader( "Linea", "field:" );
              $cList -> SetHeader( "T.Vinculacion", "field:" );
              $cList -> SetHeader( "Capacidad", "field:" );
              $cList -> SetHeader( "Peso Vacio", "field:" );
              $cList -> SetHeader( "Color", "field:" );
              $cList -> SetHeader( "Carroceria", "field:" );
              $cList -> SetHeader( "Modelo", "field:" );
              $cList -> SetHeader( "Config.", "field:" );
              $cList -> SetHeader( "Repotenciado", "field:" );
              $cList -> SetHeader( "No. de Motor", "field:" );
              $cList -> SetHeader( "No. de Chasis", "field:" );
              $cList -> SetHeader( "Insp.Vehicular", "field:" );
              $cList -> SetHeader( "Fec.Insp.Vehicular", "field:" );
              $cList -> SetHeader( "Rev. Mecanica y Gases", "field:" );
              $cList -> SetHeader( "Fec.Rev.Mecanica Y Gases", "field:" );
              $cList -> SetHeader( "A.SOAT", "field:" );
              $cList -> SetHeader( "Fecha", "field:" );
              $cList -> SetHeader( "Operador GPS", "field:" );
              $cList -> SetHeader( "ID GPS", "field:" );
              $cList -> SetHeader( "Organizacion de Transito", "field:" );
              $cList -> SetHeader( "No de certificado aduana", "field:" );
              $cList -> SetHeader( "Ingreso al servicio Pub.", "field:" );
              $cList -> SetHeader( "Creado por", "field:" );                            
              $cList -> SetHeader( "Fecha Creacion", "field:" );

              $cList -> SetClose( "yes" );
              $cList -> Display( $this -> conexion );
              echo $cList -> GetHtml();
              $_SESSION["DINAMIC_LIST"]   = $cList;
            break;            
            
            case "get_vehcon" :

              $mData = array( 
                              array( "text", "placa", $_AJAX['placa'], NULL  )
                            );  

              $xml = new Xml( $mData );
            break;                                               
            
        }//FIN SWITCH   

    }//FIN CONSTRUCTOR

    
    //------------------------------------------------------------------------------------------------------------------
    //OBTIENE EL LISTADO DE LOS VEHICULOS
    //------------------------------------------------------------------------------------------------------------------
    private function GetVehicu( $cod_vehicu = NULL )
    {
      
      //lista los vehiculos existentes
      $mSql = "SELECT 
                     a.num_placax, a.cod_tenedo, g.abr_tercer,
                     g.num_telef1, a.cod_propie, k.abr_tercer AS nom_propie,
                     a.cod_conduc, h.abr_tercer, z.num_licenc,
                     h.num_telmov, b.nom_marcax, c.nom_lineax,
                     m.nom_tipveh, a.val_capaci, a.val_pesove,
                     d.nom_colorx, e.nom_carroc, a.ano_modelo,
                     i.num_config, DATE_FORMAT( a.fec_creaci, '%d-%m-%Y' ), a.num_motorx,
                     a.num_chasis, a.ins_vehicu, a.fec_insveh,
                     a.num_agases, a.fec_revmec, o.abr_tercer AS nom_asesoa,
                     a.fec_vigfin, n.nom_operad, a.idx_gpsxxx,
                     a.nom_orgtra, a.num_ceradu, a.nom_foring,
                     a.usr_creaci, DATE_FORMAT( a.fec_creaci, '%d-%m-%Y' )
                FROM 
                     ".BASE_DATOS.".tab_tercer_tercer g,
                     ".BASE_DATOS.".tab_tercer_tercer h,
                     ".BASE_DATOS.".tab_tercer_tercer k,
                     ".BASE_DATOS.".tab_genera_tipveh m,
                     ".BASE_DATOS.".tab_tercer_tercer o,
                     ".BASE_DATOS.".tab_vehicu_vehicu a 
                     LEFT JOIN ".BASE_DATOS.".tab_genera_marcas b
                     ON a.cod_marcax = b.cod_marcax 
                     LEFT JOIN ".BASE_DATOS.".tab_vehige_carroc e 
                     ON a.cod_carroc = e.cod_carroc 
                     LEFT JOIN ".BASE_DATOS.".tab_vehige_colore d 
                     ON a.cod_colorx = d.cod_colorx 
                     LEFT JOIN ".BASE_DATOS.".tab_vehige_lineas c 
                     ON a.cod_marcax = c.cod_marcax AND a.cod_lineax = c.cod_lineax
                     LEFT JOIN ".BASE_DATOS.".tab_vehige_config i 
                     ON a.num_config = i.num_config
                     LEFT JOIN ".BASE_DATOS.".tab_tercer_conduc z 
                     ON a.cod_conduc = z.cod_tercer
                     LEFT JOIN ".BD_STANDA.".tab_genera_opegps n 
                     ON a.cod_opegps = n.cod_operad
                WHERE 
                     a.cod_propie = k.cod_tercer AND
                     a.cod_tenedo = g.cod_tercer AND
                     a.cod_conduc = h.cod_tercer AND
                     a.cod_asesoa = o.cod_tercer AND
                     a.cod_tipveh = m.cod_tipveh ";   
                    
      //echo $mSql;
      return $mSql;
        
    }//FIN FUNCION GetVehicu 

    //------------------------------------------------------------------------------------------------------------------
    //OBTIENE EL LISTADO DE LOS TRAYLERS
    //------------------------------------------------------------------------------------------------------------------
    private function GetTrayle( $cod_trayle = NULL )
    {
      
      //lista los traylers existentes
      $mSql = "SELECT 
                    a.num_trayle , b.nom_martra , a.ano_modelo , 
                    c.nom_config , a.nom_propie , a.usr_creaci , 
                    DATE_FORMAT( a.fec_creaci , '%d-%m-%Y' ) AS fec_creaci
               FROM    
                    ".BASE_DATOS.".tab_vehige_trayle a LEFT JOIN ".BD_STANDA.".tab_config_remolq c 
                    ON a.cod_config = c.cod_config LEFT JOIN ".BD_STANDA.".tab_minist_martra b 
                    ON a.cod_marcax = b.cod_martra   
               WHERE    
                    a.num_trayle LIKE '%$GLOBALS[trayle]%' ";    
                    
      //echo $mSql;
      return $mSql;                    
      
    }//FIN FUNCION GetTrayle
    
    //------------------------------------------------------------------------------------------------------------------
    //OBTIENE EL LISTADO DE LOS TERCEROS SEGUN LA ACTIVIDAD QUE SE ENVIA POR PARAMETRO
    //------------------------------------------------------------------------------------------------------------------
    private function GetListado( $cod_activi = NULL )
    {
      
      //lista los tenedores
      
      $mSql = "SELECT 
                   a.cod_tercer , a.nom_tercer , a.abr_tercer , 
                   a.num_telef1 , b.nom_ciudad , a.dir_domici , 
                   a.usr_creaci , DATE_FORMAT(a.fec_creaci,'%d-%m-%Y') , a.cod_estado , 
                   IF( IF( DATEDIFF( NOW( ) , d.fec_arpxxx ) IS NULL , '32', DATEDIFF( NOW( ) , d.fec_arpxxx ) ) < 30, 'Vigente', 'Vencido' ) AS fec_arpxxx
               FROM 
                   ".BASE_DATOS.".tab_tercer_tercer a LEFT JOIN ".BASE_DATOS.".tab_tercer_conduc d 
                   ON a.cod_tercer = d.cod_tercer ,
                   ".BASE_DATOS.".tab_genera_ciudad b ,
                   ".BASE_DATOS.".tab_tercer_activi c 
               WHERE 
                   a.cod_ciudad = b.cod_ciudad
                   AND a.cod_paisxx = b.cod_paisxx
                   AND a.cod_depart = b.cod_depart
                   AND a.cod_tercer = c.cod_tercer
                   AND c.cod_activi = ".$cod_activi." ";    
                   
      if ( !$this -> VerifyTercerInhabi() )
      {
        $mSql .= "AND a.cod_estado = '1' ";                     
      }
      
      //echo $mSql;
      return $mSql;                    
      
    }//FIN FUNCION GetListado    
    
    //------------------------------------------------------------------------------------------------------------------
    //OBTIENE EL LISTADO DE LOS TERCEROS TENEDORES Y POSEEDORES
    //------------------------------------------------------------------------------------------------------------------
    private function GetListadoTenedor()
    {
      
      //lista los tenedores
      
      $mSql = "SELECT 
                   a.cod_tercer , a.nom_tercer , a.abr_tercer , 
                   a.num_telef1 , b.nom_ciudad , a.dir_domici , 
                   a.usr_creaci , DATE_FORMAT(a.fec_creaci,'%d-%m-%Y') , a.cod_estado , 
                   IF( IF( DATEDIFF( NOW( ) , d.fec_arpxxx ) IS NULL , '32', DATEDIFF( NOW( ) , d.fec_arpxxx ) ) < 30, 'Vigente', 'Vencido' ) AS fec_arpxxx
               FROM 
                   ".BASE_DATOS.".tab_tercer_tercer a LEFT JOIN ".BASE_DATOS.".tab_tercer_conduc d 
                   ON a.cod_tercer = d.cod_tercer ,
                   ".BASE_DATOS.".tab_genera_ciudad b ,
                   ".BASE_DATOS.".tab_tercer_activi c 
               WHERE 
                   a.cod_ciudad = b.cod_ciudad
                   AND a.cod_paisxx = b.cod_paisxx
                   AND a.cod_depart = b.cod_depart
                   AND a.cod_tercer = c.cod_tercer
                   AND ( c.cod_activi = 3  OR  c.cod_activi = 18 )";
                   
      if ( !$this -> VerifyTercerInhabi() )
      {
        $mSql .= "AND a.cod_estado = '1' ";                     
      }
      
      //echo $mSql;
      return $mSql;                    
      
    }//FIN FUNCION GetListado    
    //------------------------------------------------------------------------------------------------------------------
    
    //-------------------------------------------------------------------
    // FUNCION verifica si tiene parametro activo para ingresar solamente
    // Terceros Inhabilitados
    //-------------------------------------------------------------------
    function VerifyTercerInhabi()
    {
        $mSql = "SELECT 
                    a.val_parame
                 FROM 
                    " . BASE_DATOS . ".tab_genera_parame a
                 WHERE 
                    a.nom_parame = 'ind_terinh' 
                    AND a.ind_estado = '1' ";
                    
        $consulta = new Consulta( $mSql, $this -> conexion );
        $matriz = $consulta -> ret_matriz( 'a' );
        return $matriz[0]['val_parame'] == '1' ? TRUE : FALSE;
    }    
    

}//FIN CLASE AJAX

$ajax = new Ajax();

?>