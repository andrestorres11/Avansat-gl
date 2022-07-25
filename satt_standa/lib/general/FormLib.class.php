<?php
/***********************************************************************************************************
 * esta clase permite generar los tags de los formularios                                                  *
 * @brief esta clase permite la construcion de formularios.                                                *
 * @version 0.1                                                                                            *
 * @ultima_modificacion 23 de febrero de 2009                                                              *
 * @author Christiam Barrera                                                                               *
 * @author Carlos A. Mock-kow M. (Modificaciones framework y paso a php5)                                  *
 ***********************************************************************************************************/


class FormLib  
{
  protected $cHtml      = NULL;
  protected $cHead      = NULL;
  protected $cBody      = NULL;
  protected $cBodyError      = NULL;

  private $cMeta        = array();
  private $cCss         = array();
  private $cCssJq         = array();
  private $cJs          = array();

  private $cTitle       = "UNTITLE";
  private $cFrame       = NULL;
  private $cTab         = 1;
  private $cTags        = 1;

  private $title        = NULL;
  private $browser      = NULL;
  private $menubar      = NULL;
  private $cBodyWidth   = NULL;
  private $cBodyHeight  = NULL;
  private $cAplica      = NULL;

  private $cForm  = NULL;
	private $cTimes = 0;

   /*********************************************************************
   * este metodo retorna el identificador del resultado de la consulta *
   * @fn SetProperties                                                 *
   * @brief retorna el identificador del resultado                     *
   * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function __construct( $mAplica = NULL )
  {

    # valida la inclucion de funcitions.inc
    if(!function_exists("GetUniqueCol")) {
      include_once("functions.inc");
    }

    $this -> browser = $this -> GetBrowser();

    if( NULL !== $mAplica )
    {
      $this -> cAplica = $mAplica;
    }
    else
    {
      $this -> cAplica = DefApl;
    } 
  }

  /*********************************************************************
   * Metodo Publico Asigna un Valor a la variable privada cAplica.     *
   * HTML indentada a 2 espacios.                                      *
   * @fn SetAplica                                                     *
   * @brief Asigna un Valor a la variable privada cValida              *
   *********************************************************************/
  public function SetAplica( $mData = NULL )
  {
    if( NULL !== $mData )
    {
      $this -> cAplica = $mData;
    }
  }

  /*********************************************************************
   * Metodo Publico que retorna el codigo de la Aplicacion.            *
   * HTML indentada a 2 espacios.                                      *
   * @fn GetAplica                                                     *
   * @brief retorna el Valor de la variable cCodSer                    *
   * @return cCodSer                                                   *
   *********************************************************************/
  public function GetAplica()
  {
    return $this -> cAplica;
  }

  /*********************************************************************
   * este metodo retorna el identificador del resultado de la consulta *
   * @fn SetProperties                                                 *
   * @brief retorna el identificador del resultado                     *
   * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function GetBrowser()
  {
    if( stristr( $_SERVER['HTTP_USER_AGENT'], "MSIE" ) )
      return "Explorer";

    if( stristr( $_SERVER['HTTP_USER_AGENT'], "Firefox" ) )
      return "Firefox";

    if( stristr( $_SERVER['HTTP_USER_AGENT'], "Chrome" ) )
      return "Chrome";
  }

  /*********************************************************************
   * este metodo retorna el identificador del resultado de la consulta *
   * @fn SetProperties                                                 *
   * @brief retorna el identificador del resultado                     *
   * @return cIdResultel id del resultado                              *
   *********************************************************************/
  protected function SetProperties( $array, $onfocus="on", $onblur="on" )
  {
    $mReturn = NULL;

    if( 0 === count( $array ) )
      return "";

    if( isset( $array["name"] ) && !isset( $array["id"] ) )
    {
      $array["id"] = $array["name"]."ID";
    }
    elseif ( isset( $array["id"] ) && !isset( $array["name"] ) )
    {
      $array["name"] = $array["id"];
      $array["id"] = $array["name"]."ID";
    }

    if( $array["popup"] )
    {
      $array["ondblclick"] ? $array["ondblclick"] = $array["ondblclick"]."; ".$array["popup"] : $array["ondblclick"] = $array["popup"];
    }
        
    if( $array[""] )
      $array["type"] = "date";
    
     if( $array["calendar"] )
     {
         $array["type"] = "date";
         //$array["onblur"] .= $array["onblur"] ? '; BlurDate( this )' : 'BlurDate( this )';
         $array["onblur"] .= $array["onblur"] ? '; BlurDate( this )' : 'BlurDate( this )';
    }
        
    if( $array["type"] )
    {
      if( strtolower( $array["type"] ) == "decimal" )
        $function = "return DecimalInput( event )";
      elseif( strtolower( $array["type"] ) == "numeric" || strtolower( $array["type"] ) == "date" )
        $function = "return NumericInput( event )";   
       elseif ( strtolower( $array["type"] ) == "alpha" )
          $function = "return AlphaInput( event )"; 
       elseif ( strtolower( $array["type"] ) == "date" )
         $array["onkeyup"] = "CalendarFormat( this ); ".$array["onkeyup"];
       

      if ( $array["onkeypress"] )
        $array["onkeypress"] = $array["onkeypress"]."; ".$function;
      else
        $array["onkeypress"] = $function;
    }
  
  


    if( $array["onkeypress"] )
      $mReturn .= ' onkeypress="'.$array["onkeypress"].'"';

    if( $onfocus=="on" )
    {
      $mReturn .= ' onfocus="this.className=\'campo_texto_on\';';

      if( $array["onfocus"] )
        $mReturn .= ' '.$array["onfocus"].'"';

      $mReturn .= '"';   
    }
    else
      if( $array["onfocus"] )
        $mReturn .= ' onfocus="'.$array["onfocus"].'"';

    if( strtolower( $array["type"] ) == "placa" )
    {
      $array["maxlength"] = "6";
      $array["size"] = "8";
    }
    
    $array["onblur"] = $array['type'] == 'numeric' || $array['type'] == 'decimal' ? 'BlurNumeric( this ); '.$array["onblur"] : $array["onblur"];
    
    if( $onblur=="on" )
    {
      $mReturn .= ' onblur="this.className=\'campo_texto\';';
      if( $array["onblur"] )
        $mReturn .= ' '.$array["onblur"];
        $mReturn .= '"'; 
    }
    else
      if( $array["onblur"] )
         $mReturn .= ' onblur="'.$array["onblur"].'"';

      if( $array["type"] == "decimal" )
         $array["onkeydown"] = "ValidateDecimalFormat( this ); ".$array["onkeydown"];    

      if( $array["<"] )
         $array["onkeyup"] = "LimitNumber( '<', ".$array["<"].", this ); ".$array["onkeyup"];

    if( $array["<="] )
      $array["onkeyup"] = "LimitNumber( '<=', ".$array["<="].", this ); ".$array["onkeyup"];

    if( $array["type"] == "money" )
      $array["onkeyup"] = "puntos( this,this.value.charAt( this.value.length-1 ) ); ".$array["onkeyup"];

    if( $array["limit"] )
      $array["onkeyup"] = "LimitChars( this, ".$array["limit"]." ); ".$array["onkeyup"];
            
    if( strtolower( $array["type"] ) == "placa" )
      $array["onkeyup"] = "ValidatePlacaFormat( this ); ".$array["onkeyup"];
            
        if( strtolower( $array["type"] ) == "date" )
      $array["onkeyup"] = "CalendarFormat( this ); ".$array["onkeyup"];
    

    if( $array["method"] )
      $mReturn .= ' method="'.$array["method"].'"';

    if( $array["action"] )
      $mReturn .= ' action="'.$array["action"].'"';

    if( $array["target"] )
      $mReturn .= ' target="'.$array["target"].'"';

    if( $array["enctype"] )
      $mReturn .= ' enctype="'.$array["enctype"].'"';

    if( $array["multipar"] )
      $mReturn .= ' multipar="yes"';

    if( $array["class"] )
      $mReturn .= ' class="'.$array["class"].'"';

    if( $array["name"] )
      $mReturn .= ' name="'.$array["name"].'"';

    if( $array["obl"] )
      $mReturn .= ' obl="'.$array["obl"].'"';

    if( $array["id"] )
      $mReturn .= ' id="'.$array["id"].'"';
//var_dump( $array["value"] );
    if( $array["value"] !== NULL )
      $mReturn .= ' value="'.$array["value"].'"';

    if( $array["height"] )
      $mReturn .= ' height="'.$array["height"].'"';

    if( $array["format"] )
      $mReturn .= ' format="'.$array["format"].'"';


    if( $array["size"] )
      $mReturn .= ' size="'.$array["size"].'"';

    if( $array["maxlength"] )
      $mReturn .= ' maxlength="'.$array["maxlength"].'"';

    if( $array["minlength"] )
      $mReturn .= ' minlength="'.$array["minlength"].'"';

    if( $array["rowspan"] )
      $mReturn .= ' rowspan="'.$array["rowspan"].'"';

    if( $array["colspan"] )
      $mReturn .= ' colspan="'.$array["colspan"].'"';

    if( $array["cols"] )
      $mReturn .= ' cols="'.$array["cols"].'"';

    if( $array["rows"] )
      $mReturn .= ' rows="'.$array["rows"].'"';

    if( $array["wrap"] )
      $mReturn .= ' wrap="'.$array["wrap"].'"';

    if( $array["for"] )
      $mReturn .= ' for="'.$array["for"].'"';

    if( $array["title"] )
      $mReturn .= ' title="'.$array["title"].'"';

    if( $array["onclick"] )
      $mReturn .= ' onclick="'.$array["onclick"].'"';

    if( $array["ondblclick"] )
      $mReturn .= ' ondblclick="'.$array["ondblclick"].'"';

    if( $array["onchange"] )
      $mReturn .= ' onchange="'.$array["onchange"].'"';

    if( $array["href"] )
      $mReturn .= ' href="'.$array["href"].'"';

    if( $array["src"] )
      $mReturn .= ' src="'.$array["src"].'"';

    if( $array["onkeyup"] )
      $mReturn .= ' onkeyup="'.$array["onkeyup"].'"';

    if( $array["onkeydown"] )
      $mReturn .= ' onkeydown="'.$array["onkeydown"].'"';
        
    if( $array["onmouseover"] )
      $mReturn .= ' onmouseover="'.$array["onmouseover"].'"';

    if( $array["onmouseout"] )
      $mReturn .= ' onmouseout="'.$array["onmouseout"].'"';

    if( $array["onmouseup"] )
      $mReturn .= ' onmouseup="'.$array["onmouseup"].'"';

    if( $array["selected"] )
      $mReturn .= ' selected="selected"';

    if( $array["checked"] )
      $mReturn .= ' checked="checked"';

    if( $array["disabled"] )
      $mReturn .= ' disabled="disabled"';

    if( $array["readonly"] )
      $mReturn .= ' readonly="readonly"';

    if( $array["onload"] )
      $mReturn .= ' onload="'.$array["onload"].'"';

    if( $array["validate"] )
      $mReturn .= ' validate="'.$array["validate"].'"';

    /*if( $array["type2"] )
        $mReturn .= ' type2="'.$array["type2"].'"';
    else
        $mReturn .= ' type2="'.$array["type"].'"';*/

    return $mReturn;
  }

  /*********************************************************************
   * Metodo Privado que retorna la tabulacion estricta del documento   *
   * HTML indentada a 2 espacios.                                      *
   * @fn SetTab                                                        *
   * @brief retorna el identificador del resultado                     *
   * @return cIdResultel id del resultado                              *
  **********************************************************************/
  private function SetTab()
  {
    $str = "\n";
    $tab = $this -> cTab;

    while( $tab > 0 )
    {
      $str .= "  ";
      $tab --;
    }
    unset( $tab );
    return $str;
  }

  /*********************************************************************
   * Metodo Privado que retorna la tabulacion estricta del documento   *
   * HTML indentada a 2 espacios.                                      *
   * @fn SetTab                                                        *
   * @brief retorna el identificador del resultado                     *
   * @return cIdResultel id del resultado                              *
  **********************************************************************/
  public function MakeHtml()
  {
    if( NULL !== $this -> cHead )
    {
      $this -> cHtml .= $this -> cHead;
    }
    else
    {
      if( 0 !== count( $this -> cCss ) || 0 !== count( $this -> cJs ) )
      {
        $this -> CreateHead();
        $this -> cHtml .= $this -> cHead;
      }
    }

    if( NULL !== $this -> cFrame )
    {
      $this -> cHtml .= $this -> cFrame;
    }
    elseif( NULL !== $this -> cBody )
    {
      $this -> cHtml .= $this -> cBody;
    }
/* *
    else
    {
      //$this -> cHtml .= "\n  ";
      //$this -> cHtml .= '<body>';
    }
/* */
    if( NULL !== $this -> cFrame )
    {
      $this -> cHtml .= $this -> CloseFrameset();
    }
    elseif( NULL !== $this -> cBody )
    {
      $this -> cHtml .= $this -> CloseBody();
    }

    if( NULL !== $this -> cHead )
    {
      $this -> cHtml .= "\n";
      $this -> cHtml .= '</html>';
    }

    return $this -> cHtml;
  }

  /*********************************************************************
   * Metodo Privado que retorna la tabulacion estricta del documento   *
   * HTML indentada a 2 espacios.                                      *
   * @fn SetTab                                                        *
   * @brief retorna el identificador del resultado                     *
   * @return cIdResultel id del resultado                              *
  **********************************************************************/
  public function SetBody( $mHtml )
  {
    $this -> cBody .= $mHtml;
  }

  /*********************************************************************
   * Metodo Publico que almacena en un arreglo un archivo css.         *
   * HTML indentada a 2 espacios.                                      *
   * @fn SetTab                                                        *
   * @brief retorna el identificador del resultado                     *
   * @return cIdResultel id del resultado                              *
  **********************************************************************/
  public function SetCss( $mCss = NULL )
  {
    if( NULL !== $mCss )
    {
      if( FALSE !== strpos( $mCss, "." ) )
      {
        $mTmpCss = explode( ".", $mCss );
        array_push( $this -> cCss, $mTmpCss[0] );
      }
      else
      {
        array_push( $this -> cCss, $mCss );
      }
    }
  }

  /*********************************************************************
   * Metodo Publico que almacena en un arreglo un archivo css.         *
   * HTML indentada a 2 espacios.                                      *
   * @fn SetTab                                                        *
   * @brief retorna el identificador del resultado                     *
   * @return cIdResultel id del resultado                              *
  **********************************************************************/
  public function SetCssJq( $mCss = NULL )
  {
    if( NULL !== $mCss )
    {
      if( FALSE !== strpos( $mCss, "." ) )
      {
        $mTmpCss = explode( ".", $mCss );
        if(!file_exists(DirCssJq."/".$mTmpCss[0].".css"))
          $this -> cBodyError .= "No existe el Css: ".$mTmpCss[0]."<br>";
        else
        array_push( $this -> cCssJq, $mTmpCss[0] );
      }
      else
      {
        if(!file_exists(DirCssJq."/".$mCss.".css"))
          $this -> cBodyError .= "No existe el Css: ".$mCss."<br>";
        else
        array_push( $this -> cCssJq, $mCss );
      }
    }
  }

  /*********************************************************************
   * Metodo Publico que retorna el arreglo de archivos css.            *
   * HTML indentada a 2 espacios.                                      *
   * @fn SetTab                                                        *
   * @brief retorna el identificador del resultado                     *
   * @return cIdResultel id del resultado                              *
  **********************************************************************/
  public function GetCss()
  {
    return $this -> cCss;
  }

  /*********************************************************************
   * Metodo Publico que almacena en un arreglo un archivo js.          *
   * HTML indentada a 2 espacios.                                      *
   * @fn SetTab                                                        *
   * @brief retorna el identificador del resultado                     *
   * @return cIdResultel id del resultado                              *
  **********************************************************************/
  public function SetJs( $mJs = NULL )
  {
    if( NULL !== $mJs )
    {
      if( FALSE !== strpos( $mJs, "." ) )
      {
        $mTmpJs = explode( ".", $mJs );

         echo DirJsx."/".$mTmpJs[0].".js<br>";
        if(!file_exists(DirJsx."/".$mTmpJs[0].".js"))
          $this -> cBodyError .= "No existe el JS: ".$mTmpJs[0]."<br>";
        else
          array_push( $this -> cJs, $mTmpJs[0] );
      }
      else
      {

         if(!file_exists(DirJsx."/".$mJs.".js"))
          $this -> cBodyError .= "No existe el JS: ".$mJs."<br>";
        else
        array_push( $this -> cJs, $mJs );       
      }
    }
  }
  
  

  /*********************************************************************
   * Metodo Publico que retorna el arreglo de archivos js.             *
   * HTML indentada a 2 espacios.                                      *
   * @fn SetTab                                                        *
   * @brief retorna el identificador del resultado                     *
   * @return cIdResultel id del resultado                              *
  **********************************************************************/
  public function GetJs()
  {
    return $this -> cJs;
  }

  /*********************************************************************
   * Metodo Publico que retorna el arreglo de archivos js.             *
   * HTML indentada a 2 espacios.                                      *
   * @fn SetTab                                                        *
   * @brief retorna el identificador del resultado                     *
   * @return cIdResultel id del resultado                              *
  **********************************************************************/
  public function SetTitle( $mTitle = NULL )
  {
    if( NULL !== $mTitle )
    {
    $this -> cTitle = $mTitle;
    }
  }

  /*********************************************************************
   * Metodo Publico que retorna el arreglo de archivos js.             *
   * HTML indentada a 2 espacios.                                      *
   * @fn SetTab                                                        *
   * @brief retorna el identificador del resultado                     *
   * @return cIdResultel id del resultado                              *
  **********************************************************************/
  public function CreateHead( $mTitle = NULL )
  {
    if( NULL !== $mTitle )
    {
      $this -> cTitle = $mTitle;
    }

    $this -> cHead .= '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
    $this -> cHead .= "\n";
    $this -> cHead .= '<html>';
    $this -> cHead .= "\n  ";
    $this -> cHead .= '<head>';
    $this -> cHead .= "\n    ";
    $this -> cHead .= '<title>'.$this -> cTitle.'</title>';
    $this -> cHead .= "\n    ";
    $this -> cHead .= '<meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">';


    //Insertar los archivos de Css en la cabecera Html
    if( NULL !== DefCss && "DefCss" !== DefCss )
    {
      $mDefCss = explode( ",", DefCss );

      foreach( $mDefCss as $mCss )
      {
        $this -> cHead .= "\n    ";
        if( file_exists( DirCss."/".$mCss.".css" ) )
        {
          $this -> cHead .= '<link rel="stylesheet" href="'.DirCss."/".$mCss.'.css" type="text/css">';
        }
        elseif( file_exists( DirCss.$mCss.".css" ) )
        {
          $this -> cHead .= '<link rel="stylesheet" href="'.DirCss.$mCss.'.css" type="text/css">';
        }
      }
    }

    foreach( $this -> cCss as $mCss )
    {
      if( FALSE === strpos( DefCss, $mCss ) )
      {
        $this -> cHead .= "\n    ";
        if( file_exists( DirCss."/".$mCss.".css" ) )
        {
          $this -> cHead .= '<link rel="stylesheet" href="'.DirCss."/".$mCss.'.css" type="text/css">';
        }
        elseif( file_exists( DirCss.$mCss.".css" ) )
        {
          $this -> cHead .= '<link rel="stylesheet" href="'.DirCss.$mCss.'.css" type="text/css">';
        }       
      }
    }

    # Incluye Css de Jquery
    foreach( $this -> cCssJq as $mCss )
    {
      if( FALSE === strpos( DefCss, $mCss ) )
      {
        $this -> cHead .= "\n    ";
        if( file_exists( DirCssJq."/".$mCss.".css" ) )
        {
          $this -> cHead .= '<link rel="stylesheet" href="'.DirCssJq."/".$mCss.'.css" type="text/css">';
        }
        elseif( file_exists( DirCssJq.$mCss.".css" ) )
        {
          $this -> cHead .= '<link rel="stylesheet" href="'.DirCssJq.$mCss.'.css" type="text/css">';
        }       
      }
    }

    //Insertar los Archivos Js en la Cabecera Html.
    if( NULL !== DefJsx && "DefJsx" !== DefJsx )
    {
      $mDefJsx = explode( ",", DefJsx );

      foreach( $mDefJsx as $mJs )
      {
        $this -> cHead .= "\n    ";
        if( file_exists( DirJsx."/".$mJs.".js" ) )
        {
          $this -> cHead .= '<script src="'.DirJsx."/".$mJs.'.js" type="text/javascript"></script>';
        }
        elseif( file_exists( DirJsx.$mJs.".js" ) )
        {
          $this -> cHead .= '<script src="'.DirJsx.$mJs.'.js" type="text/javascript"></script>';
        }
      }
    }

    
    foreach( $this -> cJs as $mJs )
    {
      if( FALSE === strpos( DefJsx, $mJs ) )
      {
       
        $this -> cHead .= "\n    ";
        if( file_exists( DirJsx."/".$mJs.".js" ) )
        {
          echo $this -> cHead .= '<script src="'.DirJsx."/".$mJs.'.js" type="text/javascript"></script>';
        }
        elseif( file_exists( DirJsx.$mJs.".js" ) )
        {
          $this -> cHead .= '<script src="'.DirJsx.$mJs.'.js" type="text/javascript"></script>';
        }
      }
    }

    $this -> cHead .= "\n  ";
    $this -> cHead .= '</head>';
  }

  /*********************************************************************
   * Metodo Publico que retorna el arreglo de archivos js.             *
   * HTML indentada a 2 espacios.                                      *
   * @fn SetTab                                                        *
   * @brief retorna el identificador del resultado                     *
   * @return cIdResultel id del resultado                              *
  **********************************************************************/
  public function Body( $mAttributes = NULL )
  {
    if( $this -> browser == "Explorer" )
    {
      $this -> cBodyWidth = ( $_SESSION["ScreenWidth"]-207 );     
    }
    else
    {
      $this -> cBodyWidth = ( $_SESSION["ScreenWidth"]-207 );     
    }
    if ( $this -> browser == "Explorer" )
    {
      $this -> cBodyHeight = ( $_SESSION["ScreenHeight"]-172 );     
    }
    else
    {
      $this -> cBodyHeight = ( $_SESSION["ScreenHeight"]-170 );     
    }

    $mAtt = GetAttributes( $mAttributes );

    
    if( !isset( $mAtt["class"] ) )
    {
      $mAtt["class"] = "StyleBody";
    }

    if ( !isset( $mAtt["menubar"] ) )
    {
      $this -> menubar = "yes";
    }
    else
    {
      $this -> menubar = "no";
    }

    if( "yes" == strtolower( $this -> menubar )  )
    {
      if( isset( $mAtt["onload"] ) )
      {
        $mAtt["onload"] = "MenuOnloadBody(); ".$mAtt["onload"];
      } 
      else
      {
        $mAtt["onload"] = "MenuOnloadBody()";
      }
    }

    $this -> cBody .= "\n  ";
    $this -> cBody .= '<body';
    $this -> cBody .= $this -> SetProperties( $mAtt );
    $this -> cBody .= '>';

    if($this -> cBodyError != '')
    {
      $this -> cBody .= "\n  ";
      $this -> cBody .= '<table width="100%">';
      $this -> cBody .= '<tr>';
      $this -> cBody .= '<td>Error cargando archivos Set - Verificar los siguientes errores';
      $this -> cBody .= '</tr>';
      $this -> cBody .= '<tr>';
      $this -> cBody .= '<td>'.$this -> cBodyError.'</td>';
      $this -> cBody .= '</tr>';
      $this -> cBody .= '</table>';
    }

    if( "yes" == strtolower( $this -> menubar ) )
    {
      $this -> cBody .= "\n    ";
      $this -> cBody .= '<table align="center" width="100%" height="'.$this -> cBodyHeight.'" cellspacing="0" cellpadding="0" border="0" topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">';
      $this -> cBody .= "\n      ";
      $this -> cBody .= '<tr>';
      $this -> cBody .= "\n        ";
      $this -> cBody .= '<td class="StyleMenuBar" id="MenubarID" width="15" height="'.$this -> cBodyHeight.'" align="center" ';
      $this -> cBody .= 'title="Ocultar Menu" ';
      $this -> cBody .= 'onclick="DisplayMenu()" ';
      $this -> cBody .= '>';
      $this -> cBody .= "\n          ";
      $this -> cBody .= '<img id="MenuBarImageID" src="../../images/dinamic_menu/menubar_left.jpg" width="15" height="15">';
      $this -> cBody .= "\n        ";
      $this -> cBody .= '</td>';
      $this -> cBody .= "\n        ";
      $this -> cBody .= '<td align="center" height="'.$this -> cBodyHeight.'">';
            
      $this -> cBody .= "\n          ";
      $this -> cBody .= '<div class="StyleAppCentral" name="AppCentral" id="AppCentralDIV" style="';
      //$this->cBody .= 'border: 1px black solid; ';
      $this -> cBody .= 'display:inline; ';
      $this -> cBody .= 'float:left; ';
      $this -> cBody .= 'overflow-x:scroll; ';
      $this -> cBody .= 'overflow-y:scroll; ';
      $this -> cBody .= 'width:'.$this -> cBodyWidth.'px; ';
      $this -> cBody .= 'height:'.$this -> cBodyHeight.'px;';
      $this -> cBody .= '">';
      $this -> cTab = 5;
    }
  }

  /*********************************************************************
   * Metodo Publico que retorna el arreglo de archivos js.             *
   * HTML indentada a 2 espacios.                                      *
   * @fn SetTab                                                        *
   * @brief retorna el identificador del resultado                     *
   * @return cIdResultel id del resultado                              *
  **********************************************************************/
  public function CloseBody()
  {
    if( "yes" == strtolower( $this -> menubar ) )
    {
      $this -> cBody .= "\n          ";
      $this -> cBody .= '</div>';
      $this -> cBody .= "\n        ";
      $this -> cBody .= '</td>';
      $this -> cBody .= "\n      ";
      $this -> cBody .= '</tr>';
      $this -> cBody .= "\n    ";
      $this -> cBody .= '</table>';
    }

    $this -> cBody .= "\n    ";
    $this -> cBody .= '<div id="AplicationEndDIV"></div>';
    $this -> cBody .= $this -> Transparency();
    $this -> cBody .= "\n  ";
    $this -> cBody .= '</body>';
  }

  /*********************************************************************
   * Metodo Publico que retorna el arreglo de archivos js.             *
   * HTML indentada a 2 espacios.                                      *
   * @fn SetTab                                                        *
   * @brief retorna el identificador del resultado                     *
   * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function Table( $tr1 = "", $tr2 = "", $mReturn = FALSE )
  {
    if( !$tr1 )
    {
      $mForm = "\n  ";
      $mForm .= '<tr>';
      $mForm .= "\n  ";
      $mForm .= '<td align="center" width="100%">';
    }
    $mForm .= "\n  ";
    $mForm .= '<table width="100%" align="center" cellpadding="3" cellspacing="0" border="0">';
    if ( !$tr2 )
    {
      $mForm .= "\n    ";
      $mForm .= '<tr>';
    }

    if( $mReturn )
    {
      return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );
    }
  }

  /*********************************************************************
   * Metodo Publico que retorna el arreglo de archivos js.             *
   * HTML indentada a 2 espacios.                                      *
   * @fn SetTab                                                        *
   * @brief retorna el identificador del resultado                     *
   * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function CloseTable( $tr1 = "", $tr2 = "", $mReturn = FALSE )
  {
    if ( !$tr2 )
    {
      $mForm = "\n    ";
      $mForm .= '</tr>';
    }
    $mForm .= "\n  ";
    $mForm .= '</table>';
    if ( !$tr1 )
    {
      $mForm .= "\n  ";
      $mForm .= '</td>';
      $mForm .= "\n  ";
      $mForm .= '</tr>';
    }

    if( $mReturn )
    {
      return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );
    }
  }

  /*********************************************************************
   * Metodo Publico que retorna el arreglo de archivos js.             *
   * HTML indentada a 2 espacios.                                      *
   * @fn SetTab                                                        *
   * @brief retorna el identificador del resultado                     *
   * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function Row( $td="", $mReturn = FALSE )
  {
    $mForm = "\n  ";
    $mForm .= '<tr>';
    if( $td )
    {
      $mForm .= "\n    ";
      $mForm .= '<td>';
    }

    if( $mReturn )
    {
      return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );
    }
  }

  /*********************************************************************
   * Metodo Publico que retorna el arreglo de archivos js.             *
   * HTML indentada a 2 espacios.                                      *
   * @fn SetTab                                                        *
   * @brief retorna el identificador del resultado                     *
   * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function RowProperties( $td="", $mReturn = FALSE,$properties = "")
  {
    $mForm = "\n  ";
    $mForm .= '<tr '.$properties.'>';
    if( $td )
    {
      $mForm .= "\n    ";
      $mForm .= '<td>';
    }

    if( $mReturn )
    {
      return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );
    }
  }

  /*********************************************************************
   * Metodo Publico que retorna el arreglo de archivos js.             *
   * HTML indentada a 2 espacios.                                      *
   * @fn SetTab                                                        *
   * @brief retorna el identificador del resultado                     *
   * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function CloseRow( $td = "", $mReturn = FALSE )
  {
    if( $td )
    {
      $mForm = "\n    ";
      $mForm .= '</td>';
    }
    $mForm .= "\n  ";
    $mForm .= '</tr>';

    if( $mReturn )
    {
      return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );
    }
  }

  /*********************************************************************
   * Metodo Publico que retorna el arreglo de archivos js.             *
   * HTML indentada a 2 espacios.                                      *
   * @fn SetTab                                                        *
   * @brief retorna el identificador del resultado                     *
   * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function Column( $mReturn = FALSE )
  {
    $mForm = "\n      ";
    $mForm .= '<td>';

    if( $mReturn )
    {
      return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );
    }
  }

  /*********************************************************************
   * Metodo Publico que retorna el arreglo de archivos js.             *
   * HTML indentada a 2 espacios.                                      *
   * @fn SetTab                                                        *
   * @brief retorna el identificador del resultado                     *
   * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function CloseColumn( $mReturn = FALSE )
  {
    $mForm = "\n      ";
    $mForm .= '</td>';

    if( $mReturn )
    {
      return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );
    }
  }

  /*********************************************************************
   * Metodo Publico que retorna el arreglo de archivos js.             *
   * HTML indentada a 2 espacios.                                      *
   * @fn SetTab                                                        *
   * @brief retorna el identificador del resultado                     *
   * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function OpenCell( $id = "", $class = "celda_info", $align = "", $valign = "middle", $rowspan = 1,
                            $colspan = 1, $width = "", $mReturn = TRUE, $height="" )
  {
    if( !$align )
    {
      $align = "right";
    }

    if( $id )
    {
      $id = $id."TD";
    }
    $mForm = "\n      ";
    $mForm .= '<td id="'.$id.'" class="'.$class.'" align="'.$align.'" valign="'.$valign.'" rowspan="'.$rowspan.'" colspan="'.$colspan.'" width="'.$width.'" height="'.$height.'">';
    $mForm .= "\n        ";

    if( $mReturn )
    {
      return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );
    }
  }

  /*********************************************************************
   * Metodo Publico que retorna el arreglo de archivos js.             *
   * HTML indentada a 2 espacios.                                      *
   * @fn SetTab                                                        *
   * @brief retorna el identificador del resultado                     *
   * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function CloseCell( $end="", $mReturn = TRUE )
  {
    $mForm = "\n      ";
    $mForm .= '</td>';
    if ( $end )
    {
      $mForm .= "\n    </tr>";
      $mForm .= "\n    <tr>";
    }

    if( $mReturn )
    {
      return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );
    }
  }

  /*********************************************************************
   * Metodo Publico que retorna el arreglo de archivos js.             *
   * HTML indentada a 2 espacios.                                      *
   * @fn SetTab                                                        *
   * @brief retorna el identificador del resultado                     *
   * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function OpenDiv( $properties = "", $mReturn = FALSE )
  {
    $properties = GetAttributes( $properties );

    $mForm = "\n          ";
    $mForm .= '<div ';
    $mForm .= $this -> SetProperties( $properties, "", "" );
    $mForm .= '>';  

    if( $mReturn )
    {
      return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );
    }
  }

  /*********************************************************************
   * Metodo Publico que retorna el arreglo de archivos js.             *
   * HTML indentada a 2 espacios.                                      *
   * @fn SetTab                                                        *
   * @brief retorna el identificador del resultado                     *
   * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function CloseDiv( $mReturn = FALSE )
  {
    $mForm = "\n          ";
    $mForm .= '</div>';

    if( $mReturn )
    {
      return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );
    }
  }

  /*********************************************************************
   * Metodo Publico que retorna el arreglo de archivos js.             *
   * HTML indentada a 2 espacios.                                      *
   * @fn SetTab                                                        *
   * @brief retorna el identificador del resultado                     *
   * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function Alert( $string="alert() desde Javascript.", $mReturn = FALSE )
  {
    $mForm = "\n  ";
    $mForm .= '<script language="javascript">';
    $mForm .= "alert( '$string' );";
    $mForm .= '</script>';

    if( $mReturn )
    {
      return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );
    }
  }

  /*********************************************************************
   * Metodo Publico que retorna el arreglo de archivos js.             *
   * HTML indentada a 2 espacios.                                      *
   * @fn SetTab                                                        *
   * @brief retorna el identificador del resultado                     *
   * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function Javascript( $string, $mReturn = FALSE )
  {
    $mForm = "\n  ";
    $mForm .= '<script language="javascript">';
    $mForm .= $string;
    $mForm .= '</script>';

    if( $mReturn )
    {
      return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );
    }
  }

  /*********************************************************************
   * Metodo Publico que retorna el arreglo de archivos js.             *
   * HTML indentada a 2 espacios.                                      *
   * @fn SetTab                                                        *
   * @brief retorna el identificador del resultado                     *
   * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function Space( $properties = "", $mReturn = FALSE )
  {
    $properties = GetAttributes( $properties );
    $mForm = $this -> OpenCell( $properties["id"], "celda_etiqueta", $properties["align"], $properties["valign"], $properties["rowspan"], $properties["colspan"], $properties["width"] );
    $mForm .= '<label';
    $mForm .= $this -> SetProperties( $properties );
    $mForm .=  '>';
    $mForm .= "&nbsp;";
    $mForm .= '</label>';
    $mForm .= $this -> CloseCell( $properties["end"] ); 

    if( $mReturn )
    {
      return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );
    }
  }

  /*********************************************************************
   * Metodo Publico que retorna el arreglo de archivos js.             *
   * HTML indentada a 2 espacios.                                      *
   * @fn SetTab                                                        *
   * @brief retorna el identificador del resultado                     *
   * @return cIdResultel id del resultado                              *
   *********************************************************************/
  function Link( $label = "", $properties = "", $mReturn = FALSE )    
  {
    $properties = GetAttributes( $properties );
    $mForm = $this -> OpenCell( $properties["id"], "celda_etiqueta", $properties["align"], $properties["valign"], $properties["rowspan"], $properties["colspan"], $properties["width"] );
    $mForm .= '<a ';
    $mForm .= $this -> SetProperties( $properties );
    $mForm .= '>';
    $mForm .= $label;
    $mForm .= "&nbsp;";
    $mForm .= '</a>';
    $mForm .= $this -> CloseCell( $properties["end"] ); 

    if( $mReturn )
    {
      return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );
    }
  }

  /*********************************************************************
   * Metodo Publico que retorna el arreglo de archivos js.             *
   * HTML indentada a 2 espacios.                                      *
   * @fn SetTab                                                        *
   * @brief retorna el identificador del resultado                     *
   * @return cIdResultel id del resultado                              *
   *********************************************************************/


  /*********************************************************************
   * Metodo Publico que retorna el arreglo de archivos js.             *
   * HTML indentada a 2 espacios.                                      *
   * @fn SetTab                                                        *
   * @brief retorna el identificador del resultado                     *
   * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function Image( $properties = "", $mReturn = FALSE )
  {
    $properties = GetAttributes( $properties );

    $mForm = $this -> OpenCell( $properties["id"], "celda_info", $properties["align"], $properties["valign"], $properties["rowspan"], $properties["colspan"], $properties["width"] );
    $mForm .= '<img width="'.$properties["iwidth"].'" height="'.$properties["iheight"].'" ';
    $mForm .= $this -> SetProperties( $properties, "off", "off" );
    $mForm .= ' />';
    $mForm .= $this -> CloseCell( $properties["end"] ); 

    if( $mReturn )
    {
      return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );
    }
  }

  /*********************************************************************
   * Metodo Publico que retorna el arreglo de archivos js.             *
   * HTML indentada a 2 espacios.                                      *
   * @fn SetTab                                                        *
   * @brief retorna el identificador del resultado                     *
   * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function Line( $te, $est = "", $an = 0, $col = 0, $al = "left", $title = "", $js = "", 
                        $name = "", $mReturn = FALSE ) 
  {
    switch( $est )
    {
      case "line": $estilo_et = "Line"; break;
      case "t": $estilo_et = "celda_titulo"; break;
      case "t2": $estilo_et = "celda_titulo2"; break;
      case "h": $estilo_et = "header"; break;
      case "e": $estilo_et = "celda_error"; break;
      case "c": $estilo_et = "celda"; break;
      case "c2": $estilo_et = "celda2"; break;
      case "i": $estilo_et = "celda_info"; break;
      default: $estilo_et = "celda_etiqueta"; break;
    }

    $mForm = "\n    ";
    $mForm .= '<td class="'.$estilo_et.'" align="'.$al.'"';

    if( !$te )
    {
      $te = "&nbsp;";
    }

    if( $col )
    {
      $mForm .= " colspan=\"$col\"";
    }

    if( $an )
    {
      $mForm .= " width=\"$an\"";
    }

    $mForm .= ">";

    if( $js )
    {
      $mForm .= '<label name="'.$name.'" id="'.$name.'ID" onclick="'.$js.'" title="'.$title.'" onmouseover="this.className=\'celda_hover\';" onmouseout="this.className=\''.$est.'\';" >';
    }

    $mForm .= "<b>".$te."</b>";

    if( $js )
    {
      $mForm .= '</label>';
    }

    $mForm .= "\n    ";
    $mForm .= '</td>';

    if( $mReturn )
    {
      return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );
    }
  }

  /*********************************************************************
   * Metodo Publico que retorna el arreglo de archivos js.             *
   * HTML indentada a 2 espacios.                                      *
   * @fn SetTab                                                        *
   * @brief retorna el identificador del resultado                     *
   * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function Money( $value )
  {
    $size = strlen( $value );
    if( $size > 3 )
    {
      $str = "";
      $rev = strrev( $value );
      $n = 0;
      while( $n < $size )
      {
        $str .= $rev[$n];
        if ( ( $n + 1 ) % 3 == 0 && ( $n + 1 ) < $size )
        {
          $str .= ".";
        }
        $n++;
      }
      return strrev( $str );
    }
    else
      return $value;
  }

	/*********************************************************************
	 * Metodo Publico que retorna el arreglo de archivos js.             *
	 * HTML indentada a 2 espacios.                                      *
	 * @fn SetTab                                                        *
	 * @brief retorna el identificador del resultado                     *
	 * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function Form( $properties="", $ajax="", $mReturn = FALSE )
  {
    $this -> browser = $this -> GetBrowser();
    $properties = GetAttributes( $properties );

    if (!$ajax)
    {
        $mForm = "\n          ";
        /*$mForm .= '<table width="100%" cellpadding="0" cellspacing="0" border="0" >';
        $mForm .= "\n          ";
        $mForm .= '<tr height="20">';
        $mForm .= "\n                  ";
        $mForm .= '<td width="100%" align="center" cellpadding="0" cellspacing="0" border="0" class="titulo1"  >'.$properties["header"].'</td>';
        $mForm .= "\n                  ";
        $mForm .= '</tr>';
        $mForm .= "\n                  ";
        $mForm .= '</table>';
        */
        $mForm .= "\n             ";
    
        $mForm .= '<form';
        $mForm .= $this->SetProperties($properties, "off", "off");
        $mForm .= '>';    
    }

    $mForm .= "\n";
    $mForm .= "\n";
		$mForm .= '<table width="100%" cellpadding="3" cellspacing="0" class="formulario">';

    if( $mReturn )
    {
    	return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );    	
    }
  }

	/*********************************************************************
	 * Metodo Publico que retorna el arreglo de archivos js.             *
	 * HTML indentada a 2 espacios.                                      *
	 * @fn SetTab                                                        *
	 * @brief retorna el identificador del resultado                     *
	 * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function CloseForm($mReturn = FALSE)
  {
      $mForm = '<tr>';
      $mForm .= "\n                  ";
  
      $mForm .= '<td>';
      $mForm .= "\n                    ";
      $mForm .= '<input type="hidden" id="MenuRowHiddenID" value="left" />';
      $mForm .= "\n                  ";
      $mForm .= '</td>';
  
      $mForm .= "\n                ";
      $mForm .= '</tr>';
  
      $mForm .= "\n";
      $mForm .= "</table>";
      $mForm .= "\n";
      $mForm .= "\n                    ";
      $mForm .= "</form>";
      $mForm .= "\n                  ";
      $mForm .= '</td>';
      $mForm .= "\n                ";
      $mForm .= '</tr>';
      $mForm .= "\n              ";
      $mForm .= '</table>';
      $mForm .= "\n              ";
      $mForm .= "\n              ";
      $mForm .= '<div id="AplicationEndDIV"></div>';
      $mForm .= "\n              ";
      $mForm .= "\n            ";
      $mForm .= '</td>';
      $mForm .= "\n          ";
      $mForm .= '</tr>';
  
      if ($mReturn)
      {
          return $mForm;
      }
      else
      {
          $this->SetBody($mForm);
      }
  }

	/*********************************************************************
	 * Metodo Publico que retorna el arreglo de archivos js.             *
	 * HTML indentada a 2 espacios.                                      *
	 * @fn SetTab                                                        *
	 * @brief retorna el identificador del resultado                     *
	 * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function Label( $label = "", $properties = "", $mReturn = FALSE )
  {
    $properties = GetAttributes( $properties );
    if( !$properties["class"] )
    {
      $properties["class"] = "celda_etiqueta";    	
    }
    
    $mForm = $this -> OpenCell( $properties["name"], $properties["class"], $properties["align"], $properties["valign"], $properties["rowspan"], $properties["colspan"], $properties["width"], false, $properties["height"] );
    $properties["class"] = "";
    $mForm .= '<label';
    $mForm .= $this -> SetProperties( $properties );
    $mForm .= '>';

    if( $properties["*"] )
    {
      #$mForm .= '<img src="'.DirImg.'general/asterisco.gif" />';
      $mForm .= '*';
      $mForm .= "&nbsp;";
    }

    if( $properties["error"] )
    {
      $mForm .= '<font color="red">';
    }

    if ( $properties ["exp"] )
    {
      $mForm .= $label;
    }
    else
    {
      $mForm .= $label;
    }

    if( $properties["error"] )
    {
      $mForm .= '</font>';
    }

    $mForm .= '</label>';
    $mForm .= $this -> CloseCell( $properties["end"] ); 

    if( $mReturn )
    {
    	return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );    	
    }
  }
    
	/*********************************************************************
	 * Metodo Publico que retorna el arreglo de archivos js.             *
	 * HTML indentada a 2 espacios.                                      *
	 * @fn SetTab                                                        *
	 * @brief retorna el identificador del resultado                     *
	 * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function Info( $properties="", $mReturn = FALSE )
  {
    $properties = GetAttributes( $properties );

    if( !$properties["align"] )
    {
      $properties["align"] = "left";
    }

      $mForm = $this -> OpenCell( $properties["id"], "celda_info", $properties["align"], $properties["valign"], $properties["rowspan"], $properties["colspan"], $properties["width"] );
        
    $mForm .= '<input type="text" class="Withoutborder" readonly="readonly"';

    if ( $properties["color"] )
    {
      $mForm .= ' style="color:'.$properties["color"].'; font-weight:bold; font-size:11"';
    }
    elseif ( $properties["ialign"] )
    {
      $mForm .= 'style="text-align:'.$properties["ialign"].'"';
    }
    $mForm .= $this -> SetProperties( $properties, "off", "off" );
    $mForm .= ' />';

      $mForm .= $this -> CloseCell( $properties["end"] ); 

    if( $mReturn )
    {
    	return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );    	
    }
  }

  public function aLink( $properties="", $mReturn = FALSE )
  {
    $properties = GetAttributes( $properties );
    if ( !$properties["align"] )
    {
      $properties["align"] = "left";    	
    }
    $mForm = $this -> OpenCell( $properties["id"], "celda_info", $properties["align"], $properties["valign"], $properties["rowspan"], $properties["colspan"], $properties["width"] );
    $mForm .= '<a href="'.$properties[href].'" target="'.$properties[target].'"class=""';
    $mForm .= $this -> SetProperties( $properties, "off", "off" );
    $mForm .= ' />'.$properties[label].'</a>';
    $mForm .= $this -> CloseCell( $properties["end"] ); 

    if( $mReturn )
    {
    	return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );    	
    }
  }

	/*********************************************************************
	 * Metodo Publico que retorna el arreglo de archivos js.             *
	 * HTML indentada a 2 espacios.                                      *
	 * @fn SetTab                                                        *
	 * @brief retorna el identificador del resultado                     *
	 * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function Error( $properties = "", $mReturn = FALSE )
  {
    $properties = GetAttributes( $properties );
    if ( !$properties["align"] )
    {
      $properties["align"] = "left";    	
    }

    $mForm = $this -> OpenCell( $properties["id"], "celda_info", $properties["align"], $properties["valign"], $properties["rowspan"], $properties["colspan"], $properties["width"] );
    $mForm .= '<input type="text" class="Error" readonly="readonly"';

    if ( $properties["color"] )
    {
      $mForm .= ' style="color:'.$properties["color"].'; font-weight:bold; font-size:11"';
    }

    $mForm .= $this -> SetProperties( $properties, "off", "off" );
    $mForm .= ' />';
    $mForm .= $this -> CloseCell( $properties["end"] ); 

    if( $mReturn )
    {
    	return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );    	
    }
  }

	/*********************************************************************
	 * Metodo Publico que retorna el arreglo de archivos js.             *
	 * HTML indentada a 2 espacios.                                      *
	 * @fn SetTab                                                        *
	 * @brief retorna el identificador del resultado                     *
	 * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function Input( $properties = "", $mReturn = FALSE )
  {
    $properties = GetAttributes( $properties );

    if ( !$properties["align"] )
    {
    	$properties["align"] = "left"; 
    }
    $mForm = $this -> OpenCell( $properties["id"], ($properties["class"] ? $properties["class"] : "celda_info"), $properties["align"], $properties["valign"], $properties["rowspan"], $properties["colspan"], $properties["width"] );

    $mForm .= '<input type="text" class="campo_texto"';

    if ( $properties["color"] )
    {
      $mForm .= ' style="color:'.$properties["color"].'; font-weight:bold; font-size:11"';
    }
    elseif ( $properties["ialign"] )
    {
      $mForm .= 'style="text-align:'.$properties["ialign"].'"';
    }

    if( $properties["popup"] )
    {
    	 $mForm .= ' style="cursor:pointer"';
    }
    $mForm .= $this -> SetProperties( $properties );
    $mForm .= ' />';

    if ( $properties["popup"] )
    {
      $mForm .= "\n        ";
      $mForm .= '<input class="popupButton" id="P'.$properties["name"].'ID"  onclick="'.$properties["popup"].'" ';
      $mForm .= ' title="Buscar" ';

      if ( $properties["disabled"] )
      {
        $mForm .= ' disabled="disabled"';
      }
      $mForm .= ' />';
    }
    elseif( $properties["calendar"] )
    {
      $date = date( "Y-m-d" );
      $mForm .= "\n        ";
      $mForm .= '<input id="C'.$properties["name"].'ID" class="calendarButton" type="button" value="" ';
      $mForm .= 'onclick="ShowCalendar( \''.$properties["id"].'\', \''.$date.'\', \'\', \'\' );" ';//ID\'
      $mForm .= 'title="Calendario"';
      $mForm .= ' />';
    }

      $mForm .= $this -> CloseCell( $properties["end"] ); 

    if( $mReturn )
    {
    	return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );    	
    }
  }

	/*********************************************************************
	 * Metodo Publico que retorna el arreglo de archivos js.             *
	 * HTML indentada a 2 espacios.                                      *
	 * @fn SetTab                                                        *
	 * @brief retorna el identificador del resultado                     *
	 * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function InputDIV( $properties="", $mReturn = FALSE )
  {
    $properties = GetAttributes( $properties );

    if ( !$properties["align"] )
    {
      $properties["align"] = "left";
    } 

    $mForm = $this -> OpenCell( $properties["id"], "celda_info", $properties["align"], $properties["valign"], $properties["rowspan"], $properties["colspan"], $properties["width"] );
    $mForm .= '<div style="display:none;">';
    $mForm .= '<input type="text" class="campo_texto"';
    if( $properties["color"] )
    {
      $mForm .= ' style=" color:'.$properties["color"].'; font-weight:bold; font-size:11"';    	
    }

    if( $properties["popup"] )
    {
      $mForm .= ' style="cursor:pointer"';
    }

    $mForm .= $this -> SetProperties( $properties );
    $mForm .= ' />';
    if( $properties["popup"] )
    {
      $mForm .= "\n        ";
      $mForm .= '<input class="popupButton" type="button" onclick="'.$properties["popup"].'" value="" ';
      $mForm .= 'title="Buscar"';
      if( $properties["disabled"] )
      {
      	$mForm .= ' disabled="disabled"';
      }
      $mForm .= ' />';
    }
    elseif( $properties["calendar"] )
    {
      $date = date( "Y-m-d" );
      $mForm .= "\n        ";
      $mForm .= '<input id="C'.$properties["name"].'ID" class="calendarButton" type="button" value="" ';
      $mForm .= 'onclick="ShowCalendar( \''.$properties["name"].'ID\', \''.$date.'\', \'\', \'\' );" ';
      $mForm .= 'title="Calendario"';
      $mForm .= ' />';
    }
    $mForm .= "</div>";
    $mForm .= $this -> CloseCell( $properties["end"] ); 

    if( $mReturn )
    {
    	return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );    	
    }
  }

	/*********************************************************************
	 * Metodo Publico que retorna el arreglo de archivos js.             *
	 * HTML indentada a 2 espacios.                                      *
	 * @fn SetTab                                                        *
	 * @brief retorna el identificador del resultado                     *
	 * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function Select2( $options, $properties="", $mReturn = FALSE )
  {
    $properties = GetAttributes( $properties );
    if( !$properties["align"] )
    {
      $properties["align"] = "left";    	
    }

    $mForm = $this -> OpenCell( $properties["id"], (!$properties["class"] ? "celda_info" : $properties["class"]), $properties["align"], $properties["valign"], $properties["rowspan"], $properties["colspan"], $properties["width"] );
    $mForm .= '<select class="form_01"';
    $mForm .= $this -> SetProperties( $properties, "off", "off" );
    $mForm .= '>';
    $size = count( $options );
    for ( $x=0; $x<$size; $x++ )
    {
        $selected = $options[$x][0] == $properties["key"] ? ' selected="selected"' : "";

      $mForm .= "\n          ";
      $mForm .= '<option value="'.$options[$x][0].'"'.$selected.'>';
      $mForm .= htmlentities( $options[$x][1] );
      $mForm .= '</option>';
    }
    $mForm .= "\n        ";
    $mForm .= '</select>';
    $mForm .= $this -> CloseCell( $properties["end"] ); 

    if ( $mReturn )
    {
    	return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );    	
    }
  }
  
  /*********************************************************************
   * Metodo Publico que retorna el html del tag SELECT.                *
   * HTML indentada a 2 espacios.                                      *
   * @fn Select                                                        *
   *********************************************************************/
  public function Select( $options, $properties = NULL, $keyMultiple = null, $mReturn = FALSE  )
  {
    $properties = GetAttributes( $properties );
    $properties["align"] = $properties["align"] == NULL ? 'left' : $properties["align"];
    
    $form  = NULL;
    $form .= $this -> OpenCell( $properties["id"], "celda_info", $properties["align"], $properties["valign"], $properties["rowspan"], $properties["colspan"], $properties["width"] );

    $form .= $properties["multiple"] == NULL ? '<select class="form_01"' : '<select multiple size="10" class="form_01"';

    if ( $properties["inner"] != NULL || $properties["value"] != NULL )
    {
      $element = $properties["inner"] ? $properties["inner"] : $properties["value"];
      $event   = $properties["inner"] ? "inner" : "value";
			$param   = "SelectExtra( this, '".$element."', '".$event."' )";
			$properties["onchange"] = $param."; ".$properties["onchange"];
			
      //$properties["onchange"] .= $properties["onchange"] ? "; " : "";
      //$properties["onchange"] .= "SelectExtra( this, '".$element."', '".$event."' )";
    }
    
    $form .= $this -> SetProperties( $properties, "off", "off" );
    $form .= '>';
    
    for ( $x = 0, $size = count( $options ); $x < $size; $x++ )
    {
      if ( $properties["multiple"] && is_array ( $keyMultiple ) )
      {
        $selected = NULL;
        for ( $m = 0; $m < sizeof ( $keyMultiple ); $m++ )
        {
          if ( $options[$x][0] == $keyMultiple[$m] )
          {
            $selected = ' selected="selected"';
            break;
          }
        }
      }
      else
      {
        if ( $x == $properties["index"] || $options[$x][0] == $properties["key"] )
        {
          $selected = ' selected="selected"';
        }
        else
        {
          $selected = NULL;
        }

      }

      $form .= "\n          ";
      $form .= '<option value="'.$options[$x][0].'"'.$selected.'>';
      $form .= htmlentities( $options[$x][1] );
      $form .= '</option>';
    }
    $form .= "\n        ";
    $form .= '</select>';
    if ( $properties["extra"] !== NULL )
    {
      $array_extra = explode( "|", $properties["extra"] );
      $array_name = explode( "|", $properties["value"] );

      for ( $k = 0, $size_extra = sizeof( $array_extra ); $k < $size_extra; $k ++ )
      {
        for ( $x = 0; $x < $size; $x ++ )
        {
          $hselect["name"] = "hselect_".$properties["name"].$k."-".$x;
					$hselect["id"]   = "hselect_".$properties["name"].$k."-".$x."ID";
          //$hselect["id"]   = $properties["id"] ? "hselect_".$properties["id"].$k."-".$x : "hselect_".$properties["name"].$k."-".$x."ID";
          $form .= "\n        ";
          $form .= '<input type="hidden" value="'.htmlentities( $options[$x][$array_extra[$k]] ).'" ';
          $form .= $this -> SetProperties( $hselect, "off", "off" );
          $form .= '/>';
        }

        if ( !$properties["inner"] && !$array_name[$k] )
        {
          $extra["name"] = "extra_".$properties["name"];
          $extra["id"]   = $properties["id"] ? "extra_".$properties["id"] : "extra_".$properties["name"]."ID";
          $form .= "\n        ";
          $form .= '<input type="hidden" ';
          $form .= $this -> SetProperties( $extra, "off", "off" );
          $form .= '/>';
        }
      }
    }
    $form .= $this -> CloseCell( $properties["end"] );
    
    if ( $mReturn )
    {
      return $form;
    }
    else
    {
      $this -> SetBody( $form );     
    }
  }

	/*********************************************************************
	 * Metodo Publico que retorna el arreglo de archivos js.             *
	 * HTML indentada a 2 espacios.                                      *
	 * @fn SetTab                                                        *
	 * @brief retorna el identificador del resultado                     *
	 * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function CheckBox( $properties="", $mReturn = FALSE )
  {
    $properties = GetAttributes( $properties );
    if ( !$properties["align"] )
    {
      $properties["align"] = "left";    	
    }
    
    $mForm = $this -> OpenCell( $properties["id"], ($properties["class"] ? $properties["class"] : 'celda_info'), $properties["align"], $properties["valign"], $properties["rowspan"], $properties["colspan"], $properties["width"] );
    $mForm .= '<input type="checkbox" class=""';
    $mForm .= $this -> SetProperties( $properties, "off", "off" );
    $mForm .= ' />';
    $mForm .= $this -> CloseCell( $properties["end"] ); 

    if( $mReturn )
    {
    	return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );    	
    }
  }

	/*********************************************************************
	 * Metodo Publico que retorna el arreglo de archivos js.             *
	 * HTML indentada a 2 espacios.                                      *
	 * @fn SetTab                                                        *
	 * @brief retorna el identificador del resultado                     *
	 * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function Radio( $properties="", $mReturn = FALSE )
  {
    $properties = GetAttributes( $properties );

    $mForm = $this->OpenCell( $properties["id"], ($properties["class"] ? $properties["class"] : 'celda_info'), $properties["align"], $properties["valign"], $properties["rowspan"], $properties["colspan"], $properties["width"] );
    $mForm .= '<input type="radio" class=""';
    $mForm .= $this -> SetProperties( $properties, "off", "off" );
    $mForm .= ' />';
    $mForm .= $this -> CloseCell( $properties["end"] ); 

    if( $mReturn )
    {
    	return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );    	
    }
  }

	/*********************************************************************
	 * Metodo Publico que retorna el arreglo de archivos js.             *
	 * HTML indentada a 2 espacios.                                      *
	 * @fn SetTab                                                        *
	 * @brief retorna el identificador del resultado                     *
	 * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function Password( $properties = "", $mReturn = FALSE )
  {
    $properties = GetAttributes( $properties );

    if( !$properties["align"] )
    {
    	$properties["align"] = "left";
    }

    $mForm = $this -> OpenCell( $properties["id"], ($properties["class"] ? $properties["class"] : 'celda_info'), $properties["align"], $properties["valign"], $properties["rowspan"], $properties["colspan"], $properties["width"] );
    $mForm .= '<input type="password" class="campo_texto"';
    $mForm .= $this -> SetProperties( $properties, "off", "off" );
    $mForm .= ' />';
    $mForm .= $this -> CloseCell( $properties["end"] ); 

    if( $mReturn )
    {
    	return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );    	
    }
  }

	/*********************************************************************
	 * Metodo Publico que retorna el arreglo de archivos js.             *
	 * HTML indentada a 2 espacios.                                      *
	 * @fn SetTab                                                        *
	 * @brief retorna el identificador del resultado                     *
	 * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function Hidden( $properties = "", $mReturn = FALSE )
  {
    $properties = GetAttributes( $properties );
    $mForm = "\n        ";
    $mForm .= '<input type="hidden"';
    $mForm .= $this -> SetProperties( $properties, "off", "off" );
    $mForm .= ' />';

    if( $mReturn )
    {
    	return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );    	
    }
  }
    
	/*********************************************************************
	 * Metodo Publico que retorna el arreglo de archivos js.             *
	 * HTML indentada a 2 espacios.                                      *
	 * @fn SetTab                                                        *
	 * @brief retorna el identificador del resultado                     *
	 * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function File( $properties = "", $mReturn = FALSE )
  {
    $properties = GetAttributes( $properties );
    $mForm = $this -> OpenCell( $properties["id"], ($properties["class"] ? $properties["class"] : 'celda_info'), "left", $properties["valign"], $properties["rowspan"], $properties["colspan"], $properties["width"] );
    $mForm .= '<input type="file" class=""';
    $mForm .= $this -> SetProperties( $properties, "off", "off" );
    $mForm .= ' />';
    $mForm .= $this -> CloseCell( $properties["end"] ); 

    if( $mReturn )
    {
    	return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );    	
    }
  }

	/*********************************************************************
	 * Metodo Publico que retorna el arreglo de archivos js.             *
	 * HTML indentada a 2 espacios.                                      *
	 * @fn SetTab                                                        *
	 * @brief retorna el identificador del resultado                     *
	 * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function TextArea( $value = "", $properties = "", $mReturn = FALSE )
  {
    $properties = GetAttributes( $properties );

    $mForm = $this -> OpenCell( $properties["id"], ($properties["class"] ? $properties["class"] : 'celda_info'), "left", $properties["valign"], $properties["rowspan"], $properties["colspan"], $properties["width"] );
    $mForm .= '<textarea class="campo_texto"';
    $mForm .= $this -> SetProperties( $properties );
    $mForm .= '>';
    $mForm .= htmlentities( $value );
    $mForm .= '</textarea>';
    $mForm .= $this -> CloseCell( $properties["end"] );

    if( $mReturn )
    {
    	return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );    	
    }
  }

	/*********************************************************************
	 * Metodo Publico que retorna el arreglo de archivos js.             *
	 * HTML indentada a 2 espacios.                                      *
	 * @fn SetTab                                                        *
	 * @brief retorna el identificador del resultado                     *
	 * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function Button( $properties = "", $mReturn = FALSE )
  {
    $properties = GetAttributes( $properties );

    $mForm = $this -> OpenCell( $properties["id"], ($properties["class2"] ? $properties["class2"] : 'celda_info'), $properties["align"], $properties["valign"], $properties["rowspan"], $properties["colspan"], $properties["width"] );
    $mForm .= '<input type="button" style="cursor:pointer"';

    if ( $properties["popup"] )
    {
    	$mForm .= ' style="cursor:pointer"';
    }
     
    $mForm .= $this->SetProperties( $properties, "", "" );
    $mForm .= ' />';
    $mForm .= $this -> CloseCell( $properties["end"] ); 

    if( $mReturn )
    {
    	return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );    	
    }
  }

	/*********************************************************************
	 * Metodo Publico que retorna el arreglo de archivos js.             *
	 * HTML indentada a 2 espacios.                                      *
	 * @fn SetTab                                                        *
	 * @brief retorna el identificador del resultado                     *
	 * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function DeleteButton( $properties = "", $mReturn = FALSE )
  {
    $properties = GetAttributes( $properties );
    $mForm = $this -> OpenCell( $properties["id"], "celda_info", $properties["align"], $properties["valign"], $properties["rowspan"], $properties["colspan"], $properties["width"] );
    $mForm .= '<input class="DeleteButton_" id="'.$id.'" name="'.$name.'" ';

    $mForm .= $properties["title"] == NULL ? ' title="Eliminar"' : ' title="'.$properties["title"].'"';
  
    $mForm .= $this -> SetProperties( $properties, "", "" );
    $mForm .= '/>';
    $mForm .= $this -> CloseCell( $properties["end"] ); 

    if( $mReturn )
    {
    	return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );    	
    }
  }

	/*********************************************************************
	 * Metodo Publico que retorna el arreglo de archivos js.             *
	 * HTML indentada a 2 espacios.                                      *
	 * @fn SetTab                                                        *
	 * @brief retorna el identificador del resultado                     *
	 * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function NewButton( $properties = "", $mReturn = FALSE )
  {
    $properties = GetAttributes( $properties );
    $mForm = $this -> OpenCell( $properties["id"], "celda_info", $properties["align"], $properties["valign"], $properties["rowspan"], $properties["colspan"], $properties["width"] );
    $mForm .= '<input class="NewButton_" id="'.$id.'" name="'.$name.'" ';
    if ( !$properties["title"] )
    {
    	$mForm .= ' title="Nuevo"';
    }
     
    $mForm .= $this -> SetProperties( $properties, "", "" );
    $mForm .= '/>';
    $mForm .= $this -> CloseCell( $properties["end"] ); 

    if( $mReturn )
    {
    	return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );    	
    }
  }

	/*********************************************************************
	 * Metodo Publico que retorna el arreglo de archivos js.             *
	 * HTML indentada a 2 espacios.                                      *
	 * @fn SetTab                                                        *
	 * @brief retorna el identificador del resultado                     *
	 * @return cIdResultel id del resultado                              *
   *********************************************************************/
	public function EditButton( $properties = "", $mReturn = FALSE )
	{
    $properties = GetAttributes( $properties );

    $mForm = $this -> OpenCell( $properties["id"], "celda_info", $properties["align"], $properties["valign"], $properties["rowspan"], $properties["colspan"], $properties["width"] );
    $mForm .= '<input class="EditButton_" id="'.$id.'" name="'.$name.'" ';

    if( !$properties["title"] )
    {
    	$mForm .= ' title="Editar"';
    }

    $mForm .= $this->SetProperties( $properties, "", "" );
    $mForm .= '/>';
    $mForm .= $this -> CloseCell( $properties["end"] ); 

    if( $mReturn )
    {
    	return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );    	
    }
  }

	/*********************************************************************
	 * Metodo Publico que retorna el arreglo de archivos js.             *
	 * HTML indentada a 2 espacios.                                      *
	 * @fn SetTab                                                        *
	 * @brief retorna el identificador del resultado                     *
	 * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function StyleButton( $properties = "", $mReturn = FALSE )
  {
    $properties = GetAttributes( $properties );

    $mForm = $this -> OpenCell( $properties["id"], "celda_info", $properties["align"], $properties["valign"], $properties["rowspan"], $properties["colspan"], $properties["width"] );
    $mForm .= '<input type="button" class="crmButton small save'.$properties["size"].'" ';
	//$mForm .= '<input type="button" class="'.$properties["size"].'" ';
    $mForm .= $this -> SetProperties( $properties, "", "" );
    $mForm .= ' />';
    $mForm .= $this -> CloseCell( $properties["end"] ); 

    if( $mReturn )
    {
    	return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );    	
    }
  }
  
  
  public function AddButton( $properties = "", $mReturn = FALSE )
  {
    $properties = GetAttributes( $properties );

    $mForm = $this -> OpenCell( $properties["id"], "celda_info", $properties["align"], $properties["valign"], $properties["rowspan"], $properties["colspan"], $properties["width"] );
    $mForm .= '<input type="button" class="styleAddButton'.$properties["size"].'" ';
	//$mForm .= '<input type="button" class="'.$properties["size"].'" ';
    $mForm .= $this -> SetProperties( $properties, "", "" );
    $mForm .= ' /><br>';
    $mForm .= $this -> CloseCell( $properties["end"] ); 

    if( $mReturn )
    {
    	return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );    	
    }
  }
    
	/*********************************************************************
	 * Metodo Publico que retorna el arreglo de archivos js.             *
	 * HTML indentada a 2 espacios.                                      *
	 * @fn SetTab                                                        *
	 * @brief retorna el identificador del resultado                     *
	 * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function Transparency( $id = "TransparencyDIV", $zindex = "1", $width = "100%", $height = "100%", $mReturn = FALSE  )
  {
    $mForm = "\n      ";
    $mForm .= '<div id="'.$id.'" ';
    $mForm .= 'style="position: absolute; ';
    if( $this -> GetBrowser() == "Explorer" )
    {
      $mForm .= 'filter: alpha(opacity=50); ';
    }
    $mForm .= 'opacity: 0.4; ';
    $mForm .= 'left: 0px; ';
    $mForm .= 'top: 0px; ';
    $mForm .= 'width: '.$width.'; ';
    $mForm .= 'height: '.$height.'; ';
    $mForm .= 'z-index: '.$zindex.'; ';
    $mForm .= 'visibility: hidden; ';
    $mForm .= 'border: 1px solid black; ';
    $mForm .= 'background: gray; ';
    $mForm .= '">';
    $mForm .= '</div>';
    $mForm .= "\n      ";

    if( $mReturn )
    {
    	return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );    	
    }
  }
    
	/*********************************************************************
	 * Metodo Publico que retorna el arreglo de archivos js.             *
	 * HTML indentada a 2 espacios.                                      *
	 * @fn SetTab                                                        *
	 * @brief retorna el identificador del resultado                     *
	 * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function Calendar( $zindex="5", $width="100%", $height="100%", $mReturn = FALSE )
  {
    $mForm = "\n      ";
    $mForm .= '<div id="RyuCalendarDIV" ';
    $mForm .= 'style="position:absolute; ';
    $mForm .= 'background:white; ';
    $mForm .= 'left:0px; ';
    $mForm .= 'top:0px; ';
    $mForm .= 'z-index:'.$zindex.'; ';
    $mForm .= 'display:none; ';
    $mForm .= 'border:1px solid black; ';
    $mForm .= '">';
    $mForm .= '</div>';
    $mForm .= "\n      ";

    if( $mReturn )
    {
    	return $mForm;
    }
    else
    {
      $this -> SetBody( $mForm );    	
    }
  }

	/*********************************************************************
	 * Metodo Publico que retorna el arreglo de archivos js.             *
	 * HTML indentada a 2 espacios.                                      *
	 * @fn SetTab                                                        *
	 * @brief retorna el identificador del resultado                     *
	 * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function MakeSubmit( $mArray )
  {
    $mKeys = array_keys( $mArray );
    for ( $i = 0, $mSize = sizeof( $mKeys ); $i < $mSize; $i++ )
    {
      $this -> Hidden( "name:".$mKeys[$i]."; value:".$mArray[$i] );
    }
  }
	
	
	public function AutoSuggest( $mOnchange = NULL, $mOnclick = NULL, $mName= NULL ) 
	{
		$mName = $mName == NULL ? 'AutoSuggest' : $mName;
		$mForm .= "\n      ";
		$mForm .= '<select name="'.$mName.'" id="'.$mName.'ID" onchange="'.$mOnchange.'" onkeyup="'.$mOnchange.'" onclick="'.$mOnclick.'" style="position:absolute; display:none;"></select>';
		$mForm .= "\n      ";
		$mForm .= '<input type="hidden" name="'.$mName.'Hidden" id="'.$mName.'HiddenID" />';
		$mForm .= "\n      ";
		$this -> SetBody( $mForm );    	
	}
	
}
?>