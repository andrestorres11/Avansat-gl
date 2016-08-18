<?php
/*! \file: list_images_despac.php
 *  \brief: Muestra las imagenes del despacho
 *  \author: 
 *  \author: 
 *  \version: 2.0
 *  \date: dia/mes/año
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */
session_start();

/*! \class: FleConcil
 *  \brief: Muestra las imagenes del despacho
 */
class FleConcil
{
    var $conexion;

    function __construct($conexion)
    {
        $this->conexion = $conexion;
        
        switch($_REQUEST[opcion])
        {
            case "1":
              $this->Mostrar();
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
    
    /*! \fn: Prueba
     *  \brief: 
     *  \author: 
     *  \date: dia/mes/año
     *  \date modified: dia/mes/año
     *  \param: 
     *  \return:
     */
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
        $this -> conexion = new Conexion( "bd10.intrared.net", $_SESSION[USUARIO], $_SESSION[CLAVE], $BASE  );
        
        $query = "SELECT a.bin_fotoxx, b.nom_contro, b.cod_contro, a.fec_creaci, a.num_consec, a.bin_fotox2
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

        $mHtml = '';
        $mHtml .= '<table with="100%">';

        $i=1;
        foreach ($mCount as $row) {
            $mHtml .= '<tr><td colspan="2">&nbsp;</td></tr>';
            $mHtml .= '<tr>';
            $mHtml .=    '<th colspan="2" align="center" >'.$i.') '.$row['nom_contro'].'</th>';
            $mHtml .= '</tr>';
            $mHtml .= '<tr>';
            $mHtml .=    '<td>';
            $mHtml .=        '<center><b>Conductor</b></center>';
            $mHtml .=        '<img src="'.$row[bin_fotoxx].'" width="300" height="200" border="2"/>';
            $mHtml .=    '</td>';
            $mHtml .=    '<td>';
            $mHtml .=        '<center><b>Precinto</b></center>';
            $mHtml .=        '<img src="'.$row[bin_fotox2].'" width="300" height="200" border="2"/>';
            $mHtml .=    '</td>';
            $mHtml .= '</tr>';
            $mHtml .= '<tr>';
            $mHtml .=    '<th colspan="2" align="center" >'.date($row['fec_creaci']).'</th>';
            $mHtml .= '</tr>';
            $i++;
        }
        /*
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
        */
        $mHtml .= '</tr>';      
        $mHtml .= '</table>';

        echo $mHtml;
        
        $formulario -> nueva_tabla();
        $formulario -> botoni("Cerrar","ClosePopup()",1);
        
        $formulario -> cerrar();
    }
    
    /*! \fn: Mostrar
     *  \brief: Muestra las imagenes del despacho
     *  \author: 
     *  \date: dia/mes/año
     *  \date modified: 25/05/2015
     *  \param: 
     *  \return:
     */
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
       
        $this -> conexion = new Conexion( "bd10.intrared.net", $_SESSION[USUARIO], $_SESSION[CLAVE], $BASE  );//cod_transp
        
        $query = "SELECT a.bin_fotoxx, b.nom_contro, a.bin_fotox2
                  FROM ".BASE_DATOS.".tab_despac_images a,
                       ".BASE_DATOS.".tab_genera_contro b
                  WHERE a.cod_contro = b.cod_contro AND
                        a.num_despac = '".$_REQUEST['num_despac']."' AND
                        a.cod_contro = '".$_REQUEST['cod_contro']."' AND 
                        a.num_consec = '".$_REQUEST['num_consec']."';
                  
              ";
        $consulta = new Consulta($query, $this -> conexion);
        $mImages = $consulta -> ret_matriz();
        
        #header( "Content-type: image/jpg;");
        #echo $mImages[0]['bin_fotoxx'];
        echo '<img src="'.$mImages[0]['bin_fotoxx'].'" >';
        #echo '<img src="'.$mImages[0]['bin_fotox2'].'" >';
    }

}
//$service = new FleConcil($this->conexion);
$service = new FleConcil($_SESSION['conexion']);
?> 
