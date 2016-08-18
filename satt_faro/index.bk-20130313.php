<?php

/*
global $HTTP_POST_FILES;
global $HTTP_GET_FILES;
*/

setlocale(LC_TIME,"es_ES");
//error_reporting(E_ALL);

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
        $this -> conexion = new Conexion("localhost", USUARIO, CLAVE, "".BASE_DATOS."");//Se crea la conexion a la base de datos
        $this -> principal();
    }

    function Principal()
    {
      if($this -> inicio_sesion())
      {
        if(isset($GLOBALS["window"]))
        {
            switch($GLOBALS["window"])
            {
                //si se va a ejecutar en el marco central
                case "central":
                    $this -> centralFrame($GLOBALS["num_servic"]);
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
      else header("Location: session.php");

      $_SESSION["BASE_DATOS"]=BASE_DATOS;
			$_SESSION["DIR_APLICA_CENTRAL"]=DIR_APLICA_CENTRAL;
			$_SESSION["ESTILO"]=ESTILO;
			$_SESSION["USUARIO"]=USUARIO;
			$_SESSION["CLAVE"]=CLAVE;

      $_SESSION["NOM_URL_APLICA"]=NOM_URL_APLICA;

    }

    function inicio_sesion()
    {
                 //asignamos los datos de la sesion
                 session_start();

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
                <frame name=\"centralFrame\" src=\"index.php?window=central&op=inicio\">
               </frameset>
              </html>";
    }
	
	function centralFrame()
	{
		//Perfil PRUEBAS 13
		if(!$GLOBALS["cod_servic"])
		{
			$query = "SELECT a.ind_inidet
					  FROM ".BASE_DATOS.".tab_config_parame a
					  WHERE a.ind_inidet = '1' ";
			
			$consulta = new Consulta($query, $this -> conexion);
			$servic_ini = $consulta -> ret_matriz();
			
			if( !$servic_ini )
				$GLOBALS["cod_servic"] = "3302";
			else
				$GLOBALS["cod_servic"] = "1366";
				
			if(  $_SESSION[datos_usuario][cod_perfil] == '7' )
			{
				$GLOBALS["cod_servic"] = "1366";
			}
			else if( $_SESSION[datos_usuario][cod_perfil] == '22' )
			{
				$GLOBALS["cod_servic"] = "506";
			}
		}
		
		$servicio = new Servic($GLOBALS[cod_servic]);
		
		if($servicio -> listar($this -> conexion))
			$datos_servicio = $servicio -> retornar();

            $titulo = $datos_servicio["nom_servic"];

        $pagina_central = new Pagina("".ESAD."", "#FFFFFF", 0, 0, 0, 0, 0, "../".DIR_APLICA_CENTRAL."/estilos/".ESTILO."/css/estilos.css", $datos_servicio["bod_jscrip"], "../".DIR_APLICA_CENTRAL.'/'.$datos_servicio[rut_jscrip]);

      echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"../".DIR_APLICA_CENTRAL."/estilos/".ESTILO."/css/dhtmlgoodies_calendar.css?random=20051112\" media=\"screen\"></LINK>\n";
        echo "<SCRIPT type=\"text/javascript\" src=\"../".DIR_APLICA_CENTRAL."/js/dhtmlgoodies_calendar.js?random=20060118\"></script>\n";

         $cod_hijo = $datos_servicio["cod_servic"];
           while($cod_hijo)
              {

                $query = "SELECT a.cod_servic, a.nom_servic
                         FROM ".CENTRAL.".tab_genera_servic a,
                              ".CENTRAL.".tab_servic_servic b

                          WHERE a.cod_servic = b.cod_serpad
                          AND b.cod_serhij = " . $cod_hijo ;

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

           if(($GLOBALS["cod_servic"] == "1308" || $GLOBALS["cod_servic"] == "1315" || $GLOBALS["cod_servic"] == "1410" || $GLOBALS["cod_servic"] == "1415" || $GLOBALS["cod_servic"] == "1420")&&($GLOBALS[opcion] == '1' || ($GLOBALS["cod_servic"] == "1420" && $GLOBALS[opcion] == '2')))
           	echo '';
           else
	           echo "\n    <TABLE BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\" WIDTH=\"100%\">
      <TR>
        <TD><TABLE ALIGN=\"left\" BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\" WIDTH=\"100%\">
          <TR>
            <TD><IMG NAME=\"top_01\" SRC=\"../".DIR_APLICA_CENTRAL."/estilos/".ESTILO."/imagenes/top_01.gif\" WIDTH=\"12\" HEIGHT=\"32\" BORDER=\"0\" ALT=\"\"></TD>
            <TD NOWRAP BACKGROUND=\"../".DIR_APLICA_CENTRAL."/estilos/".ESTILO."/imagenes/top_02.gif\"><TABLE WIDTH=\"300\" BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\">
                <TR>
                  <TD HEIGHT=\"30\" VALIGN=\"MIDDLE\" NOWRAP CLASS=\"texto1\">" . $titulo." </TD>
                </TR>
            </TABLE></TD>
            <TD><IMG NAME=\"top_04\" SRC=\"../".DIR_APLICA_CENTRAL."/estilos/".ESTILO."/imagenes/top_04.gif\" WIDTH=\"145\" HEIGHT=\"32\" BORDER=\"0\" ALT=\"\"></TD>
            <TD BACKGROUND=\"../".DIR_APLICA_CENTRAL."/estilos/".ESTILO."/imagenes/top_05.gif\"WIDTH=\"100%\"></TD>
          </TR>
        </TABLE></TD>
      </TR>";

                include("../".DIR_APLICA_CENTRAL.'/'.$datos_servicio[rut_archiv]);


           if(($GLOBALS["cod_servic"] == "1308" || $GLOBALS["cod_servic"] == "1315" || $GLOBALS["cod_servic"] == "1410" || $GLOBALS["cod_servic"] == "1415" || $GLOBALS["cod_servic"] == "1420")&&($GLOBALS[opcion] == '1' || ($GLOBALS["cod_servic"] == "1420" && $GLOBALS[opcion] == '2')))
           	echo '';
           else
                        	echo "\n      <TR>
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
        session_start();
        $datos_usuario = $_SESSION["datos_usuario"];

        //se revisa si el usuario tiene perfil o es usuario independiente
        if($datos_usuario["cod_perfil"] == "")
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
        }

        $pagina_menu = new Pagina("".ESAD."","#EEEEEE", 0, 0, 0, 0, 0, "../".DIR_APLICA_CENTRAL."/estilos/".ESTILO."/css/estilos.css","");
        //query para traer el nombre del perfil
        
        
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
        
         if(count($usuari)>0){
          echo '<script type="text/javascript">   
          function callTimeDown(){
              $.ajax({
                  method: "get",
                  url : "https://'.$url.'?window=time",
                  dataType : "text",
                  success: function (text) { 
                    var time = text.split(":"); 
                    if(parseInt(time[0])==0 && parseInt(time[1])==0 &&  time[2]=="00"){
                      parent.document.location = "https://'.$url2.'session.php?op=2&usuario='.$datos_usuario["cod_usuari"].'"; 
                    }else{
                      $("#supendID").html(text); 
                    }
                  }
               });
          }
          setInterval(callTimeDown, 1000); 
          
          </script>';
        }
        
        
        $query = "SELECT a.nom_perfil
                    FROM ".BASE_DATOS.".tab_genera_perfil a
                   WHERE a.cod_perfil = '".$datos_usuario["cod_perfil"]."' ";

        $consulta = new Consulta($query, $this -> conexion);
        $fila= $consulta -> ret_arreglo();

          echo "\n".'  <TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0" BORDER="0">
            <TBODY>
              <TR>
                <TD CLASS="celda_top" ALIGN="CENTER" COLSPAN="2" HEIGHT="80">&nbsp;</TD>
              </TR>
              <TR>';
              
             if(count($usuari)>0){
                
                echo '<!-- Suspension -->
                
                <tr>
                  <td colspan="2" align="center" style="background: #fff; color: red; font-weight: bold;">Su servicio será suspendido en:</td>
                </tr>
                <tr>
                  <td colspan="2" align="center" style="background: #fff; color: red; font-size: 20px; font-weight: bold;" id="supendID">'.$usuari[0]['timer'].'</td>
                </tr>
                
                <!-- Suspension --> ';
                
              } 
              echo '<TD ALIGN="RIGHT" CLASS="celda_etiqueta">Usuario:</TD><TD ALIGN="LEFT" CLASS="celda_info">'.$datos_usuario[nom_usuari].'</TD>
              </TR>
              <TR>
                <TD ALIGN="RIGHT" CLASS="celda_etiqueta">Perfil:</TD><TD ALIGN="LEFT" CLASS="celda_info">'.$fila[0].'</TD>
              <TR>
                <TD  height="17" COLSPAN="2" BGCOLOR="#FFFFFF" class=titulo_menu><CENTER>
                    <A HREF="session.php?op=2&usuario='.$datos_usuario[cod_usuari].'" TARGET="_parent"><FONT COLOR="#333333"><B>Cerrar Sesi&oacute;n</B></FONT></A>
                </CENTER></TD>
              </TR>
              <TR>
                <TD  height="17" COLSPAN="2" BGCOLOR="#FFFFFF" BACKGROUND="../'.DIR_APLICA_CENTRAL.'/estilos/'.ESTILO.'/imagenes/backg_03.gif"></TD>
              </TR>
            </TBODY>
          </TABLE>';

        //se traen los servicios de primer nivel sobre los que tiene permiso el usuario
        $query = "SELECT a.cod_servic
                    FROM ".CENTRAL.".tab_genera_servic a,
                         ".BASE_DATOS.".$tabla_permisos b LEFT JOIN
                         ".CENTRAL.".tab_servic_servic c ON b.cod_servic = c.cod_serhij
                   WHERE a.cod_servic = b.cod_servic AND
                         a.cod_aplica = '" . $this -> codigo . "' AND
                         c.cod_serhij IS NULL AND
                         b.$tipo_permiso = '$cod_permiso'
                         ORDER BY a.cod_servic";

        $consulta = new Consulta($query, $this -> conexion);
        $servicios[1] = $consulta -> ret_matriz();

        //Si ya se ha desplegado una opcion del meno se traen de la bd todos los niveles desplegados
        if(isset($GLOBALS["desplegados"]))
        {
            global $desplegados;
            for($i = 1; $i <= sizeof($desplegados); $i++)
            {
                $query = "SELECT a.cod_servic
                            FROM ".BASE_DATOS.".$tabla_permisos a,
                                 ".CENTRAL.".tab_servic_servic b,
                                 ".CENTRAL.".tab_genera_servic c
                           WHERE a.cod_servic = b.cod_serhij AND
                                 a.cod_servic = c.cod_servic AND
                                 b.cod_serpad = '" . $desplegados[$i] . "' AND
                                 a.$tipo_permiso = '$cod_permiso'
                                 ORDER BY c.cod_servic";

                $consulta = new Consulta($query, $this -> conexion);
                $servicios[$i+1] = $consulta -> ret_matriz();
            }
        }

        // Se llama a la funcion recursiva que publica el meno

        echo "\n".'  <TABLE cellspacing=0 cellpadding=2 BORDER="0" WIDTH="100%" onLoad="document.getElementById('.$GLOBALS[cod_servic].').focus()">';

        $this -> publicar_menu($servicios, 1, $desplegados, '');

        echo "\n  </TABLE>";

        // Usuarios en Linea

        echo "\n  <script language=\"javascript\" src=\"../".DIR_APLICA_CENTRAL."/js/pagina_hija.js\"></script>";
// Funciones para Boton Limpiar
    
	echo "\n".'  <script type="text/JavaScript">
	<!--
	function MM_swapImgRestore() { //v3.0
	var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
	}

	function MM_preloadImages() { //v3.0
	var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
	var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
	if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
	}

	function MM_findObj(n, d) { //v4.01
	var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
	d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
	if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
	for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
	if(!x && d.getElementById) x=d.getElementById(n); return x;
	}

	function MM_swapImage() { //v3.0
	var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
	if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
	}
	//-->
  </script>';

        echo "\n  <TABLE WIDTH=\"100%\" BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"1\" bgcolor=\"#FFFFFF\">
            <TBODY>
              <TR>
                <TD ALIGN=\"CENTER\" bgcolor=\"#F0F0F0\" ><BR>
                  
                  <TABLE cellspacing=0 cellpadding=0>
                    <TBODY>";

                if($GLOBALS[vac])

                {

                   $query3 = "DELETE FROM ".BASE_DATOS.".tab_usuari_session
                                WHERE cod_usuari <> '$datos_usuario[cod_usuari]'
                                   OR (cod_usuari = '$datos_usuario[cod_usuari]'
                                  AND host_addr <>  '".getenv("REMOTE_ADDR")."')";

 //                  $consulta3 = new Consulta($query3, $this -> conexion);

                }

            $query = "SELECT a.cod_usuari

                      FROM ".BASE_DATOS.".tab_usuari_session a, ".BASE_DATOS.".tab_genera_usuari b

                      WHERE a.cod_usuari = b.cod_usuari";

            if($datos_usuario["cod_perfil"] != COD_PERFIL_SUPERUSR)

               $query .= " AND b.cod_perfil <> '".COD_PERFIL_SUPERUSR."'";

   //         $consulta = new Consulta($query, $this -> conexion);

     //       $users = $consulta -> ret_resultado();
            if(isset($users))
            {
              while($user = mysql_fetch_array($users))

              {

                    if($user[cod_usuari] != $datos_usuario["cod_usuari"])

                       echo "\n<tr><TD><FONT size=\"1\" COLOR=\"#333333\"><a href=# onClick=\"hija('".$datos_usuario["cod_usuari"]."', '', '".COD_SERVIC_MESSENGER."', 'compose', '$user[cod_usuari]', '".$GLOBALS["nom_basdat"]."', 'a', '250')\"><IMG width=\"10\" height=\"10\" border=\"0\" SRC=\"../".DIR_APLICA_CENTRAL."/estilos/".ESTILO."/imagenes/usuario.gif\" width=10 border=0><B>$user[cod_usuari]</B></a></FONT></TD></tr>";

                    else

                    {

                       echo "\n<tr><TD><FONT size=\"1\" COLOR=\"#333333\"><IMG width=\"10\" height=\"10\" border=\"0\" SRC=\"../".DIR_APLICA_CENTRAL."/estilos/".ESTILO."/imagenes/usuario.gif\" width=10 border=0><B>$user[cod_usuari]</B></FONT></TD></tr>";

                    }

              }
            }

            echo "\n        </TBODY>
                  </table><br>
                </td>
              </tr>
              <TR>
                <TD ALIGN=\"CENTER\" bgcolor=\"#F0F0F0\">
                  <IMG SRC=\"../".DIR_APLICA_CENTRAL."/estilos/".ESTILO."/imagenes/powered_by_intrared_peq.gif\" border=0><BR>
                  <FONT color=#333333 size=1>&#169; 1995 - 2007 Intrared.net Ltda. Todos los Derechos Reservados.</FONT></TD>
              </TR>
            </TBODY>
          </TABLE>";

        $pagina_menu -> cerrar();
    }



    //Esta funcion publica los items del meno desplegados cada nivel.

    function publicar_menu($servicios, $act_nivel, $desplegados, $txt_desplegados)

    {
        //este truquillo intercala el bullet de acuerdo al nivel
        $nivel_identacion = $act_nivel%5;

        //La identacion es el nomero de espacios que se deja para dar la apariencia de cascada junto con el bullet

        $identacion = "";

        for($i = 1; $i <= $act_nivel; $i++)

            $identacion = "&nbsp;&nbsp;" . $identacion;



        //Se generan los links de cada item del menu en el nivel actual

        for($i = 0; $i < sizeof($servicios[$act_nivel]); $i++)

        {

            $servic = new Servic($servicios[$act_nivel][$i]["cod_servic"]);

            $servic -> listar($this -> conexion);

            $datos_servic = $servic -> retornar();



            //Si el servicio es sim,plemente el padre de un grupo no tendra un archivo para incluir

            if(!$datos_servic["rut_archiv"])

            {

                //los txt_desplegados_representan el vector que almacena la opcion seleccionada en cada nivel

                $tmp_txt_desplegados = $txt_desplegados . "&desplegados[$act_nivel]=$datos_servic[cod_servic]";

                $href = "index.php?window=menu$tmp_txt_desplegados&menant=$datos_servic[cod_servic]&usuario=$datos_usuario[cod_usuari]";

                $target = "menuFrame";

            }



            //Si no lo es se hace el link pora que apunte al servicio en el frame Central

            else

            {

                $href = "index.php?window=central&cod_servic=$datos_servic[cod_servic]&menant=$datos_servic[cod_servic]";

                $target = "centralFrame";

            }

            echo "\n       ".'<TR>
                <TD class= "celda_menu_n'.$nivel_identacion.'" onMouseOver="this.className=\'celda_menu_n'.$nivel_identacion.'_Hover\'" onMouseOut="this.className=\'celda_menu_n'.$nivel_identacion.'\'">&nbsp;&nbsp;<A id="'.$datos_servic[cod_servic].'" href="'.$href.'" target="'.$target.'" CLASS="menu_n'.$nivel_identacion.'"><IMG SRC="../'.DIR_APLICA_CENTRAL.'/estilos/'.ESTILO.'/imagenes/bullet_01.gif" WIDTH="10" HEIGHT="10" BORDER="0">&nbsp;'.$datos_servic[nom_servic].'</A></TD>
              </TR>';

//echo "\n<tr><td class=\"menu_niv_$nivel_identacion\">$identacion<a href=\"$href\" target=\"$target\"><img width=\"10\" height=\"10\" border=\"0\" src=\"../".DIR_APLICA_CENTRAL."/imagenes/bullet_$nivel_identacion.gif\">&nbsp;$datos_servic[nom_servic]</a></td></tr>";



            //Si el servicio ha sido desplegado se llama nuevamente a la funcion para que despliegue a partir de este item.

            if(isset($desplegados[$act_nivel]) && $desplegados[$act_nivel] == $datos_servic["cod_servic"])

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

}//fin clase

ini_set("session.save_handler", "files");
$aplicacion = new Aplicacion_Seguridad();

?>
