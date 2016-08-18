<?php
/*! \file GeneralFunctions.php
 *  \brief funcines generales del sistema
 *  \author Carlos A. Mock-kow M:
 *  \author carlos.mock@intrared.net
 *  \author Christiam. Barrera:
 *  \author christiam.barrera@intrared.net
 *  \version 1.0
 *  \date    09-2008.
 *  \bug Pendiente revision de Funciones.
*/

  /* ************************ funciones para manejo de archivos **************************** */
 
  function myExceptionHandler( $e )
  {
    echo $e;
    die();
  }

  set_exception_handler( 'myExceptionHandler' );

  /*! \fn __autoload( $fClass )
   *  \author Carlos A. MOck-kow:
   *  \author carlos.mock@intrared.net
   *  \brief Funcion para auto carga de objetos en el framework.
   *  \param $class: Objeto o clase a ser cargada.
   *  \return no retorna.
   */
  function __autoload( $fClass )
  {
    if( class_exists( $fClass, false ) )
    {
      return;
    }

    try
    {
      if( NULL != DirLib && 'DirLib' != DirLib )
        $lib = DirLib;
      else
       $lib = '../lib/';

      LoadLib( $fClass.'.class.php', $lib );

      if( !class_exists( $fClass, false ) )
      {
        throw new Exception( 'la Clase: '.$fClass.' no se encuentra' );
      }
    }
    catch( Exception $e )
    {
      myExceptionHandler( $e );
    }
  }

  /*! \fn LoadHelper( $nomHelper = NULL, $direc = '../helpers/' )
   *  \author Carlos A. Mock-kow M:
   *  \author carlos.mock@intrared.net
   *  \brief Funcion para cargar clases y archivos de funciones .
   *  \param $fNomHelper: string nombre de la clase o funcion a cargar.
   *  \param $fDirectory: string ruta del directorio donde se encuentra el arcjivo a cargar, por defecto: "../helpers/" .
   *  \return retorna verdadero si serealizo carga con exito, delocontrario falso.
   */
  function LoadLib( $fNomHelper = NULL, $fDirectory = '../lib/' )
  {
    if( NULL !== $fNomHelper )
    {

      $fArc = explode( ".", $fNomHelper );
      switch( strtolower( $fArc[1] ) )
      {
      	case "class":
      	{
          if( file_exists( $fDirectory.$fNomHelper ) )
          {
            return include_once( $fDirectory.$fNomHelper );
          }
          elseif( file_exists( $fDirectory.'model/'.$fNomHelper ) )
          {
            return include_once( $fDirectory.'model/'.$fNomHelper );
          }
          elseif( file_exists( $fDirectory.'validators/'.$fNomHelper ) )
          {
            return include_once( $fDirectory.'validators/'.$fNomHelper );
          }
          elseif( file_exists( $fDirectory.'view/'.$fNomHelper ) )
          {
            return include_once( $fDirectory.'view/'.$fNomHelper );
          }
          break;
      	}

        case "fnc":
        {
          if( file_exists( $fDirectory.'functions/'.$fNomHelper ) )
          {
            return include_once( $fDirectory.'functions/'.$fNomHelper );
          }
          break;
        }

        case "kons":
        {
          if( file_exists( $fDirectory.$fNomHelper ) )
          {
      	    return include_once( $fDirectory.$fNomHelper );
          }        	
        }
        
        default: return false;
      }
      
    }
    else
    {
      return FALSE;
    }
    
  }

  /*! \fn readConfFile()
   *  \author Carlos A. Mock-kow M:
   *  \author carlos.mock@intrared.net
   *  \brief Funcion para leer los archivos de configuracion del framework.
   *  \param $fNomArc: string nombre del archivo de configuracion a cargar.
   *  \param $fDirectory: string ruta del directorio donde se encuentra el arcjivo a cargar, por defecto: "../helpers/" .
   *  \return el contenido del archivo si es exitosa la ejecucion, false si ocurrio algun error.
   */
  function ReadCFile( $fNomArc = NULL, $fDirectory = '../config/' )
  {
    if( file_exists( $fDirectory.$fNomArc ) )
    {
      $archivo = fopen( $fDirectory.$fNomArc, "rb" );
      $contenido = fread( $archivo, filesize( $fDirectory.$fNomArc ) );
      fclose( $archivo );

      return $contenido;
    }
    else
      return FALSE;
  }

  /* ********************************* funciones para el parceo de datos ********************** */

  /*! \fn GetAttributes( $fAttributes = NULL )
   *  \author Christiam barrera:
   *  \author christiam.barrera@intrared.net
   *  \brief Funcion para parcear los atributos de un metodo o tag en el framework.
   *  \param $fAttributes: string/array cadena o arreglo a parcear segun standar del framework.
   *  \return arreglo asociativo validado segun standar del framework.
   */
  function GetAttributes( $mAttributes = NULL )
  {
    $mTags = 0;
    if( !$mAttributes )
    {
      if( !isset( $mAttributes["name"] ) )
      {
        $mAttributes["name"] = "tag".$mTags;
        $mTags++;
      }

      if( !isset( $mAttributes["id"] ) )
        $mAttributes["id"] = $mAttributes["name"]."ID";

      if( isset( $mAttributes["multiple"] ) )
        $mAttributes["name"] .= "[]";

      return $mAttributes;
    }
    elseif( is_array( $mAttributes ) )
    {
      if( !isset( $mAttributes["name"] ) )
      {
        $mAttributes["name"] = "tag".$mTags;
        $mTags++;
      }

      if( !isset( $mAttributes["id"] ) )
        $mAttributes["id"] = $mAttributes["name"]."ID";

      if( isset( $mAttributes["multiple"] ) )
        $mAttributes["name"] .= "[]";

      $mTemp = array();
      $mKeys = array_keys( $mAttributes );
      $c = 0;
      foreach( $mAttributes as $mItem )     
      {
        $mTemp[strtolower( $mKeys[$c] )] = $mItem;
        $c++;
      }     
      return $mTemp;
    }
    else
    {
      $mArray = explode( "; ", $mAttributes );
      $mTemp = array();
      foreach( $mArray as $mRow )
      {
        $mAux = explode( ":", $mRow );
        $mTemp[strtolower( trim( $mAux[0] ) )] = $mAux[1];
      }
      if( !isset( $mTemp["name"] ) )
      {
        $mTemp["name"] = "tag".$mTags;
        $mTags++;
      }

      if( !isset( $mTemp["id"] ) )
        $mTemp["id"] = $mTemp["name"]."ID";

      if( isset( $mTemp["multiple"] ) )
        $mTemp["name"] .= "[]";

      unset( $mArray, $mAux  );
      return $mTemp;
    }
  }

  /*! \fn GetAttributes( $fAttributes = NULL )
   *  \author Carlos A. Mock-kow M.:
   *  \author carlos.mock@intrared.net
   *  \brief Funcion para obtener los datos via get o post.
   *  \param $fNomdata: string nombre del campo.
   *  \param $fDefault: string valor por defecto.
   *  \return valor del compo o el valor por defecto.
   */
  function GetData( $fNomData = NULL, $fDefault = NULL )
  {
    if( $fNomData != NULL )
    {
      if( array_key_exists( $fNomData, $_POST ) )
      {
        $retorno = $_POST[ $fNomData ];
      }
      elseif( array_key_exists( $fNomData, $_GET ) )
      {
        $retorno = $_GET[ $fNomData ];
      }
      else
      {
        $retorno = $fDefault;
      }
    }
    else
    {
        $retorno = FALSE;
    }
     return $retorno;
  }

  /*! \fn DataMethod()
   *  \author Carlos A. Mock-kow:
   *  \author carlos.mock@intrared.net
   *  \brief Funcion obtener el metodo por el cual se enviaron los datos.
   *  \return metodo por el cual se enviaron los datos.
   */
  function DataMethod()
  {
    if( isset( $_GET ) )
    {
        return "get";
    }
    elseif( isset( $_POST ) )
    {
        return "post";
    }
    else
      return FALSE;
  }

	/*********************************************************************
	 * Metodo Publico que retorna el arreglo de archivos js.             *
	 * HTML indentada a 2 espacios.                                      *
	 * @fn SetTab                                                        *
	 * @brief retorna el identificador del resultado                     *
	 * @return cIdResultel id del resultado                              *
   *********************************************************************/
  function MoneyFormat( $num )
  {
    return str_replace( ",", ".", number_format( $num ) );
  }

	/*********************************************************************
	 * Metodo Publico que retorna el arreglo de archivos js.             *
	 * HTML indentada a 2 espacios.                                      *
	 * @fn SetTab                                                        *
	 * @brief retorna el identificador del resultado                     *
	 * @return cIdResultel id del resultado                              *
   *********************************************************************/
  function ReturnDate( $date = NULL )
  {
    if ( !$date )
    {
      $date = date( "Y-m-d" );
    }

    $date = explode( "-", $date );

    switch( $date[1] )
    {
      case '01' : $month = "Enero";      break;
      case '02' : $month = "Febrero";    break;
      case '03' : $month = "Marzo";      break;
      case '04' : $month = "Abril";      break;
      case '05' : $month = "Mayo";       break;
      case '06' : $month = "Junio";      break;
      case '07' : $month = "Julio";      break;
      case '08' : $month = "Agosto";     break;
      case '09' : $month = "Septiembre"; break;
      case '10' : $month = "Octubre";    break;
      case '11' : $month = "Noviembre";  break;
      case '12' : $month = "Diciembre";  break;
    }

    switch ( date( "w" ) )
    {
      case '0' : $day = "Domingo";    break;
      case '1' : $day = "Lunes";      break;
      case '2' : $day = "Martes";     break;
      case '3' : $day = "Miércoles";  break;
      case '4' : $day = "Jueves";     break;
      case '5' : $day = "Viernes";    break;
      case '6' : $day = "Sábado";     break;
    }

    return $day.", ".$date[2]." de ".$month." de ".$date[0];
  }
  
  
//----------------------------------------------------------------------------------------------------
//@REEMPLAZA EL VALOR BUSCADO POR UN NUEVO VALOR DENTRO DE UNA MATRIZ PASADA POR REFERENCIA DE MEMORIA
function ReplaceInMatrix( &$matrix, $old, $new )
{
 for ( $i = 0, $size = sizeof( $matrix ); $i < $size; $i++ )
 {
   $keys = array_keys( $matrix[$i] );
   for ( $j = 0, $row_size = sizeof( $matrix[$i] ); $j < $row_size; $j++ )
   {
     $matrix[$i][$keys[$j]] = $matrix[$i][$keys[$j]] == $old ? $new : $matrix[$i][$keys[$j]];
   }
 }
}


//---------------------------------------------------------------
//@FORMATEA LAS LLAVES PRIMARIAS DE CUALQUIER CONSULTA
function key_format( &$mKey, $mChars = NULL )
{
  if ( $mChars === NULL )
  {
    $mKey = str_replace( ' ', NULL, $mKey );
  }
	else
	{
		foreach ( $mChars as $mChar )
		{
			$mKey = str_replace( $mChar, NULL, $mKey );
		}
	}
}

//---------------------------------------------------------------
//@FORMATEA LOS CAMPOS DE CUALQUIER CONSULTA
function field_format( &$mKey, $mTrim = NULL, $mChars = NULL )
{
  if ( $mChars === NULL )
  {
    $mKey = $mTrim === NULL ? str_replace( ' ', NULL, $mKey ) : trim( $mKey );
  }
	else
	{
		foreach ( $mChars as $mChar )
		{
			$mKey = str_replace( $mChar, NULL, $mKey );
		}
	}
}

  function dateDiff( $d1, $d2 )
  {
    $d1 = ( is_string( $d1 ) ? strtotime( $d1 ) : $d1 );
    $d2 = ( is_string( $d2 ) ? strtotime( $d2 ) : $d2 );

    $diff_secs = abs( $d1 - $d2 );
    $base_year = min( date( "Y", $d1 ), date( "Y", $d2 ) );

    $diff = mktime( 0, 0, $diff_secs, 1, 1, $base_year );
    return array(
        "years" => date( "Y", $diff ) - $base_year,
        "months_total" => ( date( "Y", $diff ) - $base_year ) * 12 + date( "n", $diff ) - 1,
        "months" => date( "n", $diff ) - 1,
        "days_total" => floor( $diff_secs / ( 3600 * 24 ) ),
        "days" => date( "j", $diff ) - 1,
        "hours_total" => floor( $diff_secs / 3600 ),
        "hours" => date( "G", $diff ),
        "minutes_total" => floor( $diff_secs / 60 ),
        "minutes" => ( int ) date( "i", $diff ),
        "seconds_total" => $diff_secs,
        "seconds" => ( int ) date( "s", $diff )
    );
  }

  function getDigVerifi( $nit ) 
  {
    $pesos = array( 71, 67, 59, 53, 47, 43, 41, 37, 29, 23, 19, 17, 13, 7, 3 );
    $rut_fmt = str_pad( $nit, 15, "0", STR_PAD_LEFT );
    $suma = 0;
    for ( $i=0; $i<=14; $i++ )
      $suma += (int)substr( $rut_fmt, $i, 1 ) * $pesos[$i];
    $resto = $suma % 11;
    if ( $resto == 0 || $resto == 1 )
      $digitov = $resto;
    else
      $digitov = 11 - $resto;
    return( $digitov );
  }
  
  function eliminarAcentos($cadena)
  {
    $tofind = "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ";
    $replac = "AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn";
    return strtr( $cadena, $tofind, $replac );
  }
  
  
  
  function ControlTimerX( $DirJs, $DirCss )
  {
    $LimitSession = ini_get('session.gc_maxlifetime');
    $url2 =  "https://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
    //$mForm .= '<script src="'.$DirJs.'ads/jquerymin.js" language="Javascript"></script>';
    //$mForm .= '<script src="'.$DirJs.'ads/jqueryuimin.js" language="Javascript"></script>';
    //$mForm .= '<script src="'.$DirCss.'jqueryui.css" language="Javascript"></script>';
    $mDysplay = base64_decode($_SESSION['UUsrx']) == 'hugooo.malagon' ? 'block' : 'none';
   
    $mForm .= "<div style='position: fixed; top: 6px; left: 10%;  background-color:#000000; color: #ffffff; display: ".$mDysplay.";' ><b><span id='clockDiv'>0</span> Segundos</b></div>";
   
    $mForm .= ' <script>
                    window.onload=function(){
                      if( document.getElementById("tag0ID")) {
                      var mBody  = document.getElementById("tag0ID").setAttribute("onclick","reiniciar(true); ");
                      var mBody  = document.getElementById("tag0ID").setAttribute("onkeypress","reiniciar(true); ");
                      }
                    }
                    
                   
                    var TimeSession = "'.$LimitSession.'";
                    
                    var Limit1 = (TimeSession * 80) / 100; // coloco el 80% del tiempo total de la session
                    var Limit1 = Math.round(Limit1);
                    var Limit2 = TimeSession;
                    var Intento = 1;
                    var count = 0;
                    var visor=document.getElementById("clockDiv");
                    var header = parent.document.getElementById( "header" );
                    var objBlockMenu = parent.document.getElementById( "MenuTransparencyDiv" );
                    
                    //variables de inicio: reloj
                    var marcha=0; //control del temporizador
                    var cro=0; //estado inicial del cronómetro.
                    
                    function empezar(){
                        if (marcha==0) { //solo si el cronómetro esta parado
                        emp=new Date() //fecha actual
                        elcrono=setInterval(tiempo,1000); //función del temporizador.
                        marcha=1 //indicamos que se ha puesto en marcha.
                        }
                    }
                    
                    function tiempo(){ 
                      cro++;                   
                      visor.innerHTML=cro; 
                      if( cro == Limit1){
                        ShowMessageTimer( Limit1 );
                      }
                      if( cro == Limit2){
                        ShowMessageClose( Limit2 ); 
                      }                        
                    }
                    
                    function parar(){ 
                     if (marcha==1) { //sólo si está en funcionamiento
                        clearInterval(elcrono); //parar el crono
                        marcha=0; //indicar que está parado.
                        }		
                    }
                    
                    function reiniciar( op ) {
                      if (marcha==1) { //si el cronómetro está en marcha:
                      clearInterval(elcrono); //parar el crono
                      marcha=0;	//indicar que está parado
                      }
                      cro=0; //tiempo transcurrido a cero
                      visor.innerHTML=cro; 
                      if(op == true)
                        empezar();
                    }	
                    empezar();
                    
                   
                    function ShowMessageTimer()
                    {
                      try
                      {
                        var user = "'.base64_decode($_SESSION['UUsrx']).'";
                        var html  = "<table id=\'Box\'>";
                            html += "  <tr>";
                            html += "    <td style=\"font-size: 12px;\">";
                            html += "      Se&ntilde;or(a) Usuario: <b>"+user+"</b><br><br>";
                            html += "      No se ha tenido actividad durante los &uacute;ltimos "+Math.round(Limit1 / 60)+" Minutos<br>";
                            html += "      La sessi&oacute;n se cerrar&aacute; en "+Math.round((TimeSession - Limit1) /60)+" Minutos<br>";
                            html += "      Por favor Digite su clave para continuar<br>";
                            html += "      Intento Nro: <span id=\"IntentosID\">"+Intento+"</span>";                                         
                            html += "    </td>";
                            html += "  </tr>";
                            html += "  <tr>";
                            html += "    <td>";
                            html += "      <input type=\"password\" name=\"PassWord\" id=\"PassWordID\" placeholder=\"Digite su clave\"  >";
                            html += "    </td>";
                            html += "  </tr>";
                            html += "  <tr><td><span id=\"ErrorID\" style=\"color: red; font-size:15px; \"></span></td></tr>";
                            html += "</table>";
                            html += "<hr>";
                            html += "<table>";
                            html += "<tr>";
                            html += "<td><input type=\"button\" value=\"Continuar\" class=\"styleButtonA\" onclick=\"ValidaUser()\"></td>";
                            html += "</tr>";
                            html += "</table>";
                            ResizeDivs(html, "load");                 
                        
                      }
                      catch( e )
                      {
                        alert("Error en ShowMessageTimer: "+e.message+"\nLine: "+e.lineNumber);
                      }
                    }
                    
                    function ResizeDivs( html, option )
                    { 
                      try {
                        var _body  = document.getElementById("tag0ID");
                        var block  = document.getElementById( "blokerDIV" );
                        var popup  = document.getElementById( "formularioDIV" );
                        var header = parent.document.getElementById( "header" );
                        var  menux = parent.document.getElementById( "menu" );
                        var _menu  = parent.document.getElementById( "MenuTransparencyDiv" );                      
                       
                         
                        if( !_body )
                          return false;
                        
                        if(!_menu)
                        {
                          var newdiv = parent.document.createElement("div");
                          newdiv.setAttribute("id","MenuTransparencyDiv");
                          newdiv.innerHTML = " ";
                          menux.appendChild(newdiv);
                          var _menu  =  parent.document.getElementById( "MenuTransparencyDiv" );
                              _menu.setAttribute("style", "position: absolute; opacity: 0.4; left: 0px; top: 0px; width: 100%; height: 100%; z-index: 2; display: block; border: 1px solid black; background: none repeat scroll 0% 0% rgb(16, 112, 154);")
                        }
                        
                        if( !block ) {
                         
                         var newdiv = document.createElement("div");
                          newdiv.setAttribute("id","blokerDIV");
                          newdiv.innerHTML = " ";
                          _body.appendChild(newdiv);
                          var block  = document.getElementById( "blokerDIV" );
                              block.setAttribute("style", "position: absolute; opacity: 0.4; left: 0px; top: 0px; width: 100%; height: 100%; z-index: 2; display: block; border: 1px solid black; background: none repeat scroll 0% 0% rgb(16, 112, 154);")
                        }
                        
                        if(!popup)  
                        {
                          var newdiv = document.createElement("div");
                          newdiv.setAttribute("id","formularioDIV");
                          newdiv.innerHTML = " ";
                          _body.appendChild(newdiv); 
                          var popup  = document.getElementById( "formularioDIV" );
                              popup.setAttribute("style", "position: absolute; top: 200px; left: 200px; overflow: auto; z-index: 3; text-align: center; padding: 10px; display: block; border: 7px solid rgb(78, 140, 207); background: none repeat scroll 0% 0% white;")
                        }
                        
                        if( option === "load") {
                          // Bloqueo Central
                          block.style.display = "block";
                          block.style.opacity = "0.4";
                          _body.style.overflow = "hidden"
                          
                          //PopUp mensaje                          
                          popup.innerHTML = html;
                          popup.style.display = "block";
                          popup.style.top  = ((screen.height / 2) - 300)+"px";
                          popup.style.left = ((screen.width  / 2) - 354 )+"px";
                          
                          // Bloqueo menu
                          _menu.style.display = "block";
                          _menu.style.height  = String(menux.clientHeight) + "px";
                          _menu.style.width = String( menux.clientWidth ) + "px";
                          _menu.style.top  = String(header.clientHeight) + "px";
                        }
                        else {
                          // Bloqueo Central
                          block.style.display = "none";
                          _body.removeAttribute("style");
                          
                          //PopUp mensaje                          
                          popup.innerHTML = " ";
                          popup.style.display = "none";
                          popup.style.top = "0px";
                          popup.style.left = "opx";
                          // Bloqueo menu
                          _menu.style.display = "none";
                        }
                        
                      }
                      catch(e) {
                        alert("Error en ResizeDivs: "+e.message+"\nLine: "+e.lineNumber);
                      }
                    }
                    function ValidaUser()
                    {
                      try {
                            var _PassWord = document.getElementById("PassWordID");                                                 
                            var _Error    = document.getElementById("ErrorID")
                            if( _PassWord.value == "" ) {
                                _Error.innerHTML = "Por favor digite su clave!";
                                setTimeout(function(){ _Error.innerHTML = ""}, 2000);
                            }
                            else{
                              EjectAjax( _PassWord );
                            }
                            
                      }
                      catch(e) {
                        alert("Error en ValidaUser: "+e.message+"\nLine: "+e.lineNumber);
                      }
                    }
                    var Base64 = {	
                                    _keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
                                    // public method for encoding
                                    encode : function (input) {
                                      var output = "";
                                      var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
                                      var i = 0;
                                      input = Base64._utf8_encode(input);
                                      while (i < input.length) {
                                        chr1 = input.charCodeAt(i++);
                                        chr2 = input.charCodeAt(i++);
                                        chr3 = input.charCodeAt(i++);
                                        enc1 = chr1 >> 2;
                                        enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
                                        enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
                                        enc4 = chr3 & 63;
                                        if (isNaN(chr2)) {
                                          enc3 = enc4 = 64;
                                        } else if (isNaN(chr3)) {
                                          enc4 = 64;
                                        }
                                        output = output +
                                        this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
                                        this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);
                                      }
                                      return output;
                                    },																	
                                    // private method for UTF-8 encoding
                                    _utf8_encode : function (string) {
                                      string = string.replace(/\r\n/g,"\n");
                                      var utftext = "";
                                        for (var n = 0; n < string.length; n++) {
                                        var c = string.charCodeAt(n);
                                        if (c < 128) {
                                          utftext += String.fromCharCode(c);
                                        }
                                        else if((c > 127) && (c < 2048)) {
                                          utftext += String.fromCharCode((c >> 6) | 192);
                                          utftext += String.fromCharCode((c & 63) | 128);
                                        }
                                        else {
                                          utftext += String.fromCharCode((c >> 12) | 224);
                                          utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                                          utftext += String.fromCharCode((c & 63) | 128);
                                        }
                                      }
                                      return utftext;
                                    }				
                                  }
                    
                    function EjectAjax( a )
                    {
                      try
                      {
                        var _Error   = document.getElementById("ErrorID");
                        var _Intento = document.getElementById("IntentosID");
                        var Input = Base64.encode(a.value);
                        var params = "aplica=sos&module=users&action=ValidaPassunlock&pas_usuari="+Input+"&cod_usuari='.$_SESSION['UUsrx'].'"
                        AjaxGetXmlLocked(params, "post" );                                         
                      }
                      catch( e )
                      {
                        alert( "Error en EjectAjax: "+e.message+"\nLine: "+e.lineNumber);
                      }
                    }               
                  
                  function OkValida()
                  {
                    var _Error   = document.getElementById("ErrorID");
                    var _Intento = document.getElementById("IntentosID");
                    ResizeDivs("","close");
                                 reiniciar();
                                 empezar();
                                 Intento = 1;
                                 count = 0;
                  }
                  
                  function ErrValida()
                  {
                    var _Error   = document.getElementById("ErrorID");
                    var _Intento = document.getElementById("IntentosID");
                    var _PassWord = document.getElementById("PassWordID");  
                    Intento +=  1;    
                    if(Intento == "4"){
                     parent.location.href = "'.$url2.'";
                        return false;
                    }
                    
                    _Error.innerHTML = "Clave Incorrecta!";
                    _Intento.innerHTML = Intento;
                    _PassWord.value = "";
                    _PassWord.focus();
                  }
                  
                  function ShowMessageClose()
                  {
                    try {
                      var Sec = "'.$LimitSession.'";
                      var Min = ( Sec / 60);
                      var user = "'.base64_decode($_SESSION['UUsrx']).'";
                              var html  = "<table id=\'Box\'>";
                                  html += "  <tr>";
                                  html += "    <td style=\"font-size: 12px;\">";
                                  html += "      Se&ntilde;or(a) Usuario: <b>"+user+"</b><br>";
                                  html += "      La sessi&oacute;n caduc&oacute; porque no hubo actividad<br>";
                                  html += "      en "+Min+" minutos<br>";
                                  html += "      Por su seguridad vuelva a iniciar sessi&oacute;n<br>";
                                  html += "    </td>";
                                  html += "  </tr>";
                                  html += "</table>";
                                  html += "<hr>";
                                  html += "<table>";
                                  html += "<tr>";
                                  html += "<td><input type=\"button\" value=\"Continuar\" class=\"styleButtonA\" onclick=\"CloseSession()\"></td>";
                                  html += "</tr>";
                                  html += "</table>";
                                  ResizeDivs(html, "load");
                    }
                    catch(e) {
                      alert("Error en ShowMessageClose: "+e.message+"\nLine:"+e.lineNumber);
                    }
                  }
                  
                  function CloseSession()
                  {
                    try {
                      window.close();
                      parent.location.href = "'.$url2.'";
                    }
                    catch(e) {
                      alert("Error en CloseSession: "+e.message+"\nLine: "+e.lineNumber);
                    }
                  }
                
               
                </script>';
     


     echo $mForm;
  }

?>