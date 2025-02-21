<?php
//ini_set('display_errors', true);
//error_reporting(E_ALL &~E_NOTICE); 

#die("<center><fieldset><legend>URL Inactivada</legend>URL Inactivada, por favor ingrese a la siguiente URL <br><a href='https://avansatgl.intrared.net/ap/satt_demo/index.php?'>Aqu?</a></fieldset></center>");

include ("constantes.inc");
include ("../".DIR_APLICA_CENTRAL."/lib/generales.inc");

class Aplicacion_Seguridad
{
    //Atributos
    var $codigo,//Este codigo identifica la aplicacion en la base de datos de seguridad
        $usuario_aplicacion,//El usuario que hace uso de la aplicacion
        $conexion;//EL enlace que debe establecer con la base de datos

    //Metodos
    function __construct()//Constructor de la clase
    {
        $this -> codigo = 1;//este codigo es dado por el administrador de la base de datos de seguridad
        $this -> conexion = new Conexion(HOST, USUARIO, CLAVE, "".BASE_DATOS."");//Se crea la conexion a la base de datos
        $this -> principal();

    }
    
    function Principal()
    {
     
      session_start();
      $preload = explode( '?', $_SERVER['REQUEST_URI'] );
      $_SESSION['preload'] = $preload[1];
      if($this -> inicio_sesion())
      {
        
        if(isset($_REQUEST["window"])){
            switch($_REQUEST["window"])
            {
                //si se va a ejecutar en el marco central
                case "central":
                    $this -> centralFrame($_REQUEST["num_servic"]);
                break;

                //si se va a ejecutar en el marco de menu
                case "menu":
                    $this -> menuFrame();
                break;
                
                case "time":
                     $this->mtime();
                break;

                default:
                break;
            }
        }
        else
            //si no se ha elegido donde ejecutar la operacion
            $this -> frames();
      }
      else{
        header("Location: session.php");
      } 
      

      $_SESSION["BASE_DATOS"]=BASE_DATOS;
			$_SESSION["DIR_APLICA_CENTRAL"]=DIR_APLICA_CENTRAL;
			$_SESSION["ESTILO"]=ESTILO;
			$_SESSION["USUARIO"]=USUARIO;
      $_SESSION["CLAVE"]=CLAVE;
      $_SESSION["HOST"]=HOST;
			$_SESSION["HOST_P"]=HOST_P;

      $_SESSION["NOM_URL_APLICA"]=NOM_URL_APLICA;

    }

    function inicio_sesion()
    {
                 //asignamos los datos de la sesion
                 session_start();
                 //validateAuthToken($_SESSION['datos_usuario']['cod_usuari'], "2", session_id(), 30, $this -> conexion);

                if (session_id() == $_SESSION["id"])
                {
                $datos = $_SESSION["datos_usuario"];
                
                        //crear el objeto que maneja los mensajes del usuario
                        $this -> usuario_aplicacion = new Usuari($datos["cod_usuari"]);
                        $this -> usuario_aplicacion -> listar($this -> conexion);
                        $datos = $this -> usuario_aplicacion -> retornar();

                        $_SESSION['codigo'] = $this -> codigo;
                        $_SESSION['conexion'] = $this -> conexion;
                        $_SESSION['usuario_aplicacion'] = $this -> usuario_aplicacion;

                       //verificar si el usuario tiene perfil o es independiente
                       if($datos["cod_perfil"] != "")
                          $permiso = new Aplica_Perfil($this -> codigo, $datos["cod_perfil"]);
                       else
                          $permiso = new Aplica_Usuari($this -> codigo, $datos["cod_usuari"]);

                           //si el usuario tiene permiso se autentica
                      if($permiso -> listar($this -> conexion))
                         $us_autenticado = 1;

                         return $us_autenticado;
            }
            else
             {
                     header("Location: session.php");
             }
     }

    //Con esta funcion se generan los marcos de al aplicacion.
    function frames()
    {
        echo "<!DOCTYPE html>
               <head>
                <title>".ESAD." ".NOMSAD."</title>
                <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
               </head>
               <frameset id=\"framesetID\" cols=\"190,*\" rows=\"*\" frameborder=\"NO\" framespacing=\"0\">
                <frame name=\"menuFrame\" src=\"index.php?window=menu\"  scrolling=\"yes\" noresize>
                <frame name=\"centralFrame\" id=\"centralFrameID\" src=\"index.php?window=central&op=inicio\">
               </frameset>
              </html>";
    }
	
	function centralFrame()
	{
        $datos_usuario = $_SESSION["datos_usuario"];
        
        $pagina = explode('?',$_SERVER['REQUEST_URI']);
        $url =  $_SERVER['HTTP_HOST'].$pagina[0];
        $url2 =  $_SERVER['HTTP_HOST'].substr($pagina[0],0,-9);
        
        $query = "SELECT TIMEDIFF( CONCAT( `fec_suspen` , ' ', `hor_suspen` ) , NOW( ) ) AS timer
                    FROM ".BASE_DATOS.".tab_genera_suspen a
                   WHERE a.cod_usuari = '".$datos_usuario["cod_usuari"]."' 
                     AND a.ind_suspen = 1 ";
        $consulta = new Consulta($query, $this -> conexion);
        $usuari = $consulta -> ret_matriz();
        
        echo "<SCRIPT type=\"text/javascript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
        
        echo '
          <script type="text/javascript">   
                function SATMenuEvent()
                {
                    var hidden = document.getElementById( "MenuRowHiddenID" );
                    if ( hidden.value == "left" ){
                        hidden.value = "right";
                    }else{
                      hidden.value = "left";
                    }
                    var frameset = "framesetID";
                    var image1 = document.getElementById( "MenuRow1ID" );
                    var image2 = document.getElementById( "MenuRow2ID" );
                    cols = "0, *";
                    if( cols == parent.document.getElementById( frameset ).cols )
                    {
                         parent.document.getElementById( frameset ).cols = "190, *";
                         image1.title = "Ocultar Men&uacute;";
                         image2.title = "Ocultar Men&uacute;";
                    }
                    else
                    {
                         parent.document.getElementById( frameset ).cols = "0, *";
                         image1.title = "Mostrar Men&uacute;";
                         image2.title = "Mostrar Men&uacute;";
                    }
                    return true;
                }


                function OnMouseOverMenuRow( dir )   {
                    var way = document.getElementById( "MenuRowHiddenID" ).value;
                    var image1 = document.getElementById( "MenuRow1ID" );
                    var image2 = document.getElementById( "MenuRow2ID" );
                    image1.src = "../"+dir+"/imagenes/ryu_calendar/row_"+way+"_hover.gif";
                    image2.src = "../"+dir+"/imagenes/ryu_calendar/row_"+way+"_hover.gif";
                }


                function OnMouseOutMenuRow( dir )   {
                    var way = document.getElementById( "MenuRowHiddenID" ).value;
                    var image1 = document.getElementById( "MenuRow1ID" );
                    var image2 = document.getElementById( "MenuRow2ID" );
                    image1.src = "../"+dir+"/imagenes/ryu_calendar/row_"+way+".gif";
                    image2.src = "../"+dir+"/imagenes/ryu_calendar/row_"+way+".gif";
                }
 
          </script>';
        if(count($usuari)>0)
        {
          echo '
          <script type="text/javascript">   
                function zeroFill( number, width )
                {
                    width -= number.toString().length;
                    if ( width > 0 )
                    {
                        return new Array( width + (/\./.test( number ) ? 2 : 1) ).join( "0" ) + number;
                    }
                    return number + ""; // always return a string
                }
                   
                function restarSegundo()
                {
                    var time = $("#supendID").html();
                    var hour = $("#hourID").val();
                    time = time.split(":");

                    var newDate = new Date(0, 0, 0, time[0], time[1], time[2] - 1);
                    if( parseInt( time[1] ) == 0 && parseInt( time[2] ) == 0 )
                    {
                     hour--;
                     $("#hourID").val(hour);
                    }
                    
                    if(parseInt(time[0])== 0 && parseInt(time[1])== 0 &&  parseInt(time[2])== 0)
                    {
                        parent.document.location = "https://'.$url2.'session.php?op=2&usuario='.$datos_usuario["cod_usuari"].'"; 
                    }
                    else
                    {
                       $("#supendID").html(zeroFill( hour, 2 )+":"+zeroFill( newDate.getMinutes(), 2 )+":"+zeroFill( newDate.getSeconds(), 2 ));
                    }

                }

                setInterval("restarSegundo();", 1000);
          </script>';
        }
        
		//Perfil PRUEBAS 13
		if(!$_REQUEST["cod_servic"])
		{
			$query = "SELECT a.ind_inidet
					  FROM ".BASE_DATOS.".tab_config_parame a
					  WHERE a.ind_inidet = '1' ";
			
			$consulta = new Consulta($query, $this -> conexion);
			$servic_ini = $consulta -> ret_matriz();
			
			if( !$servic_ini )
				$_REQUEST["cod_servic"] = "1366";
			else
				$_REQUEST["cod_servic"] = "1366";

      if(  $_SESSION[datos_usuario][cod_perfil] == '1' )
      {
          $_REQUEST["cod_servic"] = "1366";
      }	
			if(  $_SESSION[datos_usuario][cod_perfil] == '7' )
			{
				$_REQUEST["cod_servic"] = "1366";
			}
			else if( $_SESSION[datos_usuario][cod_perfil] == '22' )
			{
				$_REQUEST["cod_servic"] = "506";
			}
			else if( $_SESSION[datos_usuario][cod_perfil] == '719' )
			{
				$_REQUEST["cod_servic"] = "7075";
			}
      else if( $_SESSION[datos_usuario][cod_perfil] == '704' )
			{
				$_REQUEST["cod_servic"] = "1315";
			}
		}
		
		$servicio = new Servic($_REQUEST[cod_servic]);
		
		if($servicio -> listar($this -> conexion))
			$datos_servicio = $servicio -> retornar();

            $titulo = $datos_servicio["nom_servic"];

        $pagina_central = new Pagina("".ESAD."", "#FFFFFF", 0, 0, 0, 0, 0, "../".DIR_APLICA_CENTRAL."/estilos/".ESTILO."/css/estilos.css", $datos_servicio["bod_jscrip"], "../".DIR_APLICA_CENTRAL.'/'.$datos_servicio[rut_jscrip]);

        echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"../".DIR_APLICA_CENTRAL."/estilos/".ESTILO."/css/dhtmlgoodies_calendar.css?random=20051112\" media=\"screen\"></LINK>\n";
        echo "<SCRIPT type=\"text/javascript\" src=\"../".DIR_APLICA_CENTRAL."/js/dhtmlgoodies_calendar.js?random=20060118\"></script>\n";
        echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"../".DIR_APLICA_CENTRAL."/estilos/navbar.css?random=20051112\" media=\"screen\"></LINK>\n";
         $cod_hijo = $datos_servicio["cod_servic"];
           while($cod_hijo)
              {

                $query = "SELECT a.cod_servic, a.nom_servic
                            FROM ".CENTRAL.".tab_genera_servic a,
                                 ".CENTRAL.".tab_servic_servic b 
                           WHERE a.cod_servic = b.cod_serpad 
                             AND b.cod_serhij = " . $cod_hijo ." 
                        ORDER BY a.ind_ordenx, a.nom_servic ASC";

                $consulta = new Consulta($query, $this -> conexion);
                if($consulta -> ret_num_rows())
                {

                  $padre = $consulta -> ret_arreglo();
                  $titulo = $padre["nom_servic"] . " > " . $titulo;
                  $cod_hijo = $padre["cod_servic"];

               }
               else
                  $cod_hijo = 0;

            }

           if(($_REQUEST["cod_servic"] == "1308" || $_REQUEST["cod_servic"] == "499" || $_REQUEST["cod_servic"] == "1315" || $_REQUEST["cod_servic"] == "1410" || $_REQUEST["cod_servic"] == "1415" || $_REQUEST["cod_servic"] == "1420")&&($_REQUEST[opcion] == '1' || ($_REQUEST["cod_servic"] == "1420" && $_REQUEST[opcion] == '2') || ($_REQUEST["cod_servic"] == "34219" && $_REQUEST[option] == 'getExcelLlamadas') ) || ($_REQUEST["cod_servic"] == 1))
           	echo '';
           else
           {
           
            echo "\n    <TABLE BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\" WIDTH=\"100%\">

                      
                      ";
            if(count($usuari)>0)
            {
                //$usuari[0]['timer'] = '00:01:03';
                $hour = explode( ":", $usuari[0]['timer'] );
                $hour = $hour[0];
                echo '  <!-- Suspension -->
                            <style type="text/css">
                              .style1
                              {
                                font-family: Arial, Helvetica, sans-serif;
                                font-weight: bold;
                                font-size: 14px;
                                color:#990000
                              }

                              .inform
                              {
                                border: 1px solid #cccccc;
                                border-radius:5px;
                                background-color: #ffffff;
                                width: 550px;
                                padding: 10px;

                                margin: 0px auto;
                                margin-bottom: 15px;

                                /*-------------------------*/
                                opacity: .90;
                                -moz-opacity: .90;
                                filter:alpha(opacity=90);
                                /*-------------------------*/
                              }
                            </style>
                        <!-- Suspension -->
                        <div class="inform style1">
                            <center>
                                <!-- <img src="http://flired.intrared.net/ap/sadc_standa/imagenes/logo_pow_intrared.gif"  /> -->
                                <img src="../'.DIR_APLICA_CENTRAL.'/imagenes/logo_pow_intrared.gif"  />
                                <br />
                                <br />
                                <span style="color: red; font-weight: bold;">El servicio ser&aacute; suspendido en las pr&oacute;xima </span>
                                <span id="supendID">'.$usuari[0]['timer'].'</span>
                                <span style="color: red; font-weight: bold;"> horas</span>
                                , debido a que su empresa presenta una mora en el pago.
                                <br />
                                <br />
                                Comun&iacute;quese al tel&eacute;fono (+57) (1) 742 9002 o al celular (+57) 314 394 9492.
                            </center>
                            <input type="hidden" id="hourID" value="'.$hour.'">
                        </div>';
            }            
            $ipCliente =$_SERVER['HTTP_X_REAL_IP'];
            $cod_usuari = $_SESSION['datos_usuario']['cod_usuari'];
            $cod_perfil = $_SESSION['datos_usuario']['cod_perfil'];

            if(in_array($cod_perfil, array(1, 2, 8, 7))){
              echo '<div id="myCustomNavbar">
                      <div class="navbar-pri">
                        <!-- Sección izquierda: Título, Fecha/Hora e IP -->
                        <div class="navbar-left">
                          <div class="navbar-title">Avansat GL</div>
                          <div class="navbar-user">'.$cod_usuari.'</div>
                          <div class="navbar-datetime" id="navbarDate"></div>
                          <div class="navbar-ip">'.$ipCliente.'</div>
                          <input type="hidden" name="standar" id="standar" value="'.DIR_APLICA_CENTRAL.'">
                        </div>
                        
                        <!-- Sección derecha: Botón y Dropdown de Alertas -->
                        <div class="alert-container-pri">
                          <button class="alert-button-pri" onclick="toggleAlerts()">
                            Alertas Pendientes
                            <span class="badge">0</span>
                          </button>
                          <!-- Dropdown con las notificaciones -->
                          <div id="alert-dropdown" class="alert-dropdown">
                          </div>
                        </div>
                      </div>
                    </div>';

            }
            
            echo " 
                    <TR>
                        <TD>
                            <TABLE ALIGN=\"left\" BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\" WIDTH=\"100%\">
                                <TR>
                                    <TD>
                                        <IMG NAME=\"top_01\" SRC=\"../".DIR_APLICA_CENTRAL."/estilos/".ESTILO."/imagenes/top_01.gif\" WIDTH=\"12\" HEIGHT=\"32\" BORDER=\"0\" ALT=\"\">
                                    </TD>
                                    <TD NOWRAP BACKGROUND=\"../".DIR_APLICA_CENTRAL."/estilos/".ESTILO."/imagenes/top_02.gif\">
                                        <TABLE WIDTH=\"300\" BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\">
                                        <TR>
                                            <TD HEIGHT=\"30\" VALIGN=\"MIDDLE\" NOWRAP CLASS=\"texto1\">" . $titulo." </TD>
                                        </TR>
                                    </TABLE>
                                </TD>
                                <TD>
                                    <IMG NAME=\"top_04\" SRC=\"../".DIR_APLICA_CENTRAL."/estilos/".ESTILO."/imagenes/top_04.gif\" WIDTH=\"145\" HEIGHT=\"32\" BORDER=\"0\" ALT=\"\">
                                </TD>
                                <TD BACKGROUND=\"../".DIR_APLICA_CENTRAL."/estilos/".ESTILO."/imagenes/top_05.gif\"WIDTH=\"100%\"></TD>
                            </TR>
                            <TR>
                                <td colspan='5'>
                                <img id=\"MenuRow1ID\" src=\"../satt_standa/imagenes/ryu_calendar/row_left.gif\" style=\"cursor:pointer\" title=\"Ocultar Men?\" onmouseover=\"OnMouseOverMenuRow( 'satt_standa' );\" onmouseout=\"OnMouseOutMenuRow( 'satt_standa' );\" onclick=\"SATMenuEvent();\">
                                    <input type=\"hidden\" id=\"MenuRowHiddenID\" value=\"left\">
                                </td>
                            </TR>
                        </TABLE>
                    </TD>
                </TR>";
           }

           //Incluye la clase para validar 
           
          include("../".DIR_APLICA_CENTRAL.'/lib/general/suspensiones.php');
          
          $sus_terceros = new suspensiones($this -> conexion);
          

          if(in_array($_SESSION['datos_usuario']['cod_perfil'], array(1, 2, 7, 8))){
            if(in_array($_REQUEST["cod_servic"], array(1366))){
              //Funcion Alerta de suspensi?n empresas
              $data = $sus_terceros->SetSuspensiones(null, null, 1);
              if (count($data['suspendido']) > 0) {
                $this->alertMensajeSuspenAdmin($data);
              }
            }
          }else{
          	$data = $sus_terceros->SetSuspensiones(null, $_SESSION['datos_usuario']['cod_usuari']);
          	//Funcion bloqueo servicios
  	        $this->bloServSuspension($data);
  	        //Funcion Alerta de suspensi?n
  	        $this->alertMensajeSuspension($data);
          }

          

          include("../".DIR_APLICA_CENTRAL.'/'.$datos_servicio['rut_archiv']);

           if(( $_REQUEST["cod_servic"] == "1308" || $_REQUEST["cod_servic"] == "499" || $_REQUEST["cod_servic"] == "1315" || $_REQUEST["cod_servic"] == "1410" || $_REQUEST["cod_servic"] == "1415" || $_REQUEST["cod_servic"] == "1420")&&($_REQUEST[opcion] == '1' || ($_REQUEST["cod_servic"] == "1420" && $_REQUEST[opcion] == '2')|| ($_REQUEST["cod_servic"] == "34219" && $_REQUEST[option] == 'getExcelLlamadas')))
           	echo '';
           else
           echo "\n    
           <TR>
            <td colspan='5'>
            <img id=\"MenuRow2ID\" src=\"../satt_standa/imagenes/ryu_calendar/row_left.gif\" style=\"cursor:pointer\" title=\"Ocultar Men?\" onmouseover=\"OnMouseOverMenuRow( 'satt_standa' );\" onmouseout=\"OnMouseOutMenuRow( 'satt_standa' );\" onclick=\"SATMenuEvent();\">
            <input type=\"hidden\" id=\"MenuRowHiddenID\" value=\"left\">
            </td>
            </TR>
            <TR>
            <TD><TABLE ALIGN=\"center\" BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\" WIDTH=\"100%\">
            <TR>
            <TD BACKGROUND=\"../".DIR_APLICA_CENTRAL."/estilos/".ESTILO."/imagenes/bottom_02.gif\"WIDTH=\"100%\"></TD>
            <TD><IMG NAME=\"bottom_03\" SRC=\"../".DIR_APLICA_CENTRAL."/estilos/".ESTILO."/imagenes/bottom_03.gif\" WIDTH=\"145\" HEIGHT=\"32\" BORDER=\"0\" ALT=\"\"></TD>
            <TD NOWRAP BACKGROUND=\"../".DIR_APLICA_CENTRAL."/estilos/".ESTILO."/imagenes/bottom_05.gif\"><TABLE WIDTH=\"300\" BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\">
            <TR>
            <TD HEIGHT=\"30\" align=\"center\" VALIGN=\"MIDDLE\" NOWRAP CLASS=\"texto1\">&nbsp;" . $titulo." </TD>
            </TR>
            </TABLE></TD>
            <TD><IMG NAME=\"bottom_06\" SRC=\"../".DIR_APLICA_CENTRAL."/estilos/".ESTILO."/imagenes/bottom_06.gif\" WIDTH=\"12\" HEIGHT=\"32\" BORDER=\"0\" ALT=\"\"></TD>
            </TR>
            </TABLE></TD>
            </TR>
          </TABLE>";
        echo '<script src="../'.DIR_APLICA_CENTRAL.'/js/navbar.js"></script>';
        $pagina_central -> cerrar();
    }


    function mtime(){
       $datos_usuario = $_SESSION["datos_usuario"];
      
       $query = "SELECT TIMEDIFF( CONCAT( `fec_suspen` , ' ', `hor_suspen` ) , NOW( ) ) AS timer
                    FROM ".BASE_DATOS.".tab_genera_suspen a
                   WHERE a.cod_usuari = '".$datos_usuario["cod_usuari"]."' 
                     AND a.ind_suspen = 1 LIMIT 1";

       $consulta = new Consulta($query, $this -> conexion);
       $time = $consulta -> ret_matriz();
       
       echo $time[0]['timer'];
      
    }



    //Con esta funcion se presenta el menor

    function menuFrame()
    {    

      $datos_usuario = $this -> usuario_aplicacion -> retornar();

    //se revisa si el usuario tiene perfil o es usuario independiente
    if(!$datos_usuario["cod_perfil"])
    {
        $tabla_permisos = "tab_servic_usuari";
        $tipo_permiso = "cod_usuari";
        $cod_permiso = $datos_usuario["cod_usuari"];
    }
    else
    {
        $tabla_permisos = "tab_perfil_servic";
        $tipo_permiso = "cod_perfil";
        $cod_permiso = $datos_usuario["cod_perfil"];
        $query = "SELECT nom_perfil
                  FROM  tab_genera_perfil
                  WHERE cod_perfil = '".$datos_usuario[cod_perfil]."'";
        $consulta = new Consulta($query, $this->conexion);
        $nom = $consulta -> ret_arreglo();
        $datos_usuario["nom_perfil"] = $nom[0];
    }

    include_once( "../".DIR_APLICA_CENTRAL."/lib/general/menu_new.inc" );  
    $menu = new Menu($datos_usuario, $tabla_permisos, $tipo_permiso, $cod_permiso, $this -> codigo, $this -> conexion);
}

function getRealIP() {
  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      return $_SERVER['HTTP_CLIENT_IP'];
  } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      return $_SERVER['HTTP_X_FORWARDED_FOR'];
  } else {
      return $_SERVER['REMOTE_ADDR'];
  }
}



    //Esta funcion publica los items del meno desplegados cada nivel.

    function publicar_menu($servicios, $act_nivel, $desplegados, $txt_desplegados)

    {
          //este truquillo intercala el bullet de acuerdo al nivel

    $nivel_identacion = $act_nivel % 5;

    //La identaci?n es el n?mero de espacios que se deja para dar la apariencia de cascada junto con el bullet

    $identacion = "";

    for ($i = 1; $i <= $act_nivel; $i++)

      $identacion = "&nbsp;&nbsp;" . $identacion;

    //Se generan los links de cada item del menu en el nivel actual

    for ($i = 0; $i < sizeof($servicios[$act_nivel]); $i++) {

      $servic = new Servic($servicios[$act_nivel][$i]["cod_servic"]);

      $servic -> listar($this -> conexion);

      $datos_servic = $servic -> retornar();

      //Si el servicio es sim,plemente el padre de un grupo no tendra un archivo para incluir

      if ($datos_servic["rut_archiv"] == "") {

        //los txt_desplegados_representan el vector que almacena la opcion seleccionada en cada nivel

        $tmp_txt_desplegados = $txt_desplegados . "&desplegados[$act_nivel]=$datos_servic[cod_servic]";

        $href = "index.php?window=menu$tmp_txt_desplegados&menant=$datos_servic[cod_servic]";

        $target = "menuFrame";

      }

      //Si no lo es se hace el link p?ra que apunte al servicio en el frame Central

      else {

        $href = "index.php?window=central&cod_servic=$datos_servic[cod_servic]&menant=$datos_servic[cod_servic]";

        $target = "centralFrame";

      }

      echo "\n<tr><td class=\"menu_niv_$nivel_identacion\">$identacion<a href=\"$href\" id=\"$datos_servic[cod_servic]\" target=\"$target\"><img width=\"10\" height=\"10\" border=\"0\" src=\"../" . DIR_APLICA_CENTRAL . "/imagenes/bullet_$nivel_identacion.gif\">&nbsp;$datos_servic[nom_servic]</a></td></tr>";

      //Si el servicio ha sido desplegado se llama nuevamente a la funci?n para que despliegue a partir de este item.

      if (isset($desplegados[$act_nivel]) && $desplegados[$act_nivel] == $datos_servic["cod_servic"])

        $this -> publicar_menu($servicios, $act_nivel + 1, $desplegados, $tmp_txt_desplegados);

    }
    }

    function mGetCodigo(){
      return $this -> codigo;
    }

    function mGetConexion(){
      return $this -> conexion;
    }

    function mGetUsuarioAplicacion(){
      return$this -> usuario_aplicacion;
    }

    /*! \fn: bloServSuspension
	 *  \brief: Valida si tiene servicio suspendidos o a punto de suspender para bloquear modulos
	 *  \author: Ing. Luis Manrique
	 *  \date: 27-02-2020
	 *  \date modified: dd/mm/aaaa
	 *  \param: $data = Array de los suspendidos
	 *  \return: html
	 */

    function bloServSuspension($data){
      if(in_array($_REQUEST["cod_servic"], array(20160426, 20151236, 20151238))){
        $ban = 0;
        $fact = []; 
        //Si retorna informaci?n de suspendidos
        if(!empty($data)){
          foreach ($data['suspendido'] as $ident => $campos) {
            $ban = 1;
            $fact [$ident]['num_factur'] = $campos['num_factur'];
            $fact [$ident]['val_totalx'] = "$".$campos['val_totalx'];
          }

          //Verifica que si exita suspensi?n
          if ($ban == 1) {
            echo '<script src="../'.DIR_APLICA_CENTRAL.'/js/lib/bootstrap/dist/js/bootstrap.min.js"></script>';
            echo '<link href="../'.DIR_APLICA_CENTRAL.'/js/lib/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>';

            $tittle = 'Suspension!';
            $type = 'info';
            $viewButton = 'false';
            $backdrop = 'false';
            $colorBoton = null;

            $body = 'Recuerde que se encuentra suspendido el servicio de seguimiento por mora en las siguientes facturas:<br><br>\'+
                          \'<table class="table table-sm text-center">\'+
                            \'<tr>\'+
                              \'<th class="text-center">Factura</th>\'+
                              \'<th class="text-center">Valor</th>\'+
                            \'</tr>\'+'; 
                          foreach ($fact as $key => $value) {
                  $body .= '\'<tr>\'+
                              \'<td>'.$value['num_factur'].'</td>\'+
                              \'<td>'.$value['val_totalx'].'</td>\'+
                            \'</tr>\'+';
                          }

                $body .= '\'</table>\'+
                            \'Si ya realizo el pago correspondiente, por favor enviar soporte al correo: <strong>pagos@grupooet.com</strong>';

            $this->mensajeSuspension($tittle, $body, $type, $viewButton, $backdrop, $colorBoton);
            die();
          }
        } 
      }
    }

    /*! \fn: alertMensajeSuspension
	 *  \brief: Valida si tiene servicio suspendidos o a punto de suspender para mostrar alerta de suspensi?n
	 *  \author: Ing. Luis Manrique
	 *  \date: 27-02-2020
	 *  \date modified: dd/mm/aaaa
	 *  \param: $data = Array de los suspendidos
	 *  \return: html
	 */

    function alertMensajeSuspension($data){
      //Valida si existen datos
      if(!empty($data)){

        //Valida si existe la variable de sesion para crearla
        if(!isset($_SESSION['datos_usuario']['ale_suspen'])){
          $_SESSION['datos_usuario']['ale_suspen'] = 0;
        }

        // echo "<pre>222";
        // print_r($_SESSION['datos_usuario']);
        // echo "</pre>";
        // exit();
        
        //Valida variable de sesi?n
        if($_SESSION['datos_usuario']['ale_suspen'] == 0){
          
          //Variables necesarias para alerta
          $tittle = utf8_encode('Aviso de suspensiÃ³n!');
          $viewButton = 'true';
          $backdrop = 'true';

          //Recorre la data para asignar informaci?n de las facturas
          foreach ($data as $estado => $mData) {
            // if($mData[]){

            // }
            foreach ($mData as $ident => $campos) {

              //Valida estados
              If($estado == 'suspendido'){
                //Variables necesarias para alerta
                $body = "Se ha suspendido el servicio de su cuenta, si ya realiz? el pago, por favor enviar el soporte al correo: <b>pagos@grupooet.com.</b>";
                $type = 'error';
                $colorBoton = "#ff0000";
              }else{
                 //Variables necesarias para alerta
                $body = "Su servicio ser? suspendido el d?a ".$campos['fec_suspen'].', por favor realice el pago antes de que sea suspendida la cuenta.';
                $type = 'warning';
                $colorBoton = "#f8bb86";
              }

              //Ejecuta funci?n que crea alerta
              $this->mensajeSuspension($tittle, $body, $type, $viewButton, $backdrop, $colorBoton);
              
              $_SESSION['datos_usuario']['ale_suspen'] = 1; 

              return false;
            }
          }
        }
      }
    }

    /*! \fn: alertMensajeSuspension
	 *  \brief: Valida si tiene servicio suspendidos o a punto de suspender para mostrar alerta de suspensi?n
	 *  \author: Ing. Luis Manrique
	 *  \date: 27-02-2020
	 *  \date modified: dd/mm/aaaa
	 *  \param: $data = Array de los suspendidos
	 *  \return: html
	 */

    function alertMensajeSuspenAdmin($data){
      if(!empty($data)){
        
        if(in_array($_REQUEST["cod_servic"], array(1366))){
          	$tittle = 'Aviso de suspensi&oacute;n';
          	$viewButton = 'true';
          	$backdrop = 'true';
          	$emp_suspen = [];
          	$type = 'info';
	        $colorBoton = "";

          	foreach ($data['suspendido'] as $ident => $campos) {
            	$emp_suspen[] = $campos['abr_tercer'];
          	}

          	$body = 'Las siguientes empresas se encuentran en estado de suspensi&oacute;n:<br><br>\'+';
          		foreach (array_unique($emp_suspen) as $key => $value) {
                 	 $body .= '\''.$value.'<br>\'+';
                }
            $body .= '\'';
	        

           $this->mensajeSuspension($tittle, $body, $type, $viewButton, $backdrop, $colorBoton);
        }
      }
    }

    /*! \fn: mensajeSuspension
	 *  \brief: Crea Alerta por JS dando informaci?n
	 *  \author: Ing. Luis Manrique
	 *  \date: 27-02-2020
	 *  \date modified: dd/mm/aaaa
	 *  \param: $data = Array de los suspendidos
	 *  \return: html
	 */

    function mensajeSuspension($tittle, $body, $type, $viewButton, $backdrop, $colorBoton = null){
        $script .= '<!-- Toastr -->';
        $script .='<script src="../' . DIR_APLICA_CENTRAL . '/js/sweetalert2.all.8.11.8.js"></script>';

        $script .= "<script type='text/javascript'> 
                      $(function(){
                        Swal.fire({
                          title:'".utf8_decode($tittle)."',
                          html: '".utf8_decode($body)."',
                          type: '".$type."',
                          backdrop: $backdrop,
                          showConfirmButton: $viewButton,
                          confirmButtonColor: '".$colorBoton."',
                          confirmButtonText: 'Listo'
                        })
                      });
                            
                    </script>";
        echo $script;
    }

}//fin clase

ini_set("session.save_handler", "files");
$aplicacion = new Aplicacion_Seguridad();

?>
