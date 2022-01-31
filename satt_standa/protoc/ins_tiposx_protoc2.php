<?php
session_start();

class FleConcil
{
    var $conexion;

    function __construct($conexion)
    {

        $this->conexion = $conexion;
        switch($_POST[opcion])
        {
            case "1":
            {
              $this->Listar();
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
            {
                $this->Listar();
            }
            break;
        }
    }

    function Listar()
    {
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/proto.js\"></script>\n";

        $sql = "SELECT cod_consec ,nom_tiposx 
                FROM ".BASE_DATOS.".tab_tiposx_protoc ";
        $consulta = new Consulta($sql, $this -> conexion);
  	    $tipos = $consulta -> ret_matriz();
        
        $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "INSERTAR/ACTUALIZAR TIPOS DE PROTOCOLOS", "formorden\" id=\"formularioID");

        $formulario -> nueva_tabla();
        $formulario -> texto ("Nombre","text","nom_tiposx\"  id=\"nom_tiposxID",1,50,50,"", ""   );
        $formulario -> nueva_tabla();
        $formulario -> botoni("Guardar","validarTipo()",0);
        $formulario -> oculto("url_archiv\" id=\"url_archivID\"","ins_tiposx_protoc.php",0);
		    $formulario -> oculto("dir_aplica\" id=\"dir_aplicaID\"",DIR_APLICA_CENTRAL,0);
        $formulario -> oculto("window","central",0);
        $formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],0);
        $formulario -> oculto("opcion\" id=\"opcionID",2,0);
        $i=1;
        $formulario -> nueva_tabla();
        foreach($tipos as $tipo){
          $formulario -> oculto("cod_consec$i\" id=\"cod_consecID$i",$tipo[0],0);
          $formulario -> texto ("Nombre del Protocolo","text","nom_tiposx$i\" id=\"nom_tiposxID$i",0,40,50,"", $tipo[1]   );
          echo "<td>";
          echo '<img  width="16px" height=16px" border="0" alt="" onclick="updProto('.$i.')" title="Actualizar" src="../satt_standa/imagenes/ok.gif" >';
          echo "</td>";
          echo "<td>";
          echo '<img  width="16px" height=16px" border="0" alt="" onclick="delProto('.$i.')" title="Eliminar" src="../satt_standa/imagenes/error.gif" >';
          echo "</td></tr>";
          $i++;
        }
        $formulario -> cerrar();
        
        echo '<tr><td><div id="AplicationEndDIV"></div>
              <div id="RyuCalendarDIV" style="position: absolute; background: white; left: 0px; top: 0px; z-index: 3; display: none; border: 1px solid black; "></div>
              <div id="popupDIV" style="position: absolute; left: 0px; top: 0px; width: 300px; height: 300px; z-index: 3; visibility: hidden; overflow: auto; border: 5px solid #333333; background: white; ">

    		  <div id="filtros" >
    		  </div>

    		  <div id="result" >


    		  </div>
     		  </div><div id="alg"> <table></table></div></td></tr>';
			echo"";
    }

  
  
  function updProto()
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
    $this -> conexion = new Conexion( $_SESSION['HOST'], $_SESSION[USUARIO], $_SESSION[CLAVE], $BASE  );
		$query =  "UPDATE ".BASE_DATOS.".tab_tiposx_protoc
               SET nom_tiposx ='".$_POST['nom_tiposx']."',
                   fec_modifi = NOW(),
                   usr_modifi = '$usuario'
               WHERE cod_consec = '".$_POST['cod_consec']."' ";
    $insercion = new Consulta($query, $this -> conexion,"BR");
    if($insercion = new Consulta("COMMIT", $this -> conexion))
      $tipo=1;
    else
      $tipo='';  
    $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "ANULAR CONCILIACION", "formulario");
    $formulario -> oculto("cod_tipo\" id=\"cod_tipoID",$tipo,0);
    $formulario -> oculto("opcion\" id=\"opcionID",2,0);
    $formulario -> cerrar();
	}
  
  
  
  
  function delProto()
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
    $this -> conexion = new Conexion( $_SESSION['HOST'], $_SESSION[USUARIO], $_SESSION[CLAVE], $BASE  );
		$query =  "DELETE FROM ".BASE_DATOS.".tab_tiposx_protoc
               WHERE cod_consec = '".$_POST['cod_consec']."' ";
    $insercion = new Consulta($query, $this -> conexion,"BR");
    if($insercion = new Consulta("COMMIT", $this -> conexion))
      $tipo=1;
    else
      $tipo='';  
    $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "ANULAR CONCILIACION", "formulario");
    $formulario -> oculto("tipo\" id=\"tipoID\"",$tipo,0);
    $formulario -> cerrar();
	}
  



    function insert()
	  {
	    $usuario = $_SESSION["datos_usuario"]["cod_usuari"];
      $sql = "SELECT MAX(cod_consec)
              FROM ".BASE_DATOS.".tab_tiposx_protoc ";
      $consulta = new Consulta($sql, $this -> conexion);
	    $max = $consulta -> ret_matriz();
      $max = $max[0][0] +1;
		  $query = "INSERT INTO ".BASE_DATOS.".tab_tiposx_protoc
										( 
											cod_consec, nom_tiposx,
											usr_creaci, fec_creaci 
										)
										VALUES
										( 
											'".$max."', '".$_REQUEST["nom_tiposx"]."',
											'$usuario',NOW()
										)";
      $insercion = new Consulta($query, $this -> conexion,"BR");
      if( $insercion = new Consulta("COMMIT", $this -> conexion))
        {
            $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">Insertar Otro Tipo de Protocolo</a></b>";

            if($msm)
                $mensaje = $msm;

            $mensaje .=  "Se Inserto con Exito".$link_a;
            $mens = new mensajes();
            $mens -> correcto("INSERTAR TIPO DE PROTOCOLO",$mensaje);
        }
	  }



    function getFlete()
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
    $this -> conexion = new Conexion( $_SESSION['HOST'], $_SESSION[USUARIO], $_SESSION[CLAVE], $BASE  );
    $query= "SELECT a.cod_tiposx 
             FROM ".BASE_DATOS.".tab_concil_tiposx a
             WHERE a.cod_tiposx ='".$_POST['cod_tiposx']."'";
    $consulta = new Consulta($query, $this -> conexion);
  	$flete = $consulta -> ret_matriz();
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mmpp.js\"></script>\n";
	  
		$formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "ANULAR CONCILIACION", "formulario");
    $formulario -> nueva_tabla(); 
    $formulario -> oculto("cod_fle\" id=\"cod_fleID",$flete[0][0],0);
    $formulario -> oculto("url_archiv\" id=\"url_archivID\"","anu_concil_mmpp.php",0);
    $formulario -> oculto("dir_aplica\" id=\"dir_aplicaID\"",DIR_APLICA_CENTRAL,0);
    $formulario -> cerrar();
	}

}
//$service = new FleConcil($this->conexion);
$service = new FleConcil($_SESSION['conexion']);
?> 
