<?php
/*Autor: Adrian Ignacio Cardona Mora
Ultima Actualizacion: 22-07-2006*/

setlocale(LC_TIME,"es_ES");
date_default_timezone_set("America/Bogota");

//Esta clase manipula genera un menu
class Menu
{
    //Atributos
  var $conexion; //Conexion a la Base de Datos
  //Metodos
  function __construct( $datos_usuario, $tabla_permisos, $tipo_permiso, $cod_permiso, $cod_aplica, $conexion ) {
    $this -> conexion = $conexion;

    $pagina_menu = new Pagina("AVANSAT GL - ".NOM_TRANSPOR."", "#FFFFFF\" style=\"overflow: hidden;\"", 0, 0, 0, 0, 0, "../".DIR_APLICA_CENTRAL."/lib/menu/css/menu.css","", 0);

    echo '<div id="MainID" class="div_temporal" align="center" style="overflow-y:scroll; font-size: 11px;">';
      echo '<div>';
        echo '<div>';
          echo '<img style="height:60 !important" src="../satt_standa/estilos/'.ESTILO.'/imagenes/backg_menu_top.jpg">';
        echo '</div>';
        echo '<div>';
          echo '<span style="color: green; font-weight: bold; font-size: 13px;" class="color-menu-encabeza" >Bienvenido</span><br><b>'.substr(ucwords(strtolower($datos_usuario['nom_usuari'])),0,40).'...</b>';
        echo '</div>';
        echo '<div>';
          echo 'Perfil: <b>'.substr(ucwords(strtolower($datos_usuario['nom_perfil'])), 0, 25).'...</b>';
        echo '</div>';
        echo '<div>';
          echo '<a class="menuLink color-menu-encabeza" href="http://grupooet.com/" target="_blank">Visitar la Web</a> | <a class="menuLink color-menu-encabeza" href="session.php?op=2&usuario='.$datos_usuario['cod_usuari'].'&app='.$_REQUEST['app'].'" target="_parent">Cerrar Sesi&oacute;n</a>';
        echo '</div>';
      echo '</div><br>';

          
      //se traen los servicios de primer nivel sobre los que tiene permiso el usuario
      $query = "SELECT a.cod_servic
                FROM ".BD_STANDA.".tab_genera_servic a, ".BASE_DATOS.".$tabla_permisos b LEFT JOIN ".BD_STANDA.".tab_servic_servic c ON b.cod_servic = c.cod_serhij
                WHERE a.cod_servic = b.cod_servic
                AND a.cod_aplica = '" . $cod_aplica . "'
                AND c.cod_serhij IS NULL
                AND b.$tipo_permiso = '$cod_permiso'
                ORDER BY a.ind_ordenx, a.nom_servic ASC";
      $consulta = new Consulta($query, $this -> conexion);
      $servicios[1] = $consulta -> ret_matriz();

      //Si ya se ha desplegado una opcion del menu se traen de la bd todos los niveles desplegados
      if ( isset($_REQUEST["desplegados"] ) ) {
        global $desplegados;
        for($i = 1; $i <= sizeof($desplegados); $i++) {
          $query = "SELECT a.cod_servic
                      FROM ".BASE_DATOS.".$tabla_permisos a, ".BD_STANDA.".tab_servic_servic b, ".BD_STANDA.".tab_genera_servic c
                      WHERE a.cod_servic = b.cod_serhij
                      AND a.cod_servic = c.cod_servic
                      AND b.cod_serpad = '" . $desplegados[$i] . "'
                      AND a.$tipo_permiso = '$cod_permiso'
                      ORDER BY a.ind_ordenx, a.nom_servic ASC";
            $consulta = new Consulta($query, $this -> conexion);
            $servicios[$i+1] = $consulta -> ret_matriz();
        }
      }

      // Se para el la ultima seleccion del menu
      if($_REQUEST[menant])
      //-------------------------------------------------------------------------------------------------------------------------------------
      echo '<script language="javascript" src="../'.DIR_APLICA_CENTRAL.'/lib/menu/js/dinamic_menu.js"></script>';
      echo '<link rel="stylesheet" type="text/css" href="../'.DIR_APLICA_CENTRAL.'/lib/menu/css/dinamic_menu.css" />';
      echo '<link rel="stylesheet" type="text/css" href="../'.DIR_APLICA_CENTRAL.'/estilos/'.ESTILO.'/css/estilos.css" />';
      include( "../".DIR_APLICA_CENTRAL."/lib/menu/menu.php" );
     
      
    echo '</div>';

    $pagina_menu -> cerrar();
  }
  

  //Esta funcion publica los items del menu desplegados cada nivel.
  function publicar_menu($servicios, $act_nivel, $desplegados, $txt_desplegados)
  {

      //Se generan los links de cada item del menu en el nivel actual
      for($i = 0; $i < sizeof($servicios[$act_nivel]); $i++)
      {
          $servic = new Servic($servicios[$act_nivel][$i]["cod_servic"]);
          $servic -> listar($this -> conexion);
          $datos_servic = $servic -> retornar();

          //Si el servicio es simplemente el padre de un grupo no tendra un archivo para incluir
          if($datos_servic["rut_archiv"] == "")
          {
              //los txt_desplegados_representan el vector que almacena la opcion seleccionada en cada nivel
              $tmp_txt_desplegados = $txt_desplegados."&desplegados[$act_nivel]=$datos_servic[cod_servic]";
              $href = "index.php?window=menu$tmp_txt_desplegados&nom_basdat=$_REQUEST[nom_basdat]&menant=$datos_servic[cod_servic]&app=$_REQUEST[app]";
              $target = "menuFrame";
              
              if(($desplegados[$act_nivel] == $datos_servic["cod_servic"])&&(POS_MENU == "horizontal")):
                $ant_desplegados = $tmp_txt_desplegados;
              endif;
          }
          //Si no lo es se hace el link p�a que apunte al servicio en el frame Central
          else
          {
              $href = "index.php?window=central&cod_servic=$datos_servic[cod_servic]&nom_basdat=$_REQUEST[nom_basdat]&menant=$datos_servic[cod_servic]&app=$_REQUEST[app]";
              $target = "centralFrame";
          }

          echo "\n\t\t\t  <td class=\"menu_niv_$nivel_identacion\" width=\"100%\">$identacion<a href=\"$href\" id=\"$datos_servic[cod_servic]\" target=\"$target\"><img width=\"10\" height=\"10\" border=\"0\" src=\"../".DIR_APLICA_CENTRAL."/imagenes/bullet_$nivel_identacion.gif\">&nbsp;$datos_servic[nom_servic]</a></td>";

      }

  }//FIN FUNCION publicar_menu

}//FIN CLASE Menu 
?>