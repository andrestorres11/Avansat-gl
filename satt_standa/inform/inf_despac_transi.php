<?php
session_start();
//date_default_timezone_set('America/Bogota');
  class Proc_despac
  {
    var $conexion, $cod_aplica, $usuario;
  
    function __construct($co, $us, $ca)
    {
      $this -> conexion = $co;
      $this -> usuario = $us;
      $this -> cod_aplica = $ca;
      $this -> principal();
    }

    function principal()
    {
      if(!isset($GLOBALS["opcion"]))
      {
        $this -> Listar();
      }
      else
      {
        switch($GLOBALS["opcion"])
        {
          case "1":
            $this -> Datos();
          break;
          case "informe";
            $this -> Informe();
          break;
          case "Turnos";
            $this -> Turnos();
          break;
          case "getPC";
            $this -> getPC();
          break;
          default:
            $this -> Listar();
          break;
        }
      }
    }

    function Listar()
    {
      session_start();

      $_SESSION["inf_conexio"] =  $this -> conexion ;
      $_SESSION["inf_usuario"] = $_SESSION["datos_usuario"];
      $_SESSION["inf_aplica"] = $this -> cod_aplica;
      $BD = $_SESSION["BASE_DATOS"];
      $US = $_SESSION["USUARIO"];
      $CL = $_SESSION["CLAVE"];

      echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
      echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
      echo " <script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/new_ajax.js'></script> ";
      echo " <script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/comet.js'></script> ";


      echo " <TR><TD>";

      echo "<input type='hidden' name='petro' id='petroID' value='".base64_encode( $CL )."'>";
      echo "<input type='hidden' name='oplix' id='oplixID' value='".base64_encode( $BD )."'>";
      echo "<input type='hidden' name='sachu' id='sachuID' value='".base64_encode( $US )."'>";
      echo "<input type='hidden' name='tipser' id='tipserID' value=''>";
      echo "<input type='hidden' name='us' id='usID' value=''>";
      echo "<input type='hidden' name='central' id='centralID' value='".base64_encode( DIR_APLICA_CENTRAL )."'>";
      echo "<input type='hidden' name='central' id='central2ID' value='". DIR_APLICA_CENTRAL ."'>";
      echo "<input type='hidden' name='estilo' id='estiloID' value='".base64_encode( ESTILO )."'>";
      echo "<input type='hidden' name='cod_llegad' id='cod_llegadID' value='".CONS_CODIGO_PCLLEG."'>";

      echo "<TABLE  BORDER='0' CELLPADDING='0' CELLSPACING='0' WIDTH='100%'>";
      echo " <TR><TD width='10px' class='barra' onclick='Hide()'>";
      echo "&nbsp;</TD><TD><div id='informeID'>";
      echo "Se está cargando el informe de despachos en transito...</div>";
      echo " </TD></TR>";	
      echo "</TABLE>";

      echo "<script type='text/javascript'>";
      if($_SESSION["datos_usuario"]["cod_inicio"]=='' || $_SESSION["datos_usuario"]["cod_inicio"]==1)
      {
        echo " LoadComet(); ";
      }
      else
      {
        echo " LoadTurnos(); ";
      }
      echo "</script>";
      echo " </TD></TR>";	
    }

    function Turnos()
    {
      session_start();
      include( "../lib/general/conexion_lib.inc" );
      include ("../lib/bd/seguridad/aplica_filtro_usuari_lib.inc");
      include ("../lib/bd/seguridad/aplica_filtro_perfil_lib.inc");
      include( "../lib/general/form_lib.inc" );
      include( "../lib/GeneralFunctions.inc" );
      include( "../lib/general/paginador_lib.inc" );
      include( "../despac/Despachos.inc" );
      include( "../lib/general/tabla_lib.inc" );
      include ("../lib/mensajes_lib.inc");
      define("COD_PERFIL_SUPERUSR", "73");// constante para perfil administrador del sistema
      define("COD_PERFIL_ADMINIST", "1");// constante para perfil administrador de empresa
      define("COD_PERFIL_SUPEFARO", "8");// supervisor faro
      define("COD_PERFIL_SUPEGENE", "84");// supervisor General
      define("COD_PERFIL_AUDITORX", "97");// supervisor General
      echo " <script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/new_ajax.js'></script> ";
      echo " <script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/comet.js'></script> ";

      define("COD_FILTRO_EMPTRA", "1");// constante para filtro de Transportadora
      define("COD_FILTRO_AGENCI", "2");// constante para filtro de Agencias de la transportadora
      define("COD_FILTRO_CLIENT", "3");// constante para filtro de Generadores de Carga
      define("COD_FILTRO_CONDUC", "4");//constante para filtro de conductores
      define("COD_FILTRO_PROPIE", "5");//constante para filtro de propietarios
      define("COD_FILTRO_POSEED", "6");//constante para filtro de poseedores
      define("COD_FILTRO_ASEGUR", "8");//constante para filtro de asegurador
      define("COD_ESTADO_PENDIE", "0");//constante para el estado Pendiente
      define("COD_ESTADO_ACTIVO", "1");//constante para el estado Activo
      define("COD_ESTADO_INACTI", "2");//constante para el estado Inactivo
      define("CONS_CODIGO_PCLLEG", "9999");// constante para el codigo del puesto de control de llegada
      define("COD_APLICACION", "1");// constante para el codigo de la aplicacion
      define("CONS_PERFIL", "'79'");// constante para los perfiles que no deben salir en listado de turnos

      $datos_usuario = $_SESSION["datos_usuario"];

      $US = base64_decode($_POST["sachu"]);
      $CL = base64_decode($_POST["petro"]);
      $BD = base64_decode($_POST["oplix"]);
      $CN = base64_decode($_POST["central"]);
      $ES = base64_decode($_POST["estilo"]);
      $tip = $_POST["tipser"];	
      $USE = $_POST["us"];
      $USE = utf8_decode($USE);

      define( DIR_APLICA_CENTRAL, $CN );
      define( BASE_DATOS, $BD );
      define( ESTILO, $ES );

      $this -> conexion = new Conexion( "bd10.intrared.net:3306", $US, $CL , $BD );

      $this -> aplica = 1;

      $objciud = new Despachos( $GLOBALS["cod_servic"], $GLOBALS["opcion"], $this -> aplica, $this -> conexion );
      $fechoract = date("d-M-Y h:i:s A");

      /*$query = "SELECT a.ind_remdes
      FROM ".BASE_DATOS.".tab_config_parame a
      WHERE a.ind_remdes = '1'";

      $consulta = new Consulta($query, $this -> conexion);
      $manredes = $consulta -> ret_matriz();*/

      $query = "SELECT a.ind_desurb
                  FROM ".BASE_DATOS.".tab_config_parame a
                 WHERE a.ind_desurb = '1' ";
      $consulta = new Consulta($query, $this -> conexion);
      $desurb = $consulta -> ret_matriz();

      $inicio[0][0] = 0;
      $inicio[0][1] = '-';
      $query = "SELECT cod_tipser, nom_tipser
                  FROM ".BASE_DATOS.".tab_genera_tipser";
      $consulta = new Consulta($query, $this -> conexion);
      $tipos = $consulta -> ret_matriz();

      $inicio[0][0] = 0;
      $inicio[0][1] = '-';
      $tipos= array_merge($inicio,$tipos);
      if($tip!='')
      { 
        $query = "SELECT cod_tipser, nom_tipser
                    FROM ".BASE_DATOS.".tab_genera_tipser
                    WHERE cod_tipser = '$tip'";
        $consulta = new Consulta($query, $this -> conexion);
        $tipo = $consulta -> ret_matriz();
        $tipos= array_merge($tipo,$tipos);
      }

      $query = "SELECT a.ind_desurb
                  FROM ".BASE_DATOS.".tab_config_parame a
                 WHERE a.ind_desurb = '1' ";
      $consulta = new Consulta($query, $this -> conexion);
      $desurb = $consulta -> ret_matriz();
    
      $query = "SELECT a.ind_restra
                  FROM ".BASE_DATOS.".tab_config_parame a
                 WHERE a.ind_restra = '1'";
      $consulta = new Consulta($query, $this -> conexion);
      $resptran = $consulta -> ret_matriz();

      if($datos_usuario['cod_perfil']!=COD_PERFIL_ADMINIST && $datos_usuario['cod_perfil']!=COD_PERFIL_SUPEFARO && $datos_usuario['cod_perfil']!=COD_PERFIL_SUPERUSR && $datos_usuario['cod_perfil']!=COD_PERFIL_SUPEGENE && $datos_usuario['cod_perfil']!=COD_PERFIL_AUDITORX)
      {
        $query = "SELECT b.cod_tercer
                    FROM ".BASE_DATOS.".tab_monito_encabe a,
                         ".BASE_DATOS.".tab_monito_detall b
                   WHERE a.ind_estado = '1' AND
                         b.ind_estado = '1' AND
                         a.cod_consec = b.cod_consec AND
                         a.fec_inicia <= NOW() AND
                         a.fec_finalx >= NOW() AND
                         a.cod_usuari = '".$datos_usuario["cod_usuari"]."'";
        $consulta = new Consulta($query, $this -> conexion);
        $horari = $consulta -> ret_matriz("i");

        if(!$horari)
        {
          $mensaje .=  "El Usuario no Tiene Horario Asignado".$link_a;
          $mens = new mensajes();
          $mens -> error("",$mensaje);
          die();
        }   
      }
      else
      { 
        $inicio1[0][0] = 0;
        $inicio1[0][1] = '-';
        $inicio1[1][0] = 'P';
        $inicio1[1][1] = 'Sin Asignar';

        $query = "SELECT a.cod_usuari, a.cod_usuari
                    FROM ".BASE_DATOS.".tab_monito_encabe a,
                         ".BASE_DATOS.".tab_monito_detall b,
                         ".BASE_DATOS.".tab_genera_usuari c
                   WHERE a.ind_estado = '1' AND
                         a.cod_consec = b.cod_consec AND
                         a.fec_inicia <= NOW() AND
                         a.fec_finalx >= NOW() AND
                         a.cod_usuari = c.cod_usuari AND
                         c.cod_perfil NOT IN (".CONS_PERFIL.")
                GROUP BY 1";
        $consulta = new Consulta($query, $this -> conexion);
        $user = $consulta -> ret_matriz("i");
        $user= array_merge($inicio1,$user);

        if($USE!='' && $USE!='0' && $USE!='P')
        {
          $query = "SELECT b.cod_tercer
                      FROM ".BASE_DATOS.".tab_monito_encabe a,
                           ".BASE_DATOS.".tab_monito_detall b
                     WHERE a.ind_estado = '1' AND
                           b.ind_estado = '1' AND
                           a.cod_consec = b.cod_consec AND
                           a.fec_inicia <= NOW() AND
                           a.fec_finalx >= NOW() AND
                           a.cod_usuari = '".$USE."'";
          $consulta = new Consulta($query, $this -> conexion);
          $horari = $consulta -> ret_matriz("i");

          $x[0][0]=$USE;
          $x[0][1]=$USE;
          $user= array_merge($x,$user);
        }
        if($USE=='P')
        {
          $query = "SELECT b.cod_tercer
                      FROM ".BASE_DATOS.".tab_monito_encabe a,
                           ".BASE_DATOS.".tab_monito_detall b
                     WHERE a.ind_estado = '1' AND
                           b.ind_estado = '1' AND
                           a.cod_consec = b.cod_consec AND
                           a.fec_inicia <= NOW() AND
                           a.fec_finalx >= NOW()";
          $consulta = new Consulta($query, $this -> conexion);
          $horari = $consulta -> ret_matriz("i");
          
          $x[0][0]='P';
          $x[0][1]= 'Sin Asignar';
          $user= array_merge($x,$user);
        }  
      }

      $ter = array();
      for($i=0;sizeof($horari)>=$i+1;$i++)
      {
        $ter[$i]="'".$horari[$i][0]."'";
      }



      $ter = implode(",",$ter);
      $query = "SELECT j.cod_tercer,j.abr_tercer,j.cod_ciudad
                  FROM ".BASE_DATOS.".tab_despac_despac a,
                       ".BASE_DATOS.".tab_despac_vehige d,
                       ".BASE_DATOS.".tab_tercer_tercer j,
                       ".BASE_DATOS.".tab_transp_tipser k  
                 WHERE a.num_despac = d.num_despac AND
                       d.cod_transp = j.cod_tercer AND
                       a.fec_salida Is Not Null AND
                       k.cod_transp = d.cod_transp AND
                       a.fec_salida <= NOW() AND
                       a.fec_llegad Is Null AND
                       a.ind_anulad = 'R' AND
                       a.ind_planru = 'S' ";
                      //d.cod_transp NOT IN ( SELECT cod_tercer FROM ".BASE_DATOS.".tab_config_alianz ) AND		

      if($tip!='' && $tip!='0')
      {
        $query .= " AND k.cod_tipser = '$tip' AND
                   k.fec_creaci = (SELECT MAX(l.fec_creaci) 
                                     FROM ".BASE_DATOS.".tab_transp_tipser l
                                    WHERE l.cod_transp = d.cod_transp )";
      }
      if( $USE=='P')
        $query .= " AND d.cod_transp NOT IN($ter)"; 				   
      elseif( $datos_usuario["cod_perfil"] != COD_PERFIL_ADMINIST && $datos_usuario['cod_perfil']!=COD_PERFIL_SUPEGENE  && $datos_usuario['cod_perfil']!=COD_PERFIL_SUPEFARO && $datos_usuario['cod_perfil']!=COD_PERFIL_SUPERUSR && $datos_usuario['cod_perfil']!=COD_PERFIL_AUDITORX || $USE!='' && $USE!='0')
        $query .= " AND d.cod_transp IN($ter)";

      if( $datos_usuario["cod_perfil"] == "" )
      {
        $this -> cod_aplica = '1';

        //PARA EL FILTRO DE CONDUCTOR
        $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_usuari"]);
        if($filtro -> listar($this -> conexion))
        {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
        }
  
        //PARA EL FILTRO DE EMPRESA
        $filtro = new Aplica_Filtro_Usuari( $this -> cod_aplica ,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
        if($filtro -> listar($this -> conexion))
        {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
        }
  
        //PARA EL FILTRO DE ASEGURADORA
        $filtro = new Aplica_Filtro_Usuari( $this -> cod_aplica ,COD_FILTRO_ASEGUR,$datos_usuario["cod_usuari"]);
        if($filtro -> listar($this -> conexion))
        {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND a.cod_asegur = '$datos_filtro[clv_filtro]' ";
        }
  
        //PARA EL FILTRO DEL CLIENTE
        $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_usuari"]);
        if($filtro -> listar($this -> conexion))
        {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
        }
  
        //PARA EL FILTRO DE LA AGENCIA
        $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_usuari"]);
        if($filtro -> listar($this -> conexion))
        {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
        }
      }
      else
      {
        $this -> cod_aplica = '1';
        //PARA EL FILTRO DE CONDUCTOR
        $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_perfil"]);
        if($filtro -> listar($this -> conexion))
        {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
        }

        //PARA EL FILTRO DE EMPRESA
        $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
        if($filtro -> listar($this -> conexion))
        {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
        }

        //PARA EL FILTRO DE ASEGURADORA
        $filtro = new Aplica_Filtro_Perfil( $this -> cod_aplica ,COD_FILTRO_ASEGUR,$datos_usuario["cod_perfil"]);
        if($filtro -> listar($this -> conexion))
        {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND a.cod_asegur = '$datos_filtro[clv_filtro]' ";
        }

        //PARA EL FILTRO DEL CLIENTE
        $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_perfil"]);
        if($filtro -> listar($this -> conexion))
        {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
        }

        //PARA EL FILTRO DE LA AGENCIA
        $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_perfil"]);
        if($filtro -> listar($this -> conexion))
        {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
        }
      }
      $query = $query." GROUP BY 1 ORDER BY 2";
      $consulta = new Consulta($query, $this -> conexion);
      $transpor = $consulta -> ret_matriz();
      $query_exp = base64_encode($query);
      $exp .= "url=".NOM_URL_APLICA."&db=".BASE_DATOS."&nomarchive=Despachos_en_Transito&query_exp=".$query_exp."";
      $query = "SELECT a.cod_alarma,a.nom_alarma,a.cod_colorx,a.cant_tiempo
                  FROM ".BASE_DATOS.".tab_genera_alarma a
              ORDER BY 4 ";
      $consulta = new Consulta($query, $this -> conexion);
      $alarmas = $consulta -> ret_matriz();
      $query ="SELECT cant_tiempo,cod_colorx
                 FROM ".BASE_DATOS.".tab_genera_alarma
                ORDER BY 1";
      $consulta = new Consulta ($query, $this -> conexion);
      $colores  = $consulta -> ret_matriz();
      $totaldes = $totporll = $totsires = $totfupla = 0;

      /*$query = "SELECT a.ind_defini
                    FROM ".BASE_DATOS.".tab_despac_despac a, 
                         ".BASE_DATOS.".tab_despac_seguim b, 
                         ".BASE_DATOS.".tab_despac_vehige d
                   WHERE a.num_despac = d.num_despac AND 
                        a.num_despac = b.num_despac AND 
                        a.fec_salida Is Not Null AND 
                        a.fec_salida <= NOW() AND 
                        a.fec_llegad Is Null AND 
                        a.ind_anulad = 'R' AND 
                        a.ind_planru = 'S' AND 
                        a.ind_defini= '1' ";
      if( $datos_usuario["cod_perfil"] == "" )
      {
        $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_usuari"]);
        if($filtro -> listar($this -> conexion))
        {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
        }  
      }
      $consulta = new Consulta($query, $this -> conexion); 
      $totdefin = $consulta -> ret_matriz();*/
	  
      for($i = 0; $i < sizeof($transpor); $i++)
      {
        //Se obtiene el tipo de servicio
        $query = "SELECT b.nom_tipser ,tie_conurb, tie_contro
                    FROM ".BASE_DATOS.".tab_transp_tipser a,
                         ".BASE_DATOS.".tab_genera_tipser b,
                         (SELECT MAX(num_consec) AS num_consec,cod_transp 
                            FROM ".BASE_DATOS.".tab_transp_tipser
                           GROUP BY cod_transp) AS c
                   WHERE a.cod_transp='".$transpor[$i][0]."' AND 
                         a.cod_tipser = b.cod_tipser AND
                         a.cod_transp = c.cod_transp AND
                         a.num_consec = c.num_consec ";
        $consulta = new Consulta( $query, $this -> conexion );
        $tipser = $consulta -> ret_arreglo();
        $transpor[$i][20] = $tipser[0];

        $transpor[$i][21] = $transpor[$i][3] = $transpor[$i][4] = $transpor[$i][5] = $transpor[$i][22] = 0;

        $query = "SELECT a.num_despac, a.fec_ultnov, a.fec_manala,
                         a.ind_defini, a.cod_conult,DATE_FORMAT( a.fec_ultnov, '%Y-%m-%d %H:%i' )
                    FROM ".BASE_DATOS.".tab_despac_despac a, 
                         ".BASE_DATOS.".tab_despac_vehige d 
                   WHERE a.num_despac = d.num_despac AND 
                         a.fec_salida Is Not Null AND 
                         a.fec_salida <= NOW() AND 
                         a.fec_llegad Is Null AND 
                         a.ind_anulad = 'R' AND 
                         a.ind_planru = 'S' AND
                         d.cod_transp = '".$transpor[$i][0]."'";
                         
        if( $datos_usuario["cod_perfil"] == "" )
        {
          $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_usuari"]);
          if($filtro -> listar($this -> conexion))
          {
            $datos_filtro = $filtro -> retornar();
            $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
          }  
        }
        else
        {
          //PARA EL FILTRO DE ASEGURADORA
          $filtro = new Aplica_Filtro_Perfil( $this -> cod_aplica ,COD_FILTRO_ASEGUR,$datos_usuario["cod_perfil"]);
          if($filtro -> listar($this -> conexion))
          {
            $datos_filtro = $filtro -> retornar();
            $query = $query . " AND a.cod_asegur = '$datos_filtro[clv_filtro]' ";
          }
        }
        $query.="		  GROUP BY 1 ORDER BY 1";
        $consulta = new Consulta($query, $this -> conexion); 
        $despacho = $consulta -> ret_matriz();
     
        for($j = 0; $j < sizeof($despacho); $j++)
        {
        
        $query	=	"(SELECT e.ind_fuepla, a.cod_conult,a.cod_ultnov,a.fec_ultnov,a.usr_ultnov,
                    b.obs_contro,c.nom_contro,d.nom_noveda,	b.fec_contro,
                    b.cod_contro,e.nom_noveda,b.fec_creaci
              FROM	".BASE_DATOS.".tab_despac_despac	a	LEFT	JOIN
                    ".BASE_DATOS.".tab_despac_contro	b	ON
                    a.num_despac	=	b.num_despac	AND
                    a.cod_consec	=	b.cod_consec	LEFT	JOIN
                    ".BASE_DATOS.".tab_genera_noveda	e	ON
                    b.cod_noveda	=	e.cod_noveda,
                    ".BASE_DATOS.".tab_genera_contro	c,
                    ".BASE_DATOS.".tab_genera_noveda	d
             WHERE	a.cod_conult	=	c.cod_contro	AND
                    a.cod_ultnov	=	d.cod_noveda	AND
                    a.num_despac	=	'".$despacho[$j][0]."')
             UNION
           (SELECT	e.ind_fuepla, a.cod_conult,a.cod_ultnov,a.fec_ultnov,a.usr_ultnov,
                    b.des_noveda,c.nom_contro,d.nom_noveda,b.fec_noveda,
                    b.cod_contro,e.nom_noveda,	b.fec_creaci
              FROM	".BASE_DATOS.".tab_despac_despac	a	LEFT	JOIN
                    ".BASE_DATOS.".tab_despac_noveda	b	ON
                    a.num_despac	=	b.num_despac	
                    LEFT	JOIN ".BASE_DATOS.".tab_genera_noveda	e	ON
                    b.cod_noveda	=	e.cod_noveda,
                    ".BASE_DATOS.".tab_genera_contro	c,
                    ".BASE_DATOS.".tab_genera_noveda	d
             WHERE	a.cod_conult	=	c.cod_contro	AND
                    a.cod_ultnov	=	d.cod_noveda	AND
                    a.num_despac	=	'".$despacho[$j][0]."')
          ORDER	BY	12	DESC	LIMIT	1";
        $consulta = new Consulta($query, $this -> conexion); 
        $fue_pla = $consulta -> ret_matriz();
        $transpor[$i][22] = $fue_pla[0][0];
          
          //vlix85
          //----------------------------------------------------------------
          if(in_array($datos_usuario['cod_perfil'], array(COD_PERFIL_ADMINIST,COD_PERFIL_SUPEFARO,COD_PERFIL_SUPERUSR,COD_PERFIL_SUPEGENE))){
          
            if( $this->GET_EAL($despacho[$j][0]) && strpos($tipser[0], 'EAL')!== FALSE ){
            //if( $datos_usuario['cod_usuari'] == 'desarrollo' )
              //echo "<br>".$transpor[$i][1].' '.$despacho[$j][0];
              $vlix85[ $transpor[$i][0] ][$j] = 1; // Conteo de EAL
            }else{
              $vlix85[ $transpor[$i][0] ][$j] = 0;
            }
          }
          //----------------------------------------------------------------
          
          //hugo
          //if($transpor[$i][22] != 1)
          //{
            $transpor[$i][3]++;
          //}
          
          
          $totaldes++;

          $query = "SELECT a.cod_rutasx 
                      FROM ".BASE_DATOS.".tab_despac_seguim a 
                     WHERE a.num_despac = ".$despacho[$j][0]." 
                     GROUP BY 1 ";
          $consulta = new Consulta($query, $this -> conexion);
          $totrutas = $consulta -> ret_matriz();

          if( sizeof($totrutas) < 2 ) 
            $camporder = "fec_planea";
          else 
            $camporder = "fec_alarma";
          $query = "SELECT a.cod_contro 
                      FROM ".BASE_DATOS.".tab_despac_seguim a, 
                           ".BASE_DATOS.".tab_despac_vehige c,
                           (SELECT MAX(b.".$camporder.") as fec 
                              FROM ".BASE_DATOS.".tab_despac_seguim b 
                             WHERE b.num_despac = ".$despacho[$j][0]." ) as  d 
                     WHERE a.num_despac = c.num_despac AND 
                           c.num_despac = ".$despacho[$j][0]." AND 
                           a.".$camporder." = d.fec ";
          $consulta = new Consulta($query, $this -> conexion);
          $ultimopc = $consulta -> ret_matriz();

          $query = "SELECT cod_contro, fec_noveda
                      FROM ".BASE_DATOS.".tab_despac_noveda 
                     WHERE num_despac = ".$despacho[$j][0]." AND 
                           fec_noveda = '".$despacho[$j][5].":00"."'";
          $consulta = new Consulta($query, $this -> conexion);
          $ultimnov = $consulta -> ret_matriz();
          if($ultimnov)
          {
            $query = "SELECT a.ind_urbano 
                        FROM ".BASE_DATOS.".tab_genera_contro a 
                        WHERE a.cod_contro = ".$ultimnov[0][0]." AND 
                              a.ind_urbano = '1' ";

            $consulta = new Consulta($query, $this -> conexion); 
            $pcontrurb = $consulta -> ret_matriz();
            /*$query = "SELECT b.fec_alarma 
                          FROM ".BASE_DATOS.".tab_despac_despac a, 
                               ".BASE_DATOS.".tab_despac_seguim b, 
                               ".BASE_DATOS.".tab_despac_vehige c
                         WHERE a.num_despac = c.num_despac AND
                               c.num_despac = b.num_despac AND
                               a.num_despac = ".$despacho[$j][0]." AND
                               b.fec_planea > '".$ultimnov[0][2]."'
                      ORDER BY 1 ";
            $consulta = new Consulta($query, $this -> conexion);
            $pfechala = $consulta -> ret_matriz();*/
          }
          else
            $pcontrurb = NULL;
          if( $desurb && $pcontrurb)
            $pcomparar = $ultimnov[0][0];
          else
            $pcomparar = $ultimopc[0][0];
          if( $despacho[$j][3]!='1' && ($pcomparar == $ultimnov[0][0] || $ultimnov[0][0] == CONS_CODIGO_PCLLEG) )
          {
            $transpor[$i][5]++;
            $totporll++;
          }
          else
          {
            /*if(!$ultimnov)
            {
              $query = "SELECT MIN(a.fec_alarma)
                          FROM ".BASE_DATOS.".tab_despac_seguim a
                         WHERE a.num_despac = ".$despacho[$j][0]."";
              $consulta = new Consulta($query, $this -> conexion);
              $fecalarm = $consulta -> ret_matriz();
              $tiempo_proxnov = $fecalarm[0][0];
            }
            else
              $tiempo_proxnov = $pfechala[0][0];

              //Calculo del tiempo retrasado
              $query = "SELECT TIME_TO_SEC( TIMEDIFF(NOW(), '".$tiempo_proxnov."')) / 60";
              $tiempo = new Consulta($query, $this -> conexion);
              $tiemp_demora = $tiempo -> ret_arreglo();*/
     
            if($despacho[$j][3]!='1')
            {
              $tiemp_alarma = NULL;
              //inicia nuevo calculo de alarmas

              $query = "SELECT cod_tipdes ".
                         "FROM ".BASE_DATOS.".tab_despac_despac ".
                        "WHERE num_despac='".$despacho[$j][0]."'";
              $consulta = new Consulta( $query, $this -> conexion );
              $cod_tipdes = $consulta -> ret_matriz();
              if($tipser)
              {
                if($tipser[0]=='EAL')
                {
                  $tiem ='P';
                }
                else
                {
                  if($cod_tipdes[0][0]=='1')
                  {
                    $tiem = $tipser[1]; 
                  }
                  else
                  {
                    $tiem = $tipser[2];
                  }
                }
              }
              else
              {
                if($cod_tipdes[0][0]=='1')
                  $tiem =25;
                else
                  $tiem=80;  
              }

              $query = "SELECT a.usr_ultnov, a.cod_conult, a.cod_ultnov, ".
                               "IF( a.fec_ultnov IS NULL, DATE_FORMAT( a.fec_salida, '%Y-%m-%d %H:%i' ),
                               DATE_FORMAT( a.fec_ultnov, '%Y-%m-%d %H:%i' )  ), ".
                               "a.fec_salida, a.fec_manala ".
                         "FROM ".BASE_DATOS.".tab_despac_despac a ".
                        "WHERE a.num_despac='".$despacho[$j][0]."'";
              $consulta = new Consulta( $query, $this -> conexion );
              $despac = $consulta -> ret_matriz();
              //if($transpor[$i][0]=='900012501')
              //echo "sss".$despacho[$j][0]."tiempo:".$tiem;
              //agreega el tiempo de duracion de la pernoctacion
              if($despac[0][1]!='')
              {
                $query ="SELECT tiem_duraci 
                           FROM ".BASE_DATOS.".tab_despac_contro
                          WHERE num_despac = '".$despacho[$j][0]."' AND
                                fec_contro = '".$despac[0][3]."".":00'
                       ORDER BY fec_creaci DESC" ;
                $consulta = new Consulta( $query, $this -> conexion );
                $tiem_diff = $consulta -> ret_matriz();                    
              }
              if($tiem_diff)
              {
                if($tiem_diff[0][0]!=0 )
                  $tiem = $tiem_diff[0][0];
              }
              else
              {
                if($despac[0][2]!='')
                {
                  $query ="SELECT tiem_duraci 
                             FROM ".BASE_DATOS.".tab_despac_noveda
                            WHERE num_despac = '".$despacho[$j][0]."' AND
                                  fec_noveda = '".$despac[0][3]."".":00'" ;
                  $consulta = new Consulta( $query, $this -> conexion );
                  $tiem_diff = $consulta -> ret_matriz();
                }
                if($tiem_diff)
                {
                  if($tiem_diff[0][0]!=0  )
                    $tiem = $tiem_diff[0][0];
                }
              }
              if($despac[0][2]=='' && $despac[0][1]=='' || ( $despac[0]['fec_manala'] == $despac[0]["fec_salida"] ))
                $tiem='0';
              //Se hace la validacion para alarmas acumulables
              $mFecManala = $despac[0][5];

              $tiemdif='';
              if( $mFecManala )
              {
                $query = "SELECT CAST( (TIME_TO_SEC( TIMEDIFF(NOW(), '".$mFecManala."')) / 60) AS UNSIGNED INTEGER )";
                $consulta = new Consulta( $query, $this -> conexion );
                $mTieDif = $consulta -> ret_matriz(); 
                $tiemdif = $mTieDif[0][0];
              }

              if(is_numeric( $tiem ))
              {
                //aca SACA TIEMPO
                if($tiemdif=='')
                {
                  $query = "SELECT TIMEDIFF(  NOW(), '".$despac[0][3]."' ) ";
                  $consulta = new Consulta( $query, $this -> conexion );
                  $tiemdif = $consulta -> ret_matriz(); 
                  $tiemdif = explode(":",$tiemdif[0][0]);
                  $tiemdif = ($tiemdif[0]*60)+$tiemdif[1];
                }
                $tieret ="";
                if ($tiemdif>$tiem)
                {
                  $indicad_mostrar_condic = 1;
                  $tieret = $tiemdif-$tiem;
                }
              }
              else
              {
                $query = "SELECT a.cod_contro,a.fec_alarma ".
                           "FROM ".BASE_DATOS.".tab_despac_seguim a ".
                          "WHERE a.num_despac='".$despacho[$j][0]."'
                             AND a.ind_estado = 1 
                             AND a.cod_contro NOT IN (SELECT cod_contro 
                                                        FROM ".BASE_DATOS.".tab_despac_noveda b 
                                                       WHERE b.num_despac='".$despacho[$j][0]."' 
                                                         AND a.num_despac = b.num_despac
                                                         AND a.cod_rutasx = b.cod_rutasx)
                           ORDER BY a.fec_planea "; 
                $consulta = new Consulta( $query, $this -> conexion );
                $sitios = $consulta -> ret_matriz();

                $fecha = date('Y-m-d H:i:s');
                if($fecha<$sitios[0][1])
                  $query = "SELECT TIMEDIFF(  '".$sitios[0][1]."',NOW()   ) ";
                else
                  $query = "SELECT TIMEDIFF(  NOW(),'".$sitios[0][1]."'   ) ";
                $consulta = new Consulta( $query, $this -> conexion );
                $tiemdif = $consulta -> ret_matriz(); 

                $tiemdif = explode(":",$tiemdif[0][0]);
                $tiemdif = ($tiemdif[0]*60)+$tiemdif[1];
                $tieret ="";
                $indicad_mostrar_condic = 1;
                $tieret = $tiemdif-$tieret;
                if($fecha<$sitios[0][1])
                  $tieret = $tieret *(-1);
              }
              if($tieret>0)
              {
                $y= 1;
                for( $z = 0; $z <= sizeof( $colores ); $z++ )
                {
                  if( $tieret <= $colores[$z][0] )
                  {
                    $x = $z;      
                    $y=2;
                    break;
                  }
                }
                if( $y==1 )
                {     
                  $color_a[0] = $colores[sizeof($colores)-1][1];
                }
                else
                {
                  $color_a[0] = $colores[$x][1];
                }
              }
              else
              {
                $color_a[0] = 'FFFFFF';
                if($despacho[$j][3]!='1')
                    $transpor[$i][4]++;
                if($despacho[$j][3]!='1')
                    $totsires++;     
                //if($fue_pla[0][0] != 1)
                if($transpor[$i][22] == 1)
                {
                  $color_a[0] = 'FFFFFF';
                  //aca SUMA FUERA DE PLATAFORMA
                  $transpor[$i][21]++;
                  $totfupla++;             
                }
              }
            for( $z = 0; $z <= sizeof( $colores ); $z++ )
              {
					if( $colores[$z][1] == $color_a[0] )
					{
					  if($despacho[$j][3]!='1')
						$transpor[$i][ 6 + $z ]++;
					}

              }
              // if( $color_a[0] = $colores[sizeof($colores)-1][1] && )
              if($tieret!="")
                $desenruta[$i]["tiempo"]= $tieret;
              else
                if ($tiemdif!="")
                  $desenruta[$i]["tiempo"]= $tiemdif-$tiem;
                  //finaliza nuevo calculo de alarmas
            }
          }
        }
      }
      
      echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
      echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
      echo " <script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/comet.js'></script> ";
      echo "<TABLE  BORDER='0' CELLPADDING='0' CELLSPACING='0' WIDTH='100%'>";
      $formulario = new Formulario ("index.php","post","Despachos en Transito","form_despac\" id= \"formdespacID");
      $_SESSION["ind_atras"] = "si";
      $formulario -> nueva_tabla();
      $formulario -> texto ("Celular:","text","celular\" id=\"celularID\" onchange=\"if(this.value!=''){location.href='index.php?window=central&cod_servic=3302&menant=3302&opcion=8&celu='+this.value+'';}",0,9,12,"t2","$GLOBALS[valdec]");
      //$formulario -> nueva_tabla();
      //$formulario -> imagen("Exportar","../".DIR_APLICA_CENTRAL."/imagenes/boton_excel.jpg",  "Exportar",120,30,0,  "onClick=\"top.window.open('../".DIR_APLICA_CENTRAL."/export/exp_despac_transi.php?".$exp."')\"",1,0);

      $formulario -> nueva_tabla();
      $formulario -> linea ("Fecha y Hora Reporte :: ".$fechoract,1,"t2");

      $formulario -> nueva_tabla();
      if(!$totsires) $totsires = "-";
      if(!$totporll) $totporll = "-";
      if(!$totfupla) $totfupla = "-";

      $formulario -> linea("",0,"t");
      $formulario -> linea("",0,"t");
      $formulario -> linea("TOTALES",0,"t",0,0,"right");
      $formulario -> linea($totaldes,0,"t",0,0,"center");
      $formulario -> linea($totsires,0,"t",0,0,"center");
      for($i = 0; $i < sizeof($alarmas); $i++)
      {
        if(!$totalarm[$i]) $totalarm[$i] = "-";
          $formulario -> linea($totalarm[$i],0,"t",0,0,"center");
      }
      
      //vlix85  
       $formulario -> linea(0,0,"t",0,0,"center");
      
      $formulario -> linea($totfupla,0,"t",0,0,"center");
      $formulario -> linea($totporll,0,"t",0,0,"center");
      if($datos_usuario['cod_perfil']==COD_PERFIL_ADMINIST || $datos_usuario['cod_perfil']==COD_PERFIL_SUPEFARO || $datos_usuario['cod_perfil']==COD_PERFIL_SUPERUSR || $datos_usuario['cod_perfil'] == COD_PERFIL_AUDITORX )
      {
        $formulario -> linea($totdefin[0][0],0,"t",0,0,"center");
        $formulario -> linea("",1,"t",0,0,"center");
      }
      else
        $formulario -> linea($totdefin[0][0],1,"t",0,0,"center");
        
      //Titulos del informe
      $formulario -> linea ("No.",0,"t");
      $formulario -> lista ("","tipser\" id=\"tipID\" onchange=\"Tipser(1);",$tipos,0 );
      $formulario -> linea ("Transportadora",0,"t");
      $formulario -> linea ("No. Despachos",0,"t");
      $formulario -> linea ("Sin Retraso",0,"t");
      for($i = 0; $i < sizeof($alarmas); $i++)
        $formulario -> linea ($alarmas[$i][1]." - ".$alarmas[$i][3]." Min",0,"i",0,0,"center",$alarmas[$i][2]);
        
      
      //vlix85
      //------------------------------------------------------------
      if(in_array($datos_usuario['cod_perfil'], array(COD_PERFIL_ADMINIST,COD_PERFIL_SUPEFARO,COD_PERFIL_SUPERUSR,COD_PERFIL_SUPEGENE))){
        $formulario -> linea ("EAL",0,"i",0,0,"center","#7fff99");
      }
      //------------------------------------------------------------

      
        
        
      $formulario -> linea ("Fuera de Plataforma",0,"t");    
      $formulario -> linea ("Por Llegada",0,"t");
    
      if($datos_usuario['cod_perfil']==COD_PERFIL_ADMINIST || $datos_usuario['cod_perfil']==COD_PERFIL_SUPEFARO || $datos_usuario['cod_perfil']==COD_PERFIL_SUPERUSR || $datos_usuario['cod_perfil'] == COD_PERFIL_AUDITORX )
      {
        $formulario -> linea ("A cargo Empresa",0,"t");
        $formulario -> lista ("","us1\" id=\"us1ID\" onchange=\"Tipser(1);",$user,1 );
      }
      else
        $formulario -> linea ("A cargo Empresa",1,"t");    


   

      //detalle del informe
      for($i = 0; $i < sizeof($transpor); $i++)
      {
        $query = "SELECT a.num_despac
                    FROM ".BASE_DATOS.".tab_despac_despac a, 
                         ".BASE_DATOS.".tab_despac_vehige d
                   WHERE a.num_despac = d.num_despac AND 
                         a.fec_salida Is Not Null AND 
                         a.fec_salida <= NOW() AND 
                         a.fec_llegad Is Null AND
                         a.ind_defini ='1' AND
                         a.ind_anulad = 'R' AND 
                         a.ind_planru = 'S' AND 
                         d.cod_transp = '".$transpor[$i][0]."'";
        if( $datos_usuario["cod_perfil"] == "" )
        {
          $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_usuari"]);
          if($filtro -> listar($this -> conexion))
          {
            $datos_filtro = $filtro -> retornar();
            $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
          }  
        }
        else
        {
          //PARA EL FILTRO DE ASEGURADORA
          $filtro = new Aplica_Filtro_Perfil( $this -> cod_aplica ,COD_FILTRO_ASEGUR,$datos_usuario["cod_perfil"]);
          if($filtro -> listar($this -> conexion))
          {
            $datos_filtro = $filtro -> retornar();
            $query = $query . " AND a.cod_asegur = '$datos_filtro[clv_filtro]' ";
          }
        }
        $query.="		  GROUP BY 1 ";
        $consulta = new Consulta($query, $this -> conexion); 
        $trasi = $consulta -> ret_matriz();
        
        $pordefin=0;
        $pordefin=sizeof($trasi);
        /*$query = "SELECT s.contro,s.num_despac 
                      FROM ( SELECT a.cod_contro as contro, a.num_despac as num_despac 
                               FROM ".BASE_DATOS.".tab_despac_noveda a, ".BASE_DATOS.".tab_despac_vehige c,
                                    ".BASE_DATOS.".tab_despac_despac e
                              WHERE a.num_despac = c.num_despac AND
                                    e.num_despac = a.num_despac AND e.ind_defini ='1' 
                                AND c.cod_transp = '".$transpor[$i][0]."' AND
                                    a.fec_noveda = e.fec_ultnov 
                              UNION 
                             SELECT a.cod_contro as contro, a.num_despac as num_despac 
                               FROM ".BASE_DATOS.".tab_despac_contro a, ".BASE_DATOS.".tab_despac_vehige c,
                                    ".BASE_DATOS.".tab_despac_despac e
                              WHERE a.num_despac = c.num_despac AND 
                                    e.num_despac = a.num_despac AND e.ind_defini ='1' 
                                AND c.cod_transp = '".$transpor[$i][0]."' AND
                                    a.fec_contro = e.fec_ultnov 
                           )as s GROUP BY 2";
        $consulta = new Consulta($query, $this -> conexion);
        $ultimno = $consulta -> ret_matriz();
        $pordefin=0;
        foreach($ultimno as $ultimnov)
        {
          $query = "SELECT a.ind_urbano 
                      FROM ".BASE_DATOS.".tab_genera_contro a 
                     WHERE a.cod_contro = ".$ultimnov[0]." AND 
                           a.ind_urbano = '1' ";
          $consulta = new Consulta($query, $this -> conexion); 
          $pcontrurb = $consulta -> ret_matriz();
          if($desurb && $pcontrurb)
        $pcomparar = $ultimnov[0];
        else
        $pcomparar = $ultimopc[0][0];
        
        if($pcomparar != $ultimnov[0] && $ultimnov[0] != CONS_CODIGO_PCLLEG )
        {
        $pordefin++;
        }
        }
        $query = "SELECT a.num_despac
        FROM ".BASE_DATOS.".tab_despac_despac a, 
        ".BASE_DATOS.".tab_despac_vehige d
        LEFT JOIN ".BASE_DATOS.".tab_despac_noveda e ON a.num_despac=e.num_despac
        LEFT JOIN ".BASE_DATOS.".tab_despac_contro f ON a.num_despac=f.num_despac
        WHERE a.num_despac = d.num_despac AND 
        e.num_despac IS NULL AND
        f.num_despac IS NULL AND
        a.fec_salida Is Not Null AND 
        a.fec_salida <= NOW() AND 
        a.fec_llegad Is Null AND
        a.ind_defini ='1' AND
        a.ind_anulad = 'R' AND 
        a.ind_planru = 'S' AND 
        d.cod_transp = '".$transpor[$i][0]."'";
        if( $datos_usuario["cod_perfil"] == "" )
        {
        $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_usuari"]);
        if($filtro -> listar($this -> conexion))
        {
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
        }  
        }
        $query.="		  GROUP BY 1 ";
        $consulta = new Consulta($query, $this -> conexion); 
        $trasi = $consulta -> ret_matriz();
        foreach($trasi as $trasis){
        $pordefin++;
        } 
        */

        $ciudad_a = $objciud -> getSeleccCiudad($transpor[$i][2]);
 
        if(!$transpor[$i][4])
          $transpor[$i][4] = "-";
        else
          $transpor[$i][4] = "<a href=\"index.php?cod_servic=3302&window=central&defini=0&atras=si&transp=".$transpor[$i][0].
                             "&alacla=S&totregif=".$transpor[$i][4]." \"target=\"centralFrame\">".$transpor[$i][4]."</a>";
        if(!$transpor[$i][21])
          $transpor[$i][21] = "-";
        else
          $transpor[$i][21] = "<a href=\"index.php?cod_servic=3302&window=central&atras=si&transp=".$transpor[$i][0].
                             "&FUEPLA=1\" \"target=\"centralFrame\">".$transpor[$i][21]."</a>";

        if(!$transpor[$i][5])
          $transpor[$i][5] = "-";
        else
          $transpor[$i][5] = "<a href=\"index.php?cod_servic=3302&window=central&atras=si&transp=".$transpor[$i][0].
                             "&alacla=L&totregif=".$transpor[$i][5]." \"target=\"centralFrame\">".$transpor[$i][5]."</a>";

        $transpor[$i][3] = "<a href=\"index.php?cod_servic=3302&window=central&atras=si&transp=".$transpor[$i][0].
                           "&totregif=".$transpor[$i][3]." \"target=\"centralFrame\">".$transpor[$i][3]."</a>";

        if( $transpor[$i][20] == 'ESFERAS Y MONITOREO ACTIVO' )
          $transpor[$i][20] = "ESFERAS Y M/A";
          
        $formulario -> linea (($i+1),0,"i"); //No.
        $formulario -> linea ($transpor[$i][20],0,"i");//Tipo de servicio
        $formulario -> linea ($transpor[$i][1],0,"i");//Transportadora
        $formulario -> linea ($transpor[$i][3],0,"i",0,0,"center");//No. Despachos
        $formulario -> linea ($transpor[$i][4],0,"i",0,0,"center");//Sin Retraso

		for($j = 0; $j < sizeof($alarmas); $j++)//alarmas
        {
          if(!$transpor[$i][6 + $j])
            $transpor[$i][6 + $j] = "-";
          else
             $transpor[$i][6 + $j] = "<a href=\"index.php?cod_servic=3302&atras=si&window=central&defini=0&transp=".$transpor[$i][0].
                                     "&alacla=".$alarmas[$j][0]."&totregif=".$transpor[$i][6 + $j]." \"target=\"centralFrame\">".$transpor[$i][6 + $j]."</a>";
          
          $formulario -> linea ($transpor[$i][6 + $j],0,"i",0,0,"center");
        }
        
        //vlix85
        //------------------------------------------------------------------
        if(in_array($datos_usuario['cod_perfil'], array(COD_PERFIL_ADMINIST,COD_PERFIL_SUPEFARO,COD_PERFIL_SUPERUSR,COD_PERFIL_SUPEGENE))){
          $_TOTEAL = array_sum($vlix85[ $transpor[$i][0] ]);
          if($_TOTEAL>0){
            $_EAL = "<a href=\"index.php?cod_servic=3302&window=central&atras=si&EAL=1&transp=".$transpor[$i][0]." \"target=\"centralFrame\">".$_TOTEAL."</a>";
          }else{
            $_EAL = "<span>-</span>";
          }
          $formulario -> linea ($_EAL,0,"i",0,0,"center");//EAL
        }
        //------------------------------------------------------------------
  
        $formulario -> linea ($transpor[$i][21],0,"i",0,0,"center");//Fuera de Plataforma
        $formulario -> linea ($transpor[$i][5],0,"i",0,0,"center");//Por Llegada
        if($pordefin=='0')//A cargo Empresa
          $pordefin='-';
        else	
          $pordefin = "<a href=\"index.php?cod_servic=3302&window=central&defini=1&atras=si&transp=".$transpor[$i][0]." \"target=\"centralFrame\">".$pordefin."</a>";

        if($datos_usuario['cod_perfil']==COD_PERFIL_ADMINIST || $datos_usuario['cod_perfil']==COD_PERFIL_SUPEGENE || $datos_usuario['cod_perfil']==COD_PERFIL_SUPEFARO || $datos_usuario['cod_perfil']==COD_PERFIL_SUPERUSR || $datos_usuario['cod_perfil'] == COD_PERFIL_AUDITORX)
        {
          $query = "SELECT a.cod_usuari
                      FROM ".BASE_DATOS.".tab_monito_encabe a,
                           ".BASE_DATOS.".tab_monito_detall b,
                           ".BASE_DATOS.".tab_genera_usuari c
                      WHERE a.ind_estado = '1' AND
                            b.ind_estado = '1' AND
                            a.cod_consec = b.cod_consec AND
                            a.fec_inicia <= NOW() AND
                            a.fec_finalx >= NOW() AND
                            a.cod_usuari = c.cod_usuari AND
                            c.cod_perfil NOT IN (".CONS_PERFIL.") AND
                            b.cod_tercer = '".$transpor[$i][0]."'";
          $consulta = new Consulta($query, $this -> conexion);
          $usuari = $consulta -> ret_matriz();
          $usurs = '';
          foreach($usuari AS $usr)
          {
            $usurs .= $usr[0].",";
          }
          if($usurs=='')
            $usurs= "Sin Asignar";
          $formulario -> linea ($pordefin,0,"i",0,0,"center");//A cargo Empresa
          $formulario -> linea ($usurs,1,"i",0,0,"left");          
        }
        else
          $formulario -> linea ($pordefin,1,"i",0,0,"center");//A cargo Empresa
      }
      $formulario -> oculto("window","central",0);
      $formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],0);
      $formulario -> oculto("opcion",$GLOBALS["opcion"],0);
      $formulario -> cerrar();
      echo "<TABLE></TABLE>";
      mysql_close();
    }

    function Datos()
    {
      $datos_usuario = $this -> usuario -> retornar();
      $formulario = new Formulario ("index.php","post","Informacion del Despacho","form_item");
      $listado_prin = new Despachos($GLOBALS["cod_servic"],2,$this -> aplica,$this -> conexion);
      $listado_prin  -> Encabezado($GLOBALS["despac"],$formulario,$datos_usuario,0,"Despachos en Ruta");
      $listado_prin  -> PlanDeRuta($GLOBALS["despac"],$formulario,0);

      $formulario -> nueva_tabla();
      $formulario -> oculto("despac",$GLOBALS["despac"],0);
      $formulario -> oculto("opcion",$GLOBALS["opcion"],0);
      $formulario -> oculto("window","central",0);
      $formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],0);

    $formulario -> cerrar();
    }
//-------------------------------------
    function Informe()
    {
      session_start();
      include( "../lib/general/conexion_lib.inc" );
      include ("../lib/bd/seguridad/aplica_filtro_usuari_lib.inc");
      include ("../lib/bd/seguridad/aplica_filtro_perfil_lib.inc");
      //include( "../lib/general/dinamic_list.inc" );
      include( "../lib/general/form_lib.inc" );
      
      include( "../lib/general/tabla_lib.inc" );
      
      include( "../lib/GeneralFunctions.inc" );
      include( "../lib/general/paginador_lib.inc" );
      include( "../despac/Despachos.inc" );

      define("COD_PERFIL_SUPERUSR", "73");// constante para perfil administrador del sistema
      define("COD_PERFIL_ADMINIST", "1");// constante para perfil administrador de empresa
      define("COD_PERFIL_SUPEFARO", "8");// supervisor faro
      define("COD_PERFIL_SUPEGENE", "84");// supervisor General
      define("COD_PERFIL_AUDITORX", "97");// Auditor CLF

      define("COD_FILTRO_EMPTRA", "1");// constante para filtro de Transportadora
      define("COD_FILTRO_AGENCI", "2");// constante para filtro de Agencias de la transportadora
      define("COD_FILTRO_CLIENT", "3");// constante para filtro de Generadores de Carga
      define("COD_FILTRO_CONDUC", "4");//constante para filtro de conductores
      define("COD_FILTRO_PROPIE", "5");//constante para filtro de propietarios
      define("COD_FILTRO_POSEED", "6");//constante para filtro de poseedores
      define("COD_FILTRO_ASEGUR", "8");//constante para filtro de asegurador

      define("COD_ESTADO_PENDIE", "0");//constante para el estado Pendiente
      define("COD_ESTADO_ACTIVO", "1");//constante para el estado Activo
      define("COD_ESTADO_INACTI", "2");//constante para el estado Inactivo
      define("CONS_CODIGO_PCLLEG", "9999");// constante para el codigo del puesto de control de llegada
      define("COD_APLICACION", "1");// constante para el codigo de la aplicacion
      define("CONS_PERFIL", "'79'");// constante para los perfiles que no deben salir en listado de turnos

      $datos_usuario = $_SESSION["datos_usuario"];

      $US = base64_decode($_POST["sachu"]);
      $CL = base64_decode($_POST["petro"]);
      $BD = base64_decode($_POST["oplix"]);
      $CN = base64_decode($_POST["central"]);
      $ES = base64_decode($_POST["estilo"]);
      $tip = $_POST["tipser"];
      define( DIR_APLICA_CENTRAL, $CN );
      define( BASE_DATOS, $BD );
      define( ESTILO, $ES );

      $this -> conexion = new Conexion( "bd10.intrared.net:3306", $US, $CL , $BD );

      $this -> aplica = 1;

      $objciud = new Despachos( $GLOBALS["cod_servic"], $GLOBALS["opcion"], $this -> aplica, $this -> conexion );
      $fechoract = date("d-M-Y h:i:s A");

      /*$query = "SELECT a.ind_remdes
                    FROM ".BASE_DATOS.".tab_config_parame a
                   WHERE a.ind_remdes = '1'";
      $consulta = new Consulta($query, $this -> conexion);
      $manredes = $consulta -> ret_matriz();*/

      $query = "SELECT a.ind_desurb
                  FROM ".BASE_DATOS.".tab_config_parame a
                 WHERE a.ind_desurb = '1' ";
      $consulta = new Consulta($query, $this -> conexion);
      $desurb = $consulta -> ret_matriz();

      $query = "SELECT cod_tipser, nom_tipser
                  FROM ".BASE_DATOS.".tab_genera_tipser";
      $consulta = new Consulta($query, $this -> conexion);
      $tipos = $consulta -> ret_matriz();

      $inicio[0][0] = 0;
      $inicio[0][1] = '-';
      $tipos= array_merge($inicio,$tipos);
      if($tip!='')
      { 
        $query = "SELECT cod_tipser, nom_tipser
        FROM ".BASE_DATOS.".tab_genera_tipser
        WHERE cod_tipser = '$tip'";
        $consulta = new Consulta($query, $this -> conexion);
        $tipo = $consulta -> ret_matriz();
        $tipos= array_merge($tipo,$tipos);
      }

      $query = "SELECT a.ind_restra
      FROM ".BASE_DATOS.".tab_config_parame a
      WHERE a.ind_restra = '1'";
      $consulta = new Consulta($query, $this -> conexion);
      $resptran = $consulta -> ret_matriz();

      $query = "SELECT j.cod_tercer,j.abr_tercer,j.cod_ciudad
                  FROM ".BASE_DATOS.".tab_despac_despac a,
                       ".BASE_DATOS.".tab_despac_seguim b,
                       ".BASE_DATOS.".tab_vehicu_vehicu i,
                       ".BASE_DATOS.".tab_tercer_tercer j,
                       ".BASE_DATOS.".tab_despac_vehige d
                       LEFT JOIN ".BASE_DATOS.".tab_transp_tipser k ON k.cod_transp = d.cod_transp
                 WHERE a.num_despac = d.num_despac AND
                       a.num_despac = b.num_despac AND
                       i.num_placax = d.num_placax AND
                       d.cod_transp = j.cod_tercer AND
                       a.fec_salida Is Not Null AND
                       a.fec_salida <= NOW() AND
                       a.fec_llegad Is Null AND
                       a.ind_anulad = 'R' AND
                       a.ind_planru = 'S' ";
                       //d.cod_transp NOT IN ( SELECT cod_tercer FROM ".BASE_DATOS.".tab_config_alianz ) AND

      if($tip!='' && $tip!='0')
      {
        $query .= " AND k.cod_tipser = '$tip' AND
        k.fec_creaci = (SELECT MAX(l.fec_creaci) FROM ".BASE_DATOS.".tab_transp_tipser l
        WHERE l.cod_transp = d.cod_transp )";
      }

      if( strtolower( $datos_usuario["cod_usuari"] ) == 'alogistica' )
        $query .= " AND d.cod_transp IN ( SELECT cod_tercer FROM ".BASE_DATOS.".tab_config_alianz ) ";

      if( $datos_usuario["cod_perfil"] == "" )
      {
        $this -> cod_aplica = '1';

        //PARA EL FILTRO DE CONDUCTOR
        $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_usuari"]);
        if($filtro -> listar($this -> conexion))
        {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
        }

        //PARA EL FILTRO DE PROPIETARIO
        $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_usuari"]);
        if($filtro -> listar($this -> conexion))
        {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND i.cod_propie = '$datos_filtro[clv_filtro]' ";
        }

        //PARA EL FILTRO DE POSEEDOR
        $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_usuari"]);
        if($filtro -> listar($this -> conexion))
        {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND i.cod_tenedo = '$datos_filtro[clv_filtro]' ";
        }

        //PARA EL FILTRO DE EMPRESA
        $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
        if($filtro -> listar($this -> conexion))
        {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
        }

        //PARA EL FILTRO DE ASEGURADORA
        $filtro = new Aplica_Filtro_Usuari( $this -> cod_aplica ,COD_FILTRO_ASEGUR,$datos_usuario["cod_usuari"]);
        if($filtro -> listar($this -> conexion))
        {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND a.cod_asegur = '$datos_filtro[clv_filtro]' ";
        }

        //PARA EL FILTRO DEL CLIENTE
        $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_usuari"]);
        if($filtro -> listar($this -> conexion))
        {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
        }

        //PARA EL FILTRO DE LA AGENCIA
        $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_usuari"]);
        if($filtro -> listar($this -> conexion))
        {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
        }
      }
      else
      {
        $this -> cod_aplica = '1';
        //PARA EL FILTRO DE CONDUCTOR
        $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_perfil"]);
        if($filtro -> listar($this -> conexion))
        {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
        }

        //PARA EL FILTRO DE PROPIETARIO
        $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_perfil"]);
        if($filtro -> listar($this -> conexion))
        {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND i.cod_propie = '$datos_filtro[clv_filtro]' ";
        }

        //PARA EL FILTRO DE POSEEDOR
        $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_perfil"]);
        if($filtro -> listar($this -> conexion))
        {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND i.cod_tenedo = '$datos_filtro[clv_filtro]' ";
        }

        //PARA EL FILTRO DE EMPRESA
        $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
        if($filtro -> listar($this -> conexion))
        {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
        }

        //PARA EL FILTRO DE ASEGURADORA
        $filtro = new Aplica_Filtro_Perfil( $this -> cod_aplica ,COD_FILTRO_ASEGUR,$datos_usuario["cod_perfil"]);
        if($filtro -> listar($this -> conexion))
        {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND a.cod_asegur = '$datos_filtro[clv_filtro]' ";
        }

        //PARA EL FILTRO DEL CLIENTE
        $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_perfil"]);
        if($filtro -> listar($this -> conexion))
        {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
        }
        

        //PARA EL FILTRO DE LA AGENCIA
        $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_perfil"]);
        if($filtro -> listar($this -> conexion))
        {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
        }
      }

      $query = $query." GROUP BY 1 ORDER BY 2";
      $consulta = new Consulta($query, $this -> conexion);
      $transpor = $consulta -> ret_matriz();
      $query_exp = base64_encode($query);

      $exp .= "url=".NOM_URL_APLICA."&db=".BASE_DATOS."&nomarchive=Despachos_en_Transito&query_exp=".$query_exp."";

      $query = "SELECT a.cod_alarma,a.nom_alarma,a.cod_colorx,a.cant_tiempo
                  FROM ".BASE_DATOS.".tab_genera_alarma a
              ORDER BY 4 ";
      $consulta = new Consulta($query, $this -> conexion);
      $alarmas = $consulta -> ret_matriz();

      $query ="SELECT cant_tiempo,cod_colorx
                 FROM ".BASE_DATOS.".tab_genera_alarma
                ORDER BY 1";
      $consulta = new Consulta ($query, $this -> conexion);
      $colores  = $consulta -> ret_matriz();
      $totaldes = $totporll = $totsires = 0;

      $query = "SELECT a.ind_defini
                  FROM ".BASE_DATOS.".tab_despac_despac a, 
                       ".BASE_DATOS.".tab_despac_seguim b, 
                       ".BASE_DATOS.".tab_despac_vehige d
                 WHERE a.num_despac = d.num_despac AND 
                       a.num_despac = b.num_despac AND 
                       a.fec_salida Is Not Null AND 
                       a.fec_salida <= NOW() AND 
                       a.fec_llegad Is Null AND 
                       a.ind_anulad = 'R' AND 
                       a.ind_planru = 'S' AND 
                       a.ind_defini= '1' ";

      if( $datos_usuario["cod_perfil"] == "" )
      {
        $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_usuari"]);
        if($filtro -> listar($this -> conexion))
        {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
        }

        //PARA EL FILTRO DE EMPRESA
        $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
        if($filtro -> listar($this -> conexion))
        {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
        }

        //PARA EL FILTRO DE ASEGURADORA
        $filtro = new Aplica_Filtro_Usuari( $this -> cod_aplica ,COD_FILTRO_ASEGUR,$datos_usuario["cod_usuari"]);
        if($filtro -> listar($this -> conexion))
        {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND a.cod_asegur = '$datos_filtro[clv_filtro]' ";
        }
      }
      else
      {
        $filtro = new Aplica_Filtro_Perfil( $this -> cod_aplica ,COD_FILTRO_ASEGUR,$datos_usuario["cod_perfil"]);
        if($filtro -> listar($this -> conexion))
        {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND a.cod_asegur = '$datos_filtro[clv_filtro]' ";
        }
      }
        $consulta = new Consulta($query, $this -> conexion); 
        $totdefin = $consulta -> ret_matriz();

      for($i = 0; $i < sizeof($transpor); $i++)
      {
        //Se obtiene el tipo de servicio
        $query = "SELECT b.nom_tipser ,tie_conurb, tie_contro
                    FROM ".BASE_DATOS.".tab_transp_tipser a,
                         ".BASE_DATOS.".tab_genera_tipser b,
                         (SELECT MAX(num_consec) AS num_consec,cod_transp 
                            FROM ".BASE_DATOS.".tab_transp_tipser
                        GROUP BY cod_transp) AS c
                   WHERE a.cod_transp='".$transpor[$i][0]."' AND 
                         a.cod_tipser = b.cod_tipser AND
                         a.cod_transp = c.cod_transp AND
                         a.num_consec = c.num_consec ";
        $consulta = new Consulta( $query, $this -> conexion );
        $tipser = $consulta -> ret_arreglo();
        $transpor[$i][20] = $tipser[0];

        $transpor[$i][3] = $transpor[$i][4] = $transpor[$i][5] = $transpor[$i][21] = $transpor[$i][22] = 0;

        $query = "SELECT a.num_despac, a.fec_ultnov, a.fec_manala,
                         a.ind_defini, a.cod_conult,DATE_FORMAT( a.fec_ultnov, '%Y-%m-%d %H:%i' )
                    FROM ".BASE_DATOS.".tab_despac_despac a, 
                         ".BASE_DATOS.".tab_despac_seguim b, 
                         ".BASE_DATOS.".tab_despac_vehige d 
                   WHERE a.num_despac = d.num_despac AND 
                         a.num_despac = b.num_despac AND 
                         a.fec_salida Is Not Null AND 
                         a.fec_salida <= NOW() AND 
                         a.fec_llegad Is Null AND 
                         a.ind_anulad = 'R' AND 
                         a.ind_planru = 'S' AND
                         d.cod_transp = '".$transpor[$i][0]."'";

        if( $datos_usuario["cod_perfil"] == "" )
        {
          $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_usuari"]);
          if($filtro -> listar($this -> conexion))
          {
            $datos_filtro = $filtro -> retornar();
            $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
          }

          //PARA EL FILTRO DE EMPRESA
          $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
          if($filtro -> listar($this -> conexion))
          {
            $datos_filtro = $filtro -> retornar();
            $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
          }

          //PARA EL FILTRO DE ASEGURADORA
          $filtro = new Aplica_Filtro_Usuari( $this -> cod_aplica ,COD_FILTRO_ASEGUR,$datos_usuario["cod_usuari"]);
          if($filtro -> listar($this -> conexion))
          {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND a.cod_asegur = '$datos_filtro[clv_filtro]' ";
          }
        }
        else
        {
          $filtro = new Aplica_Filtro_Perfil( $this -> cod_aplica ,COD_FILTRO_ASEGUR,$datos_usuario["cod_perfil"]);
          if($filtro -> listar($this -> conexion))
          {
            $datos_filtro = $filtro -> retornar();
            $query = $query . " AND a.cod_asegur = '$datos_filtro[clv_filtro]' ";
          }
        }

        $query.="		  GROUP BY 1 ";
        $consulta = new Consulta($query, $this -> conexion); 
        $despacho = $consulta -> ret_matriz();

        for($j = 0; $j < sizeof($despacho); $j++)
        {

        $query	=	"(SELECT e.ind_fuepla, a.cod_conult,a.cod_ultnov,a.fec_ultnov,a.usr_ultnov,
                    b.obs_contro,c.nom_contro,d.nom_noveda,	b.fec_contro,
                    b.cod_contro,e.nom_noveda,b.fec_creaci
              FROM	".BASE_DATOS.".tab_despac_despac	a	LEFT	JOIN
                    ".BASE_DATOS.".tab_despac_contro	b	ON
                    a.num_despac	=	b.num_despac	AND
                    a.cod_consec	=	b.cod_consec	LEFT	JOIN
                    ".BASE_DATOS.".tab_genera_noveda	e	ON
                    b.cod_noveda	=	e.cod_noveda,
                    ".BASE_DATOS.".tab_genera_contro	c,
                    ".BASE_DATOS.".tab_genera_noveda	d
             WHERE	a.cod_conult	=	c.cod_contro	AND
                    a.cod_ultnov	=	d.cod_noveda	AND
                    a.num_despac	=	'".$despacho[$j][0]."')
             UNION
           (SELECT	e.ind_fuepla, a.cod_conult,a.cod_ultnov,a.fec_ultnov,a.usr_ultnov,
                    b.des_noveda,c.nom_contro,d.nom_noveda,b.fec_noveda,
                    b.cod_contro,e.nom_noveda,	b.fec_creaci
              FROM	".BASE_DATOS.".tab_despac_despac	a	LEFT	JOIN
                    ".BASE_DATOS.".tab_despac_noveda	b	ON
                    a.num_despac	=	b.num_despac	
                    LEFT	JOIN ".BASE_DATOS.".tab_genera_noveda	e	ON
                    b.cod_noveda	=	e.cod_noveda,
                    ".BASE_DATOS.".tab_genera_contro	c,
                    ".BASE_DATOS.".tab_genera_noveda	d
             WHERE	a.cod_conult	=	c.cod_contro	AND
                    a.cod_ultnov	=	d.cod_noveda	AND
                    a.num_despac	=	'".$despacho[$j][0]."')
          ORDER	BY	12	DESC	LIMIT	1";
        $consulta = new Consulta($query, $this -> conexion); 
        $fue_pla = $consulta -> ret_matriz();
        $transpor[$i][22] = $fue_pla[0][0];

          $transpor[$i][3]++;
          $totaldes++;

          $query = "SELECT a.cod_rutasx 
                      FROM ".BASE_DATOS.".tab_despac_seguim a 
                     WHERE a.num_despac = ".$despacho[$j][0]." GROUP BY 1 ";
          $consulta = new Consulta($query, $this -> conexion);
          $totrutas = $consulta -> ret_matriz();

          if( sizeof($totrutas) < 2 ) 
            $camporder = "fec_planea";
          else 
            $camporder = "fec_alarma";

          $query = "SELECT a.cod_contro 
                      FROM ".BASE_DATOS.".tab_despac_seguim a, 
                           ".BASE_DATOS.".tab_despac_vehige c,
                           (SELECT MAX(b.".$camporder.") as fec 
                              FROM ".BASE_DATOS.".tab_despac_seguim b 
                             WHERE b.num_despac = ".$despacho[$j][0]." ) as  d 
                     WHERE a.num_despac = c.num_despac AND 
                           c.num_despac = ".$despacho[$j][0]." AND 
                           a.".$camporder." = d.fec ";
          $consulta = new Consulta($query, $this -> conexion);
          $ultimopc = $consulta -> ret_matriz();

          $query = "SELECT a.cod_contro, a.fec_noveda, d.fec_planea 
                      FROM ".BASE_DATOS.".tab_despac_noveda a, 
                           ".BASE_DATOS.".tab_despac_seguim d 
                     WHERE a.num_despac = d.num_despac AND 
                           a.cod_rutasx = d.cod_rutasx AND 
                           a.cod_contro = d.cod_contro AND 
                           a.num_despac = ".$despacho[$j][0]." AND 
                           a.fec_noveda = '".$despacho[$j][5].":00"."'";
          $consulta = new Consulta($query, $this -> conexion);
          $ultimnov = $consulta -> ret_matriz();
          if($ultimnov)
          {
            $query = "SELECT a.ind_urbano 
                        FROM ".BASE_DATOS.".tab_genera_contro a 
                       WHERE a.cod_contro = ".$ultimnov[0][0]." AND 
                             a.ind_urbano = '1' ";
            $consulta = new Consulta($query, $this -> conexion); 
            $pcontrurb = $consulta -> ret_matriz();

            /*$query = "SELECT b.fec_alarma 
                          FROM ".BASE_DATOS.".tab_despac_despac a, 
                               ".BASE_DATOS.".tab_despac_seguim b, 
                               ".BASE_DATOS.".tab_despac_vehige c
                         WHERE a.num_despac = c.num_despac AND
                               c.num_despac = b.num_despac AND
                               a.num_despac = ".$despacho[$j][0]." AND
                               b.fec_planea > '".$ultimnov[0][2]."'
                      ORDER BY 1 ";
            $consulta = new Consulta($query, $this -> conexion);
            $pfechala = $consulta -> ret_matriz();*/
          }
          else
            $pcontrurb = NULL;

          if( $desurb && $pcontrurb)
            $pcomparar = $ultimnov[0][0];
          else
            $pcomparar = $ultimopc[0][0];
          if( $despacho[$j][3]!='1' && ($pcomparar == $ultimnov[0][0] || $ultimnov[0][0] == CONS_CODIGO_PCLLEG) )
          {
            $transpor[$i][5]++;
            $totporll++;
          }
          else
          {
            /*if(!$ultimnov)
            {
            $query = "SELECT MIN(a.fec_alarma)
            FROM ".BASE_DATOS.".tab_despac_seguim a
            WHERE a.num_despac = ".$despacho[$j][0]."
            ";

            $consulta = new Consulta($query, $this -> conexion);
            $fecalarm = $consulta -> ret_matriz();

            $tiempo_proxnov = $fecalarm[0][0];
            }
            else
            $tiempo_proxnov = $pfechala[0][0];

            //Calculo del tiempo retrasado
            $query = "SELECT TIME_TO_SEC( TIMEDIFF(NOW(), '".$tiempo_proxnov."')) / 60";

            $tiempo = new Consulta($query, $this -> conexion);
            $tiemp_demora = $tiempo -> ret_arreglo();*/

            if($despacho[$j][3]!='1')
            {
              $tiemp_alarma = NULL;
              //inicia nuevo calculo de alarmas

              $query = "SELECT  cod_tipdes ".
                         "FROM ".BASE_DATOS.".tab_despac_despac ".
                        "WHERE num_despac='".$despacho[$j][0]."'";
              $consulta = new Consulta( $query, $this -> conexion );
              $cod_tipdes = $consulta -> ret_matriz();
              if($tipser)
              {
                if($tipser[0]=='ECL')
                {
                  $tiem ='P';
                }
                else
                {
                  if($cod_tipdes[0][0]=='1')
                  {
                    $tiem = $tipser[1]; 
                  }
                  else
                  {
                    $tiem = $tipser[2];
                  }  
                }
              }
              else
              {
                if($cod_tipdes[0][0]=='1')
                  $tiem =25;
                else
                  $tiem=80;  
              }

              $query = "SELECT a.usr_ultnov, a.cod_conult, a.cod_ultnov, ".
                               "IF( a.fec_ultnov IS NULL, DATE_FORMAT( a.fec_salida, '%Y-%m-%d %H:%i' ), 
                               DATE_FORMAT( a.fec_ultnov, '%Y-%m-%d %H:%i' )  ), ".
                               "a.fec_salida, a.fec_manala ".
                         "FROM ".BASE_DATOS.".tab_despac_despac a ".
                        "WHERE num_despac='".$despacho[$j][0]."'";
              $consulta = new Consulta( $query, $this -> conexion );
              $despac = $consulta -> ret_matriz();
              if($despac[0][2]=='' && $despac[0][1]=='' || ( $despac[0]['fec_manala'] == $despac[0]["fec_salida"] ))
                $tiem='0';
              //agreega el tiempo de duracion de la pernoctacion
              if($despac[0][1]!='')
              {
                $query ="SELECT tiem_duraci 
                           FROM ".BASE_DATOS.".tab_despac_contro
                          WHERE num_despac = '".$despacho[$j][0]."' AND
                                fec_contro = '".$despac[0][3]."".":00'
                       ORDER BY fec_creaci DESC" ;//agregado
                $consulta = new Consulta( $query, $this -> conexion );
                $tiem_diff = $consulta -> ret_matriz();                    
              }
              if($tiem_diff)
              {
                if($tiem_diff[0][0]!=0 && !$despac[0][5])
                $tiem = $tiem_diff[0][0];
              }
              else
              {
                if($despac[0][2]!='')
                {
                  $query ="SELECT tiem_duraci 
                             FROM ".BASE_DATOS.".tab_despac_noveda
                            WHERE num_despac = '".$despacho[$j][0]."' AND
                                  fec_noveda = '".$despac[0][3]."".":00'" ;
                  $consulta = new Consulta( $query, $this -> conexion );
                  $tiem_diff = $consulta -> ret_matriz();
                }
                if($tiem_diff)
                {
                  if($tiem_diff[0][0]!=0 && !$despac[0][5] )
                    $tiem = $tiem_diff[0][0];
                }         
              }
              //Se hace la validacion para alarmas acumulables
              $mFecManala = $despac[0][5];

              $tiemdif='';
              if( $mFecManala )
              {
                $query = "SELECT CAST( (TIME_TO_SEC( TIMEDIFF(NOW(), '".$mFecManala."')) / 60) AS UNSIGNED INTEGER )";
                $consulta = new Consulta( $query, $this -> conexion );
                $mTieDif = $consulta -> ret_matriz(); 
                $tiemdif = $mTieDif[0][0];
              }

              if(is_numeric( $tiem ))
              {
                //aca
                if($tiemdif=='')
                {
                  $query = "SELECT TIMEDIFF(  NOW(), '".$despac[0][3]."' ) ";
                  $consulta = new Consulta( $query, $this -> conexion );
                  $tiemdif = $consulta -> ret_matriz(); 
                  $tiemdif = explode(":",$tiemdif[0][0]);
                  $tiemdif = ($tiemdif[0]*60)+$tiemdif[1];
                }
                $tieret ="";
                if ($tiemdif>$tiem)
                {
                  $indicad_mostrar_condic = 1;
                  $tieret = $tiemdif-$tiem;
                }
              }
              else
              {
                $query = "SELECT a.cod_contro,a.fec_alarma ".
                           "FROM ".BASE_DATOS.".tab_despac_seguim a ".
                          "WHERE a.num_despac='".$despacho[$j][0]."'
                                 AND a.ind_estado = 1 
                                 AND a.cod_contro NOT IN (SELECT cod_contro 
                                                            FROM ".BASE_DATOS.".tab_despac_noveda b 
                                                           WHERE b.num_despac='".$despacho[$j][0]."' 
                                                             AND a.num_despac = b.num_despac
                                 AND a.cod_rutasx = b.cod_rutasx)
                        ORDER BY a.fec_planea "; 
                $consulta = new Consulta( $query, $this -> conexion );
                $sitios = $consulta -> ret_matriz();

                $fecha = date('Y-m-d H:i:s');
                if($fecha<$sitios[0][1])
                  $query = "SELECT TIMEDIFF(  '".$sitios[0][1]."',NOW()   ) ";
                else
                  $query = "SELECT TIMEDIFF(  NOW(),'".$sitios[0][1]."'   ) ";
                $consulta = new Consulta( $query, $this -> conexion );
                $tiemdif = $consulta -> ret_matriz(); 

                $tiemdif = explode(":",$tiemdif[0][0]);
                $tiemdif = ($tiemdif[0]*60)+$tiemdif[1];
                $tieret ="";
                $indicad_mostrar_condic = 1;
                $tieret = $tiemdif;
                if($fecha<$sitios[0][1])
                  $tieret = $tieret *(-1);
              }

              if($tieret>0)
              {
                $y= 1;
                for( $z = 0; $z <= sizeof( $colores ); $z++ )
                {
                  if( $tieret <= $colores[$z][0] )
                  {
                    $x = $z;      
                    $y=2;
                    break;
                  }
                }
                if( $y==1 )
                {        
                  $color_a[0] = $colores[sizeof($colores)-1][1];
                } 
                else
                {
                  $color_a[0] = $colores[$x][1];
                }
              }
              else
              {
                //if($fue_pla[0][0] != 1)
                if($transpor[$i][22] != 1)
                {
                  $color_a[0] = 'FFFFFF';
                  //aca SUMA SIN RETRASO
                  if($despacho[$j][3]!='1')
                    $transpor[$i][4]++;
                  if($despacho[$j][3]!='1')
                    $totsires++;               
                }
                else
                {
                  $color_a[0] = 'FFFFFF';
                  //aca SUMA FUERA DE PLATAFORMA
                  $transpor[$i][21]++;
                  $totfupla++;             
                }
              }
              for( $z = 0; $z <= sizeof( $colores ); $z++ )
              {
                if( $colores[$z][1] == $color_a[0] )
                {
                  if($despacho[$j][3]!='1')
                    $transpor[$i][ 6 + $z ]++;
                }
              }
              if($tieret!="")
                $desenruta[$i]["tiempo"]= $tieret;
              else
                if ($tiemdif!="")
              $desenruta[$i]["tiempo"]= $tiemdif-$tiem;
              //finaliza nuevo calculo de alarmas
            }
          }
        }
      }
      echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
      echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
      echo " <script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/comet.js'></script> ";
      
      
      echo "<TABLE  BORDER='0' CELLPADDING='0' CELLSPACING='0' WIDTH='100%'>";

      $formulario = new Formulario ("index.php","post","Despachos en Transito","form_despac\" id= \"formdespacID");
      $_SESSION[ind_atras] = "si";
      //$formulario -> nueva_tabla();
      //$formulario -> imagen("Exportar","../".DIR_APLICA_CENTRAL."/imagenes/boton_excel.jpg",  "Exportar",120,30,0,  "onClick=\"top.window.open('../".DIR_APLICA_CENTRAL."/export/exp_despac_transi.php?".$exp."')\"",1,0);

      $formulario -> nueva_tabla();
      $formulario -> linea ("Fecha y Hora Reporte :: ".$fechoract,1,"t2");

      $formulario -> nueva_tabla();

      if(!$totsires) $totsires = "-";
      if(!$totporll) $totporll = "-";

      $formulario -> linea("",0,"t");
      $formulario -> linea("",0,"t");
      $formulario -> linea("TOTALES",0,"t",0,0,"right");
      $formulario -> linea($totaldes,0,"t",0,0,"center");
      $formulario -> linea($totsires,0,"t",0,0,"center");
      for($i = 0; $i < sizeof($alarmas); $i++)
      {
        if(!$totalarm[$i]) $totalarm[$i] = "-";
          $formulario -> linea($totalarm[$i],0,"t",0,0,"center");
      }

      $formulario -> linea($totfupla,0,"t",0,0,"center");
      $formulario -> linea($totporll,0,"t",0,0,"center");

      $formulario -> linea($totdefin[0][0],1,"t",0,0,"center");
      $formulario -> linea ("No.",0,"t");
      $formulario -> lista ("","tipser\" id=\"tipID\" onchange=\"Tipser(1);",$tipos,0 );
      $formulario -> linea ("Transportadora",0,"t");
      $formulario -> linea ("No. Despachos",0,"t");
      $formulario -> linea ("Sin Retraso",0,"t");

      for($i = 0; $i < sizeof($alarmas); $i++)
        $formulario -> linea ($alarmas[$i][1]." - ".$alarmas[$i][3]." Min",0,"i",0,0,"center",$alarmas[$i][2]);

      $formulario -> linea ("Fuera de Plataforma",0,"t");    
      $formulario -> linea ("Por Llegada",0,"t");
      $formulario -> linea ("A cargo Empresa",1,"t");    
      for($i = 0; $i < sizeof($transpor); $i++)
      {
        $query = "SELECT a.num_despac
                    FROM ".BASE_DATOS.".tab_despac_despac a, 
                         ".BASE_DATOS.".tab_despac_vehige d
                   WHERE a.num_despac = d.num_despac AND 
                         a.fec_salida Is Not Null AND 
                         a.fec_salida <= NOW() AND 
                         a.fec_llegad Is Null AND
                         a.ind_defini ='1' AND
                         a.ind_anulad = 'R' AND 
                         a.ind_planru = 'S' AND 
                         d.cod_transp = '".$transpor[$i][0]."'";
        if( $datos_usuario["cod_perfil"] == "" )
        {
          $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_usuari"]);
          if($filtro -> listar($this -> conexion))
          {
            $datos_filtro = $filtro -> retornar();
            $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
          }
          //PARA EL FILTRO DE EMPRESA
          $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
          if($filtro -> listar($this -> conexion))
          {
            $datos_filtro = $filtro -> retornar();
            $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
          }

          //PARA EL FILTRO DE ASEGURADORA
          $filtro = new Aplica_Filtro_Usuari( $this -> cod_aplica ,COD_FILTRO_ASEGUR,$datos_usuario["cod_usuari"]);
          if($filtro -> listar($this -> conexion))
          {
            $datos_filtro = $filtro -> retornar();
            $query = $query . " AND a.cod_asegur = '$datos_filtro[clv_filtro]' ";
          }
        }
        else
        {
          $filtro = new Aplica_Filtro_Perfil( $this -> cod_aplica ,COD_FILTRO_ASEGUR,$datos_usuario["cod_perfil"]);
          if($filtro -> listar($this -> conexion))
          {
            $datos_filtro = $filtro -> retornar();
            $query = $query . " AND a.cod_asegur = '$datos_filtro[clv_filtro]' ";
          }
        }
        $query.="GROUP BY 1 ";
        $consulta = new Consulta($query, $this -> conexion); 
        $trasi = $consulta -> ret_matriz();
        $pordefin=0;
        $pordefin=sizeof($trasi);

        /*$query = "SELECT s.contro,s.num_despac FROM(
                                                      SELECT a.cod_contro as contro, a.num_despac as num_despac 
        FROM ".BASE_DATOS.".tab_despac_noveda a, ".BASE_DATOS.".tab_despac_vehige c,
        ".BASE_DATOS.".tab_despac_despac e
        WHERE a.num_despac = c.num_despac AND
        e.num_despac = a.num_despac AND e.ind_defini ='1' 
        AND c.cod_transp = '".$transpor[$i][0]."' AND
        a.fec_noveda = e.fec_ultnov 
        UNION 
        SELECT a.cod_contro as contro, a.num_despac as num_despac 
        FROM ".BASE_DATOS.".tab_despac_contro a, ".BASE_DATOS.".tab_despac_vehige c,
        ".BASE_DATOS.".tab_despac_despac e
        WHERE a.num_despac = c.num_despac AND 
        e.num_despac = a.num_despac AND e.ind_defini ='1' 
        AND c.cod_transp = '".$transpor[$i][0]."' AND
        a.fec_contro = e.fec_ultnov 

)as s GROUP BY 2";
$consulta = new Consulta($query, $this -> conexion);
$ultimno = $consulta -> ret_matriz();
$pordefin=0;
foreach($ultimno as $ultimnov)
{

$query = "SELECT a.ind_urbano 
FROM ".BASE_DATOS.".tab_genera_contro a 
WHERE a.cod_contro = ".$ultimnov[0]." AND 
a.ind_urbano = '1' ";

$consulta = new Consulta($query, $this -> conexion); 
$pcontrurb = $consulta -> ret_matriz();

if($desurb && $pcontrurb)
$pcomparar = $ultimnov[0];
else
$pcomparar = $ultimopc[0][0];

if($pcomparar != $ultimnov[0] && $ultimnov[0] != CONS_CODIGO_PCLLEG )
{
$pordefin++;
}
}
$query = "SELECT a.num_despac
FROM ".BASE_DATOS.".tab_despac_despac a, 
".BASE_DATOS.".tab_despac_vehige d
LEFT JOIN ".BASE_DATOS.".tab_despac_noveda e ON a.num_despac=e.num_despac
LEFT JOIN ".BASE_DATOS.".tab_despac_contro f ON a.num_despac=f.num_despac
WHERE a.num_despac = d.num_despac AND 
e.num_despac IS NULL AND
f.num_despac IS NULL AND
a.fec_salida Is Not Null AND 
a.fec_salida <= NOW() AND 
a.fec_llegad Is Null AND
a.ind_defini ='1' AND
a.ind_anulad = 'R' AND 
a.ind_planru = 'S' AND 
d.cod_transp = '".$transpor[$i][0]."'";
if( $datos_usuario["cod_perfil"] == "" )
{
$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_usuari"]);
if($filtro -> listar($this -> conexion))
{
$datos_filtro = $filtro -> retornar();
$query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
}  
}
$query.="		  GROUP BY 1 ";
$consulta = new Consulta($query, $this -> conexion); 
$trasi = $consulta -> ret_matriz();
foreach($trasi as $trasis){
$pordefin++;
} 
*/

        $ciudad_a = $objciud -> getSeleccCiudad($transpor[$i][2]);

        if(!$transpor[$i][4]) 
          $transpor[$i][4] = "-";
        else
          $transpor[$i][4] = "<a href=\"index.php?cod_servic=3302&window=central&defini=0&atras=si&transp=".$transpor[$i][0].
                             "&alacla=S&totregif=".$transpor[$i][4]." \"target=\"centralFrame\">".$transpor[$i][4]."</a>";

        if(!$transpor[$i][21])
          $transpor[$i][21] = "-";
        else
          //if($transpor[$i][22] == 1)
          $transpor[$i][21] = "<a href=\"index.php?cod_servic=3302&window=central&defini=0&atras=si&transp=".$transpor[$i][0].
                             "&alacla=S&totregif=".$transpor[$i][21]." \"target=\"centralFrame\">".$transpor[$i][21]."</a>";

        if(!$transpor[$i][5]) 
          $transpor[$i][5] = "-";
        else
          $transpor[$i][5] = "<a href=\"index.php?cod_servic=3302&window=central&atras=si&transp=".$transpor[$i][0].
                             "&alacla=L&totregif=".$transpor[$i][5]." \"target=\"centralFrame\">".$transpor[$i][5]."</a>";

        $transpor[$i][3] = "<a href=\"index.php?cod_servic=3302&window=central&atras=si&transp=".$transpor[$i][0].
                           "&totregif=".$transpor[$i][3]." \"target=\"centralFrame\">".$transpor[$i][3]."</a>";

        if( $transpor[$i][20] == 'ESFERAS Y MONITOREO ACTIVO' ) 
          $transpor[$i][20] = "ESFERAS Y M/A";
        
        $formulario -> linea (($i+1),0,"i");
        $formulario -> linea ($transpor[$i][20],0,"i");
        $formulario -> linea ($transpor[$i][1],0,"i");
        $formulario -> linea ($transpor[$i][3],0,"i",0,0,"center");
        $formulario -> linea ($transpor[$i][4],0,"i",0,0,"center");
        for($j = 0; $j < sizeof($alarmas); $j++)
        {
          if(!$transpor[$i][6 + $j]) 
            $transpor[$i][6 + $j] = "-";
          else
            $transpor[$i][6 + $j] = "<a href=\"index.php?cod_servic=3302&atras=si&window=central&defini=0&transp=".$transpor[$i][0].
                                    "&alacla=".$alarmas[$j][0]."&totregif=".$transpor[$i][6 + $j]." \"target=\"centralFrame\">".$transpor[$i][6 + $j]."</a>";

          $formulario -> linea ($transpor[$i][6 + $j],0,"i",0,0,"center");
        }
        $formulario -> linea ($transpor[$i][21],0,"i",0,0,"center");//Fuera de Plataforma
        $formulario -> linea ($transpor[$i][5],0,"i",0,0,"center");
        if($pordefin=='0')
          $pordefin='-';
        else	
          $pordefin = "<a href=\"index.php?cod_servic=3302&window=central&defini=1&atras=si&transp=".$transpor[$i][0]." \"target=\"centralFrame\">".$pordefin."</a>";

        $formulario -> linea ($pordefin,1,"i",0,0,"center");
      }

      $formulario -> oculto("us1\" id=\"us1ID","",0);
      $formulario -> oculto("window","central",0);
      $formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],0);
      $formulario -> oculto("opcion",$GLOBALS["opcion"],0);

      $formulario -> cerrar();
      echo "<TABLE></TABLE>";
      mysql_close();
    }
    
    //vlix85  
    function GET_EAL($num_despac){
      
      
      $mQuery = "SELECT a.num_despac, a.cod_contro
                   FROM ".BASE_DATOS.".tab_despac_seguim a, ".BASE_DATOS.".tab_genera_contro b
                  WHERE a.cod_contro = b.cod_contro
                    AND b.ind_virtua =0
                    AND a.ind_estado =0
                    AND a.num_despac = '".$num_despac."'
                    AND (
                      a.num_despac, a.cod_contro
                    ) NOT
                    IN (
                      SELECT num_despac, cod_contro
                      FROM ".BASE_DATOS.".tab_despac_noveda
                    )";
      $consulta = new Consulta($mQuery, $this -> conexion);
      $consulta = $consulta -> ret_matriz();
      
      return count($consulta)>0 ? TRUE : FALSE;
      
    }
    
    
    function getPC(){
      
      @session_start();
      include( "../lib/general/conexion_lib.inc" );
      include( "../lib/general/tabla_lib.inc" );
      include( "../lib/general/constantes.inc" );
      define('BASE_DATOS', $_SESSION['BASE_DATOS']);
      $this -> conexion = new Conexion( "bd10.intrared.net:3306", $_SESSION["USUARIO"], $_SESSION["CLAVE"], BASE_DATOS );
      
      $num_despac = $_REQUEST["num_despac"];
      
      $mQuery = "SELECT a.num_despac, a.cod_rutasx, c.nom_rutasx, b.nom_contro
                   FROM ".BASE_DATOS.".tab_despac_seguim a,
                        ".BASE_DATOS.".tab_genera_contro b,
                        ".BASE_DATOS.".tab_genera_rutasx c
                  WHERE a.num_despac = '".$num_despac."'
                    AND a.cod_contro = b.cod_contro
                    AND a.cod_rutasx = c.cod_rutasx
                    AND a.ind_estado = 0
                    AND b.ind_virtua = '0'
                    AND a.cod_contro NOT IN ( SELECT cod_contro FROM ".BASE_DATOS.".tab_despac_noveda WHERE num_despac = '".$num_despac."' GROUP BY cod_contro ) ";
                    
      $consulta = new Consulta($mQuery, $this -> conexion);
      $consulta = $consulta -> ret_matriz();
      
      $mHtml  = "<table class='formulario' width='100%' cellspacing='0' cellpadding='4'>";
         $mHtml .= "<tbody>";
           $mHtml .= "<tr>";
              $mHtml .= "<td class='celda_titulo2' align='left' colspan='4'>";
                $mHtml .= "<b>Informaci&oacute;n Basica</b>";
              $mHtml .= "</td>";
           $mHtml .= "</tr>";
           $mHtml .= "<tr>";
              $mHtml .= "<td class='celda_titulo' align='right'>N&uacute;mero de Despacho: </td>";
              $mHtml .= "<td class='celda_info'>".$consulta[0]["num_despac"]."</td>";
              $mHtml .= "<td class='celda_titulo' align='right'>C&oacute;digo de Ruta: </td>";
              $mHtml .= "<td class='celda_info'>".$consulta[0]["cod_rutasx"]."</td>";
           $mHtml .= "</tr>";
            $mHtml .= "<tr>";
              $mHtml .= "<td class='celda_titulo' align='right'>Ruta: </td>";
              $mHtml .= "<td class='celda_info' colspan='3'>".utf8_decode($consulta[0]["nom_rutasx"])."</td>";
           $mHtml .= "</tr>";
            $mHtml .= "<tr>";
              $mHtml .= "<tD class='celda_titulo' align='center' colspan='4'>Detalle de Puestos de Control</td>";
           $mHtml .= "</tr>";
         $mHtml .= "</tbody>";
      $mHtml .= "</table>";
    
      $mHtml .= "<div style='width: 100% overflow: auto; height: 250px;'>";
       $mHtml  .= "<table class='formulario' width='100%' cellspacing='0' cellpadding='4'>";
         $mHtml .= "<tbody>";
         for($i=0, $len = sizeof($consulta); $i<$len; $i++){
                $mHtml .= "<tr>";
                  $mHtml .= "<td class='celda_info' align='center' colspan='4'>".$consulta[$i]["nom_contro"]."</td>";
               $mHtml .= "</tr>";
         }      
         $mHtml .= "<tbody>";
        $mHtml .= "</table>";
      $mHtml .= "</div>";

      echo $mHtml;
      
    }
    
    
//-------------------------------------
  }
  
  //$proceso = new Proc_despac($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
  $proceso = new Proc_despac($_SESSION['conexion'], $_SESSION['usuario_aplicacion'], $_SESSION['codigo']);

?>