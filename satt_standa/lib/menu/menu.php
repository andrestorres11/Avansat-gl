<?php



if ( isset( $_GET["Action"] ) ) $_AJAX = $_GET;
if ( isset( $_POST["Action"]  ) ) $_AJAX = $_POST;


if ( $_AJAX["Action"] == "load" )  {
  
    include_once( "lib/menu/dinamic_menu.inc" );
    session_start();
    $menu = $_SESSION["SATMenu"];
    $tree = $_SESSION["tree"];
    $menu->Display( $tree );
}
else  
{
        $data = $_SESSION["datos_usuario"];

        //@Menu:      
        //@Matriz de servicios padres con padre "root".
        $fathers = "SELECT c.cod_servic AS node, ";
        $fathers .= "nom_servic AS nnode, ";
        $fathers .= "'root' AS father, ";
        $fathers .= "'root' AS nfather, ";
        $fathers .= "a.rut_archiv AS url "; 
        if ( $data["cod_perfil"] ){
            $fathers .= "FROM ".BASE_DATOS.".tab_perfil_servic c, ".BD_STANDA.".tab_genera_servic a LEFT JOIN ".BD_STANDA.".tab_servic_servic b ON a.cod_servic = b.cod_serhij ";
            $fathers .= "WHERE a.cod_servic = c.cod_servic AND c.cod_perfil = '".$data["cod_perfil"]."' AND cod_serhij IS NULL ORDER BY a.ind_ordenx, a.nom_servic ASC";
        }
        else    {
            $fathers .= "FROM ".BASE_DATOS.".tab_servic_usuari c, ".BD_STANDA.".tab_genera_servic a LEFT JOIN ".BD_STANDA.".tab_servic_servic b ON a.cod_servic = b.cod_serhij ";
            $fathers .= "WHERE a.cod_servic = c.cod_servic AND c.cod_usuari = '".$data["cod_usuari"]."' AND cod_serhij IS NULL ORDER BY a.ind_ordenx, a.nom_servic ASC";
        }
        
        $request1 = new Consulta( $fathers, $this->conexion );
        $fathers = $request1->ret_matriz( "a" );    
        
        //@Matriz de servicios hijos con sus respectivos padres.
        $sons = "SELECT a.cod_serhij as node, ";
        $sons .= "b.nom_servic as nnode, ";
        $sons .= "a.cod_serpad as father, ";
        $sons .= "c.nom_servic as nfather, ";
        $sons .= "b.rut_archiv AS url ";
        if ( $data["cod_perfil"] ){
        $sons .= "FROM ".BD_STANDA.".tab_servic_servic a, ".BD_STANDA.".tab_genera_servic b, ".BD_STANDA.".tab_genera_servic c, ".BASE_DATOS.".tab_perfil_servic d ";
        $sons .= "WHERE a.cod_serhij = b.cod_servic AND a.cod_serpad = c.cod_servic AND a.cod_serhij = d.cod_servic AND d.cod_perfil = '".$data["cod_perfil"]."' ORDER BY node ASC";
        }
        else    {
            $sons .= "FROM ".BD_STANDA.".tab_servic_servic a, ".BD_STANDA.".tab_genera_servic b, ".BD_STANDA.".tab_genera_servic c, ".BASE_DATOS.".tab_servic_usuari d ";
            $sons .= "WHERE a.cod_serhij = b.cod_servic AND a.cod_serpad = c.cod_servic AND a.cod_serhij = d.cod_servic AND d.cod_usuari = '".$data["cod_usuari"]."' ORDER BY node ASC";
        
        }

        $request2 = new Consulta( $sons, $this->conexion );
        $sons = $request2->ret_matriz( "a" );
        
        //@Construcci�n del �rbol ( nodos-padres ) al unir la matriz de padres con la de hijos.
        $tree = array_merge( $fathers, $sons );



       
      echo '
        <link type="text/css" rel="stylesheet" href="../'.DIR_APLICA_CENTRAL.'/lib/menu/css/bootstrap.min.css" />
        <link type="text/css" rel="stylesheet" href="../'.DIR_APLICA_CENTRAL.'/lib/menu/css/mmenu.css" />
        <link type="text/css" rel="stylesheet" href="../'.DIR_APLICA_CENTRAL.'/lib/menu/css/mmenu-theme-light.css" />
        
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
        <script type="text/javascript" src="../'.DIR_APLICA_CENTRAL.'/lib/menu/js/jquery.mmenu.js"></script>
     ';

     echo '<script language="javascript" src="../'.DIR_APLICA_CENTRAL.'/lib/menu/js/dinamic_menu2.js"></script>';


    ?>
    <script type="text/javascript">
        $(function() {
            var menuHeight = Number($(document).innerHeight()) -  Number($('#MainID').find('div').first().height()) - Number($('.mm-search').height()) - 86;
            $('div#menu').mmenu({
                searchfield: true ,
                slidingSubmenus: false 
            }).css( {
                height:  menuHeight + 'px',
                'overflow-y' : 'scroll'
            } ).fadeIn("slow");

            $(".mm-subopen").click(function() {
                var a   = $(this).next();
                var cod = Number(a.attr('id').replace(/[^\d]/g, '').replace(/^\s+|\s+$/g,""));
                DinamicClose( a, cod );
            });
        });

        $( window ).resize(function() {
            var menuHeight = Number($(document).innerHeight()) -  Number($('#MainID').find('div').first().height()) - Number($('.mm-search').height()) - 86;
            $('div#menu').css( {
                height:  menuHeight + 'px',
                'overflow-y' : 'scroll'
            } ).fadeIn("slow");
        });


        
    </script>
    
  <div id="menu" style='display: none;'>
          
    <?php

         

        include( "../".DIR_APLICA_CENTRAL."/lib/menu/dinamic_menu.inc" );
        //------------------------------------------------------------------------------------------------
        $menu = new DinamicMenu;
        
        if ( $_SESSION["Browser"]=="Explorer" ) {
            $menu->SetMenuHeight( $_SESSION["ScreenHeight"]-217 );
            $menu->SetWorkArea( "SATWorkAreaDIV", $_SESSION["ScreenWidth"]-47, $_SESSION["ScreenWidth"]-253 );
        }
        else    {
            $menu->SetMenuHeight( $_SESSION["ScreenHeight"]-237 );
            $menu->SetWorkArea( "SATWorkAreaDIV", $_SESSION["ScreenWidth"]-44, $_SESSION["ScreenWidth"]-250 );
        }
        
        $_SESSION["SATMenu"] = $menu;
        $_SESSION["tree"] = $tree;
        $_SESSION["APP_MENU"] = $menu;
        
        $menu->Display( $tree,  $this->conexion ); 
        
}//FIN ELSE GENERAL 

?>
        
  </div>

