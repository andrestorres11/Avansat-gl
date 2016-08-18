<?php

/***********************************************************************************************************
 * esta clase permite crear el HTML de un formulario con ayudas validadoras javascript                     *
 * @brief esta clase permite la construcion de formularios.                                                *
 * @version 0.1                                                                                            *
 * @ultima_modificacion 21 de agosto de 2009                                                               *
 * @author Ing. Christiam Barrera                                                                          *
 * @author Ing. Carlos A. Mock-kow M. (Modificaciones framework y paso a php5)                             *
 ***********************************************************************************************************/

class DinamicHtml2
{
  protected $cHtml      = NULL;
  protected $cHead      = NULL;
  protected $cBody      = NULL;

  private $cMeta        = array();
  private $cCss         = array();
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

  /*********************************************************************
   * este metodo retorna el identificador del resultado de la consulta *
   * @fn SetProperties                                                 *
   * @brief retorna el identificador del resultado                     *
   * @return cIdResultel id del resultado                              *
   *********************************************************************/
  public function __construct( $mAplica = NULL )
  {
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

    if( $array["id"] )
      $mReturn .= ' id="'.$array["id"].'"';
//var_dump( $array["value"] );
    if( $array["value"] !== NULL )
      $mReturn .= ' value="'.$array["value"].'"';

    if( $array["height"] )
      $mReturn .= ' height="'.$array["height"].'"';

    if( $array["size"] )
      $mReturn .= ' size="'.$array["size"].'"';

    if( $array["maxlength"] )
      $mReturn .= ' maxlength="'.$array["maxlength"].'"';

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
        array_push( $this -> cJs, $mTmpJs[0] );
      }
      else
      {
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
        if( file_exists( DirCss.$this -> cAplica."/".$mCss.".css" ) )
        {
          $this -> cHead .= '<link rel="stylesheet" href="'.DirCss.$this -> cAplica."/".$mCss.'.css" type="text/css">';
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
        if( file_exists( DirCss.$this -> cAplica."/".$mCss.".css" ) )
        {
          $this -> cHead .= '<link rel="stylesheet" href="'.DirCss.$this -> cAplica."/".$mCss.'.css" type="text/css">';
        }
        elseif( file_exists( DirCss.$mCss.".css" ) )
        {
          $this -> cHead .= '<link rel="stylesheet" href="'.DirCss.$mCss.'.css" type="text/css">';
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
        if( file_exists( DirJsx.$this -> cAplica."/".$mJs.".js" ) )
        {
          $this -> cHead .= '<script src="'.DirJsx.$this -> cAplica."/".$mJs.'.js" type="text/javascript"></script>';
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
        if( file_exists( DirJsx.$this -> cAplica."/".$mJs.".js" ) )
        {
          $this -> cHead .= '<script src="'.DirJsx.$this -> cAplica."/".$mJs.'.js" type="text/javascript"></script>';
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
                            $colspan = 1, $width = "", $mReturn = TRUE )
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
    $mForm .= '<td id="'.$id.'" class="'.$class.'" align="'.$align.'" valign="'.$valign.'" rowspan="'.$rowspan.'" colspan="'.$colspan.'" width="'.$width.'">';
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
}
?>