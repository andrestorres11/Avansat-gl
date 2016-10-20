<?php
session_start();

class FleConcil
{
    var $conexion;

    function __construct($conexion)
    {

        $this->conexion = $conexion;
        
        switch($_REQUEST[opcion])
        {
            case "1":
            {
              $this->Mostrar();
            }
            break;
            case "2":
				      $this ->updProto();
            break;
            case "3":
				      $this ->insert();
            break;
            case "4":
				      $this ->delProto();
            break;
            default:
            
            $this->Prueba();
            
            break;
        }
                
    }
    
    
    function Prueba ()
    {
      ini_set('display_errors', true);
        error_reporting(E_ALL & ~E_NOTICE);
        global $HTTP_POST_FILES;
        session_start();
        $BASE = $_SESSION[BASE_DATOS];
        define ('DIR_APLICA_CENTRAL', $_SESSION['DIR_APLICA_CENTRAL']);
        define ('ESTILO', $_SESSION['ESTILO']);
        define ('BASE_DATOS', $_SESSION['BASE_DATOS']);
        include( "../lib/general/conexion_lib.inc" );
        include( "../lib/general/form_lib.inc" );
        include( "../lib/general/tabla_lib.inc" );
        include ("../lib/mensajes_lib.inc");
        $this -> conexion = new Conexion( $_SESSION['HOST'], $_SESSION[USUARIO], $_SESSION[CLAVE], $BASE  );
        
        $query = "SELECT a.bin_fotoxx, b.nom_contro, b.cod_contro, a.fec_creaci, a.num_consec
                  FROM ".BASE_DATOS.".tab_despac_images a,
                       ".BASE_DATOS.".tab_genera_contro b
                  WHERE a.cod_contro = b.cod_contro 
                    AND a.num_despac = '".$_REQUEST['num_despac']."'
                    AND bin_fotoxx !=  ''";
        $consulta = new Consulta($query, $this -> conexion);
        $mCount = $consulta -> ret_matriz();
      
        $formulario = new Formulario ("index.php","post","Fotos Despachos","form\" id=\"formuID");
        $formulario -> nueva_tabla();
        $formulario -> botoni("Cerrar","ClosePopup()",1);
        
        $formulario->linea("Puestos de control", 0, "t");
        echo '<table with="100%">';
        echo '<tr>';
        $mSalto = 1;
        for($m = 0; $m < sizeof($mCount); $m++)
        {
          echo '<td>';
          echo '<center><b>'.$mSalto.') '.$mCount[$m]['nom_contro'].'</b></center>';
          echo '<img src="../satt_standa/protoc/list_images_despac.php?opcion=1&num_despac='.$_REQUEST['num_despac'].'&cod_contro='.$mCount[$m][cod_contro].'&num_consec='.$mCount[$m][num_consec].'" width="300" height="200" border="2"/>';
          echo '<center><b>'.date($mCount[$m]['fec_creaci']).'</b></center>';
          echo '</td>';       
          
          $mSalto++; 
          if(($mSalto - 1) % 2 == 0)          
            echo '</tr>';
        }
        echo '</tr>';      
        echo '</table>';
        
        $formulario -> nueva_tabla();
        $formulario -> botoni("Cerrar","ClosePopup()",1);
        
        $formulario -> cerrar();
    }
    
    function Mostrar ()
    {
        ini_set('display_errors', true);
        error_reporting(E_ALL & ~E_NOTICE);
        global $HTTP_POST_FILES;
        session_start();
        $BASE = $_SESSION[BASE_DATOS];
        define ('DIR_APLICA_CENTRAL', $_SESSION['DIR_APLICA_CENTRAL']);
        define ('ESTILO', $_SESSION['ESTILO']);
        define ('BASE_DATOS', $_SESSION['BASE_DATOS']);
        include( "../lib/general/conexion_lib.inc" );
       
        $this -> conexion = new Conexion( $_SESSION['HOST'], $_SESSION[USUARIO], $_SESSION[CLAVE], $BASE  );//cod_transp
        
        $query = "SELECT a.bin_fotoxx, b.nom_contro
                  FROM ".BASE_DATOS.".tab_despac_images a,
                       ".BASE_DATOS.".tab_genera_contro b
                  WHERE a.cod_contro = b.cod_contro AND
                        a.num_despac = '".$_REQUEST['num_despac']."' AND
                        a.cod_contro = '".$_REQUEST['cod_contro']."' AND 
                        a.num_consec = '".$_REQUEST['num_consec']."';
                  
              ";
        $consulta = new Consulta($query, $this -> conexion);
        $mImages = $consulta -> ret_matriz();
        header( "Content-type: image/jpg;");
        echo $mImages[0]['bin_fotoxx'];
        
    }

}
//$service = new FleConcil($this->conexion);
$service = new FleConcil($_SESSION['conexion']);
?> 
