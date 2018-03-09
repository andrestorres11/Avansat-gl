<?php

ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);

/*
@Autor: Ing. Christiam Barrera Arango.
@Fecha: Jueves, 1 de Mayo de 2008.
@Clase: DinamicMenu
@Descripción: Crea un menú dinámico sin recarga de la página, sin Ajax y sin consultas DB a partir de un Arbol (Padres-Hijos).
@Bugs: Ninguno comprobado. No hay errores CSS ni Javascripts mostrados por la consola de errores de Firefox.
@Última Modificación: Jueves, 5 de Junio de 2008.
@Nota 1: Despues de instanciar el objeto y despues de invocar todos los métodos almacenar el objeto en la
 variable de sesion: $_SESSION["APP_MENU"], para luego ser cargada en el archivo: style_dinamic_menu.php.
@Nota 2: Cargar el archivo de funciones javascript "dinamic_menu.js" en el head del HTML principal.
*/



Class DinamicMenu {
  var $tree;
  var $services;
  var $cells_width;
  var $cells_height;
  var $menu_height;
  var $path_javascript;
  var $path_server;
  var $path_ajax;
  var $path_images;
  var $workarea;
  var $browser;
  var $cRutScrip  = NULL;
  var $nivel = 0;
  var $conexion;
  
  function setRutaScript( $mRuta )
  {
    $this -> cRutScrip = $mRuta;
  }
  
  function getRutaScript()
  {
    return $this -> cRutScrip;
  }   
  
  function SetBrowser( $browser="Explorer" ) { $this->browser = $browser; }
  
  function SetWorkArea( $workarea="SATWorkAreaDIV", $max=600, $min=400 ) { 
   $this->workarea = Array( "id"=>$workarea, "max"=>$max, "min"=>$min ); 
  }
  
    function SetMenuHeight( $height=500 )   { $this->menu_height = $height; }
    function GetMenuHeight()   { return $this->menu_height; }  

  function SetTree( $tree ) { 
    $this->tree = array_merge( Array( Array( "node"=>"root", "nnode"=>"root", "father"=>"", "nfather"=>"", "url"=>"" ) ), $tree ); 
  }
  function GetTree() { return $this->tree; }

//------------------------------------------------------------------------------------------------------------------------------------------------------

  function Sons( $node="", $matrix=Array(), $mode="normal" )  {
    if ( !$node || sizeof( $matrix )==0 )
      return array();
    else  {
      $temp = Array();
      foreach( $matrix as $row )  {
        if ( $node==$row["father"] )
          if ( $mode=="normal" )
            $temp[] = $row["node"];
          else
            $temp[] = $row;
      }
      return $temp;
    }
  }

  function Inorden( $node, $tree, $level=0, $temp=array() ) {
    $temp[] = array_merge( $node, Array( "level"=>$level ) );
    $sons = $this->Sons( $node["node"], $tree, "complex" );
    $level++;
    foreach( $sons as $son )  {
      $temp = $this->Inorden( $son, $tree, $level, $temp );
    }
    return $temp;
  }

  function Display( $tree, $conexion )   {
      
      $this->conexion = $conexion;
      
      $tree = array_merge( Array( Array( 'node' => 'root', 'nnode' => 'root', 'father' => NULL, 'nfather' => NULL, 'url' => NULL ) ), $tree );
      $this -> tree = $this -> Inorden( $tree[0], $tree );
      $size = sizeof( $this -> tree );
      /*
      echo "<pre>";
      print_r($this -> tree );
      echo "</pre>";
      die();
      */
      $html  = NULL;
      $html .= '<ul>';

      //-------------------------------------------------------------------------------------------------------------
      echo "<ul>";
      echo "  <li>";
      unset($_SESSION['padre']);
      $this -> recorrer_menu( 0 , "" , $this -> tree );
      echo "</ul>";

        $html .= '<input type="hidden" id="NodesID" value="'.$ids.'">';
        $html .= '<input type="hidden" id="MenuMode" value="display">';
        $html .= '<input type="hidden" id="ClientWidthID" value="">';
        $html .= '<input type="hidden" id="ClientHeightID" value="">';
        $html .= '<input type="hidden" id="TableWidthID" value="'.( $this->cells_width + 45 ).'">';
        $html .= '<input type="hidden" id="TableHeightID" value="'.( $this->menu_height + 5 ).'">';
        $html .= '<input type="hidden" id="WorkAreaID" value="'.$this->workarea["id"].'">';
        $html .= '<input type="hidden" id="MaxWidthWorkAreaID" value="'.$this->workarea["max"].'">';
        $html .= '<input type="hidden" id="MinWidthWorkAreaID" value="'.$this->workarea["min"].'">';
        $html .= '<input type="hidden" id="MenuBrowserID" value="'.$this->browser.'">';
  
      echo $html;
      
  }//FIN FUNCION Display
  
    function getRutaServic( $cod_servic = NULL , $nivel = 0 , $rutaScript = NULL )
    {
      //$conexion = $this->conexion;
      //------------------------------------------------------------
      // Sacando el nombre del servicio que se envia como parametro
      //------------------------------------------------------------
      $sql = NULL;
      $sql = 'SELECT
                nom_servic
              FROM
                '.BD_STANDA.'.tab_genera_servic
              WHERE 
                cod_servic = '.$cod_servic.' ';
                
      $consul = new Consulta( $sql, $this->conexion );                     
      $nom_servic = $consul -> ret_matriz( 'a' );
      $nom_servic = $nom_servic[0]['nom_servic'];
      //------------------------------------------------------------
      
      if( $nivel == 1 )
      {
        $ruta = $nom_servic.' > '.$rutaScript;        
      }
      else
      {
        $ruta = $nom_servic ;
      }
      
      $sql = NULL;
      $sql = 'SELECT 
                * 
              FROM
                '.BD_STANDA.'.tab_servic_servic
              WHERE 
                cod_serhij = '.$cod_servic.'';

      $consul = new Consulta( $sql, $this->conexion );
      $padre = $consul -> ret_num_rows();
      
      if( $padre )
      {
        $consul = new Consulta( $sql, $this->conexion );
        $cod_servic = $consul -> ret_matriz( 'a' );
        $cod_servic = $cod_servic[0]['cod_serpad'];
        $this -> getRutaServic( $cod_servic , $nivel = 1 , $ruta );
      }
      else 
      {
        $this -> setRutaScript($ruta);  
      }
                
    }//FIN FUNCION getRutaServic    
  
//FUNCION recorrer_menu
function recorrer_menu( $padre , $nivel_anterior , $_TREE ){
        
        //unset($_SESSION['jass']);    
     
        if( $this -> nivel == 0 ):
            //si la goblal nivel está vacia es que acaba de empezar el ciclo recursivo
        else:
            //según la diferencia de nivel actual con el anterior guardada en $_REQUEST['nivel'] se cierran o abren etiquetas <Li>
            $diferencia = $_TREE[$padre]['level'] - $this -> nivel;
            
            if ( $diferencia == 0 ){
                $_SESSION[BASE_DATOS]['jass'] .=  "</li><li>"; //no ha cambiado de nivel de subfamilia respecto al anterior
            }
            if ( $diferencia == 1 ){
                $_SESSION[BASE_DATOS]['jass'] .=  "<ul><li>"; //ha subido un nivel de subfamilia respecto al anterior
            }
            if ( $diferencia == (-1) ){
                $_SESSION[BASE_DATOS]['jass'] .=  "</li></ul><li>\n"; //ha bajado un nivel de subfamilia respecto al anterior
            }
            if ( $diferencia < (-1) ){
                //baja varios niveles de subfamilia respecto al anterior
                $_SESSION[BASE_DATOS]['jass'] .=  "</li>";
                for( $i = $diferencia; $i < 0; $i++ )
                    $_SESSION[BASE_DATOS]['jass'] .=  "</ul></li>";
                $_SESSION[BASE_DATOS]['jass'] .=  "<li>"; 
            }
        endif;

        //crea el enlace
        if( $_TREE[$padre]['nnode'] != 'root' ){
          $title = $this -> getRutaServic( $_TREE[$padre]['node'] );
          $_SESSION[BASE_DATOS]['jass'] .= '<a id="'.$_TREE[$padre]['node'].'ID" class="servic'.$_TREE[$padre]['level'].'" title="'.$this -> getRutaScript().'" href="#" onclick="javascript:DinamicDisplayer2( this,  \''.$_TREE[$padre]['node'].'\' );">';
          $_SESSION[BASE_DATOS]['jass'] .= ( ( $_TREE[$padre]['nnode'] ) );
          $_SESSION[BASE_DATOS]['jass'] .= '</a>';
        }
        //actualiza $_REQUEST['nivel'] al nivel actual
        $this -> nivel = $_TREE[$padre]['level'];

        echo $_SESSION[BASE_DATOS]['jass'];
        $_SESSION[BASE_DATOS]['jass'] = "";
        



        if( $padre == sizeof($_TREE)-1 ) { die(); } 

        //se llama a si mismo para comprovar quienes son los hijos de la familia actual
        $this -> recorrer_menu( $padre+1 , $this -> tree[$padre]['level'] , $_TREE );
 
}//FIN FUNCION RECORRIDO MENU

}

?>