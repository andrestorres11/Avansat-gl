<?php
 
  die("No esta activa la URL: Hasta el domingo Julio 24.");
 

ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);


define("USUARIO", "satt_faro");
define("CLAVE", "sattfaro");
define("SERVIDOR", "aglbd.intrared.net");
define("BD_STANDA", "satt_standa");
define("BASE_DATOS", "satt_faro");

include( 'general/conexion_lib.inc' );
include( '/var/www/html/ap/satt_standa/lib/general/tabla_lib.inc' );


  /*! \Class: InterfS3Amazon
  *  \brief: Clase encargada de hacer la respectiva conexion a servidor S3 de Amazon
  *  \author: Ing. Nelson Liberato
  *  \date: 09/06/2015   
  *  \param: $cBucket  -  Nombre del bucket
  *  \param: $cUserxx  -  NOmbre usuario de conexion al bucket
  *  \param: $cAcckey  -  Llave publica
  *  \param: $cPrikey  -  Llave Privada
  *  \param: $cConexion  -  Variable de la conexion para la clase en general
  *  \param: $cMsgError  -  Variable de mensaje en caso de error
  *  \return array
  */
  class InterfSimplexityPhp
  {

    private static $cPatron    = array("(\¬)", "(\.)", "(\,)", "(\ )", "(ñ)", "(Ñ)", "(\°)", "(\º)", "(&)", "(Â)", "(\()", "(\))", "(\/)", "(\´)", "(\¤)", "(\Ã)", "(\‘)", "(\ƒ)", "(\â)", "(\€)", "(\˜)", "(\¥)", "(Ò)", "(Í)", "(\É)", "(\Ãƒâ€šÃ‚Â)", "(\·)", "(\ª)", "(\-)", "(\+)", "(\Ó)", "(\ü)", "(\Ü)", "(\é)", "(\;)", "(\¡)", "(\!)", "(\`)", "(\<)", "(\>)", "(\_)", "(\#)", "(\ö)", "(\À)", "(\¿)", "(\Ã±)", "(\±)", "(\*)", "(Ú)", "(\%)", "(\|)", "(\ò)", "(\Ì)", "(\:)", "(\Á)", "(\×)", "(\@)", "(\ )", "(\Ù)", "(\á)", "(\–)", "(\")", "(\È)", "(\])", "(\')", "(\í)", "(\Ç)","(\Nš)","(\‚)", "(\ó)", "(\ )", "(\ )", "(\ï½)", "(\?)" );
    private static $cReemplazo = array(" ", " ", " ", " ", "n", "N", " ", " ", "Y", "", "", "", "", "", "", "", "", "", "", "", "", "", "O", "I", "E", " ", "", "a", " ", " ", "O","U","U", "e", " ", "", "", "", "", "", "", "", "", "A", "", "", "", "", "", "", "", "", "I", "", "A", "", "", " ", "U", "a", " ", "", "E", " ", " ", "i", "", "N"," ", " ", " ", " ", " " , "", ""  );  


    private static $cPatronRemi    = array("(\.)","(\:)", "(\ )", "(\,)", "/\s+/"  );
    private static $cReemplazoRemi = array(""    , ""   , ""    , ""    , ""        );
    var $conexion = null;
 
     
    function __construct(  )
    {  


        if(!$_REQUEST["opcion"]) {
          echo self::ShowLinks()."<br><br>";   
          die();
        } else {
          echo self::ShowLinks()."<br><br>";   
        }
        

        switch ($_REQUEST["opcion"]) {

          case 'cargue':
                $this -> conexion = new Conexion( SERVIDOR, USUARIO, CLAVE, BASE_DATOS );
                 
                include("InterfSimplexityNew.inc");                
                $mSimplexity = new InterfSimplexity( $this -> conexion );

                $mQuery = 'SELECT num_desext, fec_cumcar, nov_cumcar, obs_cumcar  
                           FROM satt_faro.tab_despac_sisext 
                          WHERE fec_cumcar BETWEEN "2015-01-01 00:00:00" AND "2015-01-31 23:59:59" AND fec_cumcar IS NOT NULL LIMIT 10 ';
       

                $mValue["num_desext"] = 'VJ-238853';
                $mValue["fec_cumcar"] = '2015-01-02 08:00:00';
                $mValue["nov_cumcar"] = '255';
                $mValue["obs_cumcar"] = '31 CUMPLE CITA SE PRESENTO A LASS 006 00 ANM prueba a produccion de OET';
               

                $mNoveda = array( "LP", "IP", "IC", "FC", "SP" );
                $mDataSimplexityCargue = array(
                                      "NumeroViaje"=> $mValue["num_desext"],
                                      "CodigoEvento"=>  "SP",
                                      "Fecha"=> date_format(date_create($mValue["fec_cumcar"]), 'Y/m/d H:i:s'),
                                      "CodigoNovedad"=> $mValue["nov_cumcar"],
                                      "DescripcionNovedad"=> $mValue["obs_cumcar"]
                                    );         
                $mCargue = $mSimplexity -> setRegistrarCargue($mDataSimplexityCargue);        
                
                echo "<pre><hr># ".($mCont++)."<br>Datos Cargue:<br>"; print_r( var_export($mDataSimplexityCargue, true) ); echo "</pre>";
                echo "<pre>Resultado Cargue:<br>"; print_r($mCargue); echo "</pre>";
               
            break;

            case 'remesa':
                $this -> conexion = new Conexion( SERVIDOR, USUARIO, CLAVE, BASE_DATOS );
                 
                include("InterfSimplexityNew.inc");
                $mSimplexity = new InterfSimplexity( $this -> conexion );

                /*$mQuery = 'SELECT b.num_despac, a.num_docalt, a.nov_cumdes, a.obs_cumdes, a.fec_cumdes  
                         FROM satt_faro.tab_despac_destin a LEFT JOIN 
                              satt_faro.tab_despac_corona b ON a.num_despac = b.num_dessat 
                        WHERE 
                              a.fec_creaci BETWEEN "2014-10-01 00:00:00" AND "2015-01-01 23:59:59" 
                          AND a.num_docalt LIKE "%RE-%"
                          AND a.fec_cumdes IS NOT NULL 
                          ORDER BY a.fec_creaci ASC LIMIT 2  ';
                $mData = self::cConectionBD( $mQuery );
                */
                
              
                /*
                $mValue["num_despac"] = "VJ-360696";  
                $mValue["num_docalt"] = "RE-330303";
                $mValue["fec_cumdes"] = "2014/10/01 00:00:00";
                $mValue["nov_cumdes"] = "256";
                $mValue["obs_cumdes"] = "34>OK CUMPLE CITA A LAS 6 AM, prueba de pros OET a Astrans";   */                    

                
                $mValue["num_despac"] = "VJ-238850";  
                $mValue["num_docalt"] = "RE-243354";
                $mValue["fec_cumdes"] = "2014/10/01 00:00:00";
                $mValue["nov_cumdes"] = "256";
                $mValue["obs_cumdes"] = "34>OK CUMPLE CITA A LAS 6 AM, prueba de pros OET a Astrans PRUEBA 2 VJ-238850 RE-243354";          
 

            
                $mNoveda = array( "LD", "ID", "FD" );
                $mDataSimplexityCargue = array(
                                      "NumeroViaje"=> $mValue["num_despac"],
                                      "NumeroRemesa"=>$mValue["num_docalt"],
                                      "CodigoEvento"=> "FD" ,
                                      "Fecha"=> date_format(date_create($mValue["fec_cumdes"]), 'Y/m/d H:i:s'),
                                      "CodigoNovedad"=> $mValue["nov_cumdes"],
                                      "DescripcionNovedad"=> $mValue["obs_cumdes"]
                                    );         
                $mCargue = $mSimplexity -> setRegistDesReme($mDataSimplexityCargue);        
                
                echo "<pre><hr># ".($mCont++)."<br>Datos Remesa:<br>"; print_r( var_export($mDataSimplexityCargue, true) ); echo "</pre>";
                echo "<pre>Resultado Remesa:<br>"; print_r($mCargue); echo "</pre>";
              

            break;

            case 'remision':
              $this -> conexion = new Conexion( SERVIDOR, USUARIO, CLAVE, BASE_DATOS );
                 
              include("InterfSimplexityNew.inc");
              $mSimplexity = new InterfSimplexity( $this -> conexion );
             

              /*
              $mQuery = 'SELECT b.num_despac, a.num_docalt, a.num_docume, a.ind_citdes, a.fec_cumdes, a.nov_cumdes, a.obs_cumdes , a.num_despac AS num_dessat
                           FROM satt_faro.tab_despac_destin a, 
                                satt_faro.tab_despac_corona b
                          WHERE a.num_despac = b.num_dessat AND
                                a.fec_cumdes >= "2015-06-10 08:42:00" AND 
                                a.fec_cumdes <= "2015-06-31 23:59:59" AND 
                                a.num_docume NOT LIKE "RE-%" AND 
                                a.num_docalt != ""
                                LIMIT 2   ';

              $mData = self::cConectionBD( $mQuery ); 
              */
            

                /*
                $mValue["num_despac"] = "VJ-391080";
                $mValue["num_docume"] = "C1-5119535";
                $mValue["cod_evento"] = "LD";
                $mValue["fec_cumdes"] = "2015/06/10 20:37:00";
                $mValue["nov_cumdes"] = "256";
                $mValue["obs_cumdes"] = "30 CUMPLIO CITA, prueba de OET porduccion a Astrans";*/

                
                $mValue["num_despac"] = "VJ-238853";
                $mValue["num_docume"] = "RE-135409";
                $mValue["fec_cumdes"] = "2014/10/01 00:00:00";
                $mValue["nov_cumdes"] = "256";
                $mValue["obs_cumdes"] = "Con remision el mismo VJ Julio 6";


              
                $mNoveda = array( "LD", "ID", "FD" );
                $mDataSimplexityRemisi = array(
                                    "NumeroViaje"=> $mValue["num_despac"],                                    
                                    "NumeroRemision"=> @preg_replace( self::$cPatronRemi, self::$cReemplazoRemi, $mValue["num_docume"] ),
                                    "CodigoEvento"=> $mNoveda[ rand(0, 2) ],
                                    "Fecha"=> date_format(date_create($mValue["fec_cumdes"]), 'Y/m/d H:i:s'),
                                    "CodigoNovedad"=> $mValue["nov_cumdes"],
                                    "DescripcionNovedad"=>  @preg_replace( self::$cPatron, self::$cReemplazo, $mValue["obs_cumdes"]  )
                                  );     
                $mRemisi = $mSimplexity -> setRegistDesRemi($mDataSimplexityRemisi);        
                
                echo "<pre><hr># ".($mCont++)."<br>Datos Remisiones:<br>"; print_r( var_export($mDataSimplexityRemisi, true) ); echo "</pre>";
                echo "<pre>Resultado Remision:<br>"; print_r($mRemisi); echo "</pre>";
               
            break;
             
            default:
              echo self::ShowLinks();    
            die( );
            break;
        }
  
        
    }
     

    function ShowLinks()
    {
            $mLinks  = '[ <a href="InterfSimplexity.php?opcion=cargue">Cargue</a> ]';
            $mLinks .= '[ <a href="InterfSimplexity.php?opcion=remesa">Remesa</a> ]';
            $mLinks .= '[ <a href="InterfSimplexity.php?opcion=remision">Remision</a> ]';
            #$mLinks .= '[ <a href="InterfSimplexity.php?opcion=curl">Soap Version2</a> ]';
            return "<center><b>Por favor elija una opcion ( ".$mLinks." )</b></center>";       
    }

    function cConectionBD( $mQuery = NULL )
    {
      try
      {

        $mLink = mysql_connect("aglbd.intrared.net", "satt_faro", "sattfaro");
        if(!$mLink){         
          throw new Exception("Error no se pudo conectar al servidor de datos: ".mysql_error($mLink), 3001);          
        }

        $mBd = mysql_select_db("satt_faro", $mLink);
        if(!$mBd){         
          throw new Exception("Error al seleccionar la BD : ".mysql_error($mLink), 3001);      
        }

        $mExecute = mysql_query($mQuery, $mLink );
        if(!$mExecute){
          throw new Exception("Error de Query : ".mysql_error($mLink), 3001);      
        }

        $mArray = array();
        while ( $mRow = mysql_fetch_array($mExecute, MYSQL_ASSOC)) {
           $mArray[] = $mRow;
        }

        return $mArray;


        
      }
      catch(Exception $e)
      {
          echo "<pre>Error en cConectionBD"; print_r($e); echo "</pre>";
          die();
      }

      return true;
    }
 
  }

  $S3Amazon = new InterfSimplexityPhp();

?>