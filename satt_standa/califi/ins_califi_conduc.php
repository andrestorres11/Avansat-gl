<?php

class Proc_despac
{
 var $conexion,
     $cod_aplica,
     $usuario;

 function __construct($co, $us, $ca)
 {
  $this -> conexion = $co;
  $this -> usuario = $us;
  $this -> cod_aplica = $ca;
  $this -> principal();
 }

 function principal()
 {
 
  if($_GET[opcionOut])
  {         
  
    if($this -> VerifyInterfRit( $_GET[despac] ) === '1')   
      $this -> Formulario();   
    else
    {
      $this -> InterfRitActive( $_GET[despac] );
      die();
    }
   
  }
  else
     {  
      switch($_REQUEST[opcion])
       {
        case "1":
          if($this -> VerifyInterfRit( $_GET[despac] ) === '1')   
            $this -> Formulario();
          else   
            $this -> InterfRitActive( $_GET[despac] );
        break;
        case "2":
          $this -> Insertar();
          break;
        default:         
          $this -> Listar();
        break;
       }//FIN SWITCH
     }// FIN ELSE GLOBALS OPCION
 }//FIN FUNCION PRINCIPAL

 function Listar()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $listado_prin = new Despachos($_REQUEST[cod_servic],1,$this -> cod_aplica,$this -> conexion);
   $listado_prin -> ListadoPrincipal($datos_usuario,0,NULL,0,NULL,NULL,1,0,0,NULL,0,1);  
   
 }

 function Formulario()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];
   //trae la fecha actual
  echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/califi.js\"></script>\n";
 

  $fecha_lleg = new Fecha();

  $formulario = new Formulario ("index.php","post","","form_ins");
  
  $mNumDespac['num_despac'] = $_GET['despac'];
  
  
  $mData = $this -> GetManifiData( $mNumDespac );
 
  //echo "<pre>"; print_r( $mData ); echo "</pre>";
  //-------------------------------------------------------------------
  $formulario -> nueva_tabla();
  $formulario -> linea("Datos Conductor",1,"t2");

  $formulario -> nueva_tabla();
  $formulario->linea("Código:", 0, "t");
  $formulario->linea($mData[0]['cod_conduc'], 0, "i");
  $formulario->linea("Nombres:", 0, "t");
  $formulario->linea($mData[0]['nom_conduc'], 1, "i");
  $formulario->linea("Apellidos:", 0, "t");
  $formulario->linea($mData[0]['ape_conduc'], 0, "i");
  $formulario->linea("Dirección:", 0, "t");
  $formulario->linea($mData[0]['dir_conduc'], 1, "i");
  $formulario->linea("Fijo:", 0, "t");
  $formulario->linea($mData[0]['fij_conduc'], 0, "i");
  $formulario->linea("Móvil:", 0, "t");
  $formulario->linea($mData[0]['mov_conduc'], 1, "i");

  //-------------------------------------------------------------------
  $formulario -> nueva_tabla();
  $formulario -> linea("Datos Manifiesto",1,"t2");

  $formulario -> nueva_tabla();
  $formulario->linea("Despacho:", 0, "t");
  $formulario->linea($_GET['despac'], 0, "i");  
  $formulario->linea("Transportadora:", 0, "t");
  $formulario->linea($mData[0]['nom_transp'], 1, "i");
  
  $formulario->linea("Placa:", 0, "t");
  $formulario->linea($mData[0]['num_placax'], 0, "i");
  $formulario->linea("Poseedor", 0, "t");
  $formulario->linea($mData[0]['nom_tenedo'], 1, "i");
  
  $formulario->linea("Origen:", 0, "t");
  $formulario->linea($mData[0]['ciu_origen'], 0, "i");
  $formulario->linea("Destino:", 0, "t");
  $formulario->linea($mData[0]['ciu_destin'], 1, "i");
  
  
  //---------------------------------------------------------------------
  $formulario -> nueva_tabla();
  $formulario -> linea("Calificaciíon",1,"t2");

  $formulario -> nueva_tabla();
  
  $mNumCalifi=  array(array("0"=>"", "1"=>"--")    ,array("0"=>"1","1"=>"Pésimo"),array("0"=>"2","1"=>"Malo"),
                            array("0"=>"3","1"=>"Regular"),array("0"=>"4","1"=>"Bueno") , array("0"=>"5","1"=>"Excelente"));
  $formulario->lista("Calificación:","num_califi\" id=\"num_califiID\" ", $mNumCalifi, "t2"); 
  $formulario -> texto ("Observaciones","textarea","obs_califi\" id=\"obs_califiID\" ",1,50,5,"","");
  

  $formulario -> nueva_tabla();
  $formulario -> oculto("nom_conduc\" id=\"nom_conducID\"",$mData[0]['nom_conduc'],0);
  $formulario -> oculto("ape_conduc\" id=\"ape_conducID\"",$mData[0]['ape_conduc'],0);
  
  
  $formulario -> oculto("usuario","$usuario",0);
  $formulario -> oculto("tercero","$tercero",0);
  $formulario -> oculto("feclle","$fec_actual",0);
  $formulario -> oculto("horlle","$hor_actual",0);
  $formulario -> oculto("window","central",0);
  $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
  $formulario -> oculto("opcion\" id=\"opcionID\" ",2,0);
  $formulario -> oculto("despac",$_REQUEST[despac],0);
  $formulario -> nueva_tabla();
  $formulario -> botoni("Aceptar","InsertCumpli()",0);
  $formulario -> botoni("Borrar","form_ins.reset()",1);
  $formulario -> cerrar();
  
  //Para la carga del Popup
  echo '<div id="AplicationEndDIV"></div>
        <div id="popupDIV" style="position: absolute; left: 0px; top: 0px; width: 300px; height: 300px; z-index: 3; visibility: hidden; overflow: auto; border: 5px solid #333333; background: white; ">
          <div id="result" ></div>
        </div>
        ';

 }//FIN FUNCION FORMULARIO


    function GetManifiData ( $mData )
    {
      $mSql = " SELECT '--Propietario--',
                        d.cod_tercer AS cod_propie, 
                        d.cod_tipdoc AS cod_tippro, 
                        d.cod_ciudad AS cod_ciupro,
                        'N' AS cod_genpro, 
                        TRIM(IF(d.abr_tercer IS NULL, CONCAT(d.nom_tercer,' ',d.nom_apell1,'',d.nom_apell2), d.abr_tercer )) AS nom_propie, 
                        IF(d.dir_domici IS NULL OR d.dir_domici = '' ,'Sin Especificar', d.dir_domici ) AS dir_propie,
                        IF(d.num_telef1 IS NULL OR d.num_telef1 = '' ,'0000000', d.num_telef1 ) AS fij_propie, 
                        IF(d.num_telmov IS NULL OR d.num_telmov = '' ,'0000000000', d.num_telmov ) AS mov_propie,
                       
                         '--Tenedor--',
                        e.cod_tercer AS cod_tenedo, 
                        e.cod_tipdoc AS cod_tipten, 
                        e.cod_ciudad AS cod_ciuten,
                        'N' AS cod_genten, 
                        TRIM(IF(e.abr_tercer IS NULL, CONCAT(e.nom_tercer,' ',e.nom_apell1,'',e.nom_apell2), e.abr_tercer )) AS nom_tenedo, 
                        IF(e.dir_domici IS NULL OR e.dir_domici = '','Sin Especificar', e.dir_domici ) AS dir_proten,
                        IF(e.num_telef1 IS NULL OR e.num_telef1 = '' ,'0000000', e.num_telef1) AS fij_proten, 
                        IF(e.num_telmov IS NULL OR e.num_telmov = '' ,'0000000000', e.num_telmov)AS mov_proten,
                        
                        '****Conductor****',
                        f.cod_tercer AS cod_conduc, f.cod_tipdoc AS cod_tipcon, f.cod_ciudad AS cod_ciucon, 'N' AS cod_gencon,
                        TRIM(f.nom_tercer) AS nom_conduc, IF(f.nom_apell1 IS NULL OR f.nom_apell1 = '','No Registra',TRIM(f.nom_apell1) ) AS ape_conduc, 
                        IF(f.dir_domici IS NULL OR f.dir_domici = '', 'No registra', f.dir_domici) AS dir_conduc, 
                        IF(f.num_telef1 IS NULL OR f.num_telef1 = '', '0000000', f.num_telef1  ) AS fij_conduc, 
                        IF(f.num_telmov IS NULL OR f.num_telmov = '', '0000000000', f.num_telmov) AS mov_conduc,
                        IF(l.num_catlic IS NULL OR l.num_catlic = '', '1', l.num_catlic ) AS cod_catlic, 
                        IF(l.num_licenc IS NULL OR l.num_licenc = '', '0000000', l.num_licenc) AS num_licenc, 
                        IF(l.fec_venlic IS NULL OR l.fec_venlic = '', '0000-00-00', l.fec_venlic) AS fec_venlic,
                        
                        '****Vehiculo*****',
                        b.num_placax,
                        c.ano_modelo,
                        IF(c.num_motorx IS NULL OR c.num_motorx = '', '000000', c.num_motorx) AS num_motorx,
                        IF(c.num_poliza IS NULL OR c.num_poliza = '', '000000', c.num_poliza) AS num_poliza,
                        '1' AS cod_asesoa,
                        c.cod_ciusoa,
                        '********************',
                        
                        a.cod_manifi, a.cod_ciuori, a.cod_ciudes, m.nom_ciudad AS ciu_origen, n.nom_ciudad AS ciu_destin,
                        UPPER(o.abr_tercer) AS nom_transp
                  
                  FROM ".BASE_DATOS.".tab_despac_despac a,
                       ".BASE_DATOS.".tab_despac_vehige b,
                       ".BASE_DATOS.".tab_vehicu_vehicu c,
                       ".BASE_DATOS.".tab_tercer_tercer d,
                       ".BASE_DATOS.".tab_tercer_tercer e,
                       ".BASE_DATOS.".tab_tercer_tercer f,
                       ".BASE_DATOS.".tab_tercer_conduc l,
                       ".BASE_DATOS.".tab_genera_ciudad m,
                       ".BASE_DATOS.".tab_genera_ciudad n,
                       ".BASE_DATOS.".tab_tercer_tercer o
                       
                       
                  WHERE a.num_despac = b.num_despac AND
                        b.num_placax = c.num_placax AND
                        c.cod_propie = d.cod_tercer AND
                        c.cod_tenedo = e.cod_tercer AND
                        b.cod_conduc = f.cod_tercer AND
                        b.cod_conduc = l.cod_tercer AND
                        a.cod_ciuori = m.cod_ciudad AND
                        a.cod_ciudes = n.cod_ciudad AND
                        b.cod_transp = o.cod_tercer AND
                        a.num_despac = '".$mData['num_despac']."' ";
      
      $consulta = new Consulta($mSql, $this->conexion);
      $mDataRit = $consulta -> ret_matriz();
      return $mDataRit;
    }
//FUNCION INSERTAR

// *****************************************************

 function Insertar()
 {
  
  
   $mens = new mensajes();
   
   if($this -> VerifyInterfRit( $_POST[despac] ) === '1')
   {
      // Consumo WSDL Interfaz
      include( "../".DIR_APLICA_CENTRAL."/lib/InterfRIT.inc" );         
      $_POST["nom_usuari"] = $_POST["usuario"];
      $mInterfRit = new InterfRIT ( $this -> conexion );            
      $mResult = $mInterfRit -> setCalifiConduc ( $_POST['despac'] , $_POST);   
      
      if($mResult[0] === true)
      {
        // Actualiza estado del despacho para la calificación --------------------------------------------------------------------
        $mSql = "UPDATE ".BASE_DATOS.".tab_despac_despac SET ind_califi = '1' WHERE num_despac = '{$_REQUEST['despac']}' ";
        $mUpdate = new Consulta( $mSql, $this -> conexion ); 
        $mNumDespac['num_despac'] = $_POST['despac'];
        $mDataCalifi = $this -> GetManifiData( $mNumDespac );
        
       
        // Maximo consecutivo por despacho
        $mSql = "SELECT IF( MAX(cod_consec) IS NULL, '1', MAX(cod_consec + 1)) FROM  ".BASE_DATOS.".tab_califi_conduc  WHERE num_despac = '{$_POST['despac']}' ";
        $consulta = new Consulta( $mSql, $this -> conexion );
        $mMaxConsec = $consulta -> ret_matriz( 'i' );
        
        // Inserta los datos de la calificación a nivel Local -----------------------------------------------------------------------
        $mSql = "INSERT INTO ".BASE_DATOS.".tab_califi_conduc ( 
                                                                cod_consec, num_despac, cod_manifi, cod_conduc, num_placax, num_califi,
                                                                usr_califi, obs_noveda, fec_califi
                                                               )
                                                       VALUES
                                                              (
                                                                '{$mMaxConsec[0][0]}','{$_POST['despac']}','{$mDataCalifi[0][cod_manifi]}', '{$mDataCalifi[0]['cod_conduc']}', 
                                                                '{$mDataCalifi[0]['num_placax']}', '{$_POST['num_califi']}', '{$this -> usuario -> nom_usuari}', '{$_POST['obs_califi']}',
                                                                NOW()
                                                              ) ";
        $mInsert = new Consulta( $mSql, $this -> conexion , 'R'); 
        
        if ( $mUpdate != false && $mInsert != false)
        {
          $mensaje = "<table><tr><td align=left>Respuesta RIT:</td></tr><tr><td>".utf8_decode( $mResult[1] )." </td></tr>
                             <tr><td>Datos: ".$mResult[2]."</td></tr>
                             <tr><td>Despacho: ".$_REQUEST['despac']."</td></tr>
                             <tr><td><a href='?cod_servic=".$_REQUEST["cod_servic"]."&window=central'>CALIFICAR OTRO CONDUCTOR</a></td></tr>
                      </table>";
          $mens->correcto("REGISTRO CALIFICACIÓN CONDUCTOR RIT", $mensaje);
        }
        else
        {
          $mensaje = "<table><tr><td>Error insercion BD Local </td></tr><tr><td>Datos: ".$mResult[2]."</td></tr><tr><td>Despacho: ".$_REQUEST['despac']."</td></tr></table>";
          $mens->error("REGISTRO CALIFICACION CONDUCTOR RIT", $mensaje);
        }
      }
      else
      {
        $mensaje = "Ha ocurrido un(os) Error(es) con la Interfaz del RIT:<br>".$mResult[1]." <br>Datos:".$mResult[2]."<br>Despacho:".$_REQUEST['despac'];
        $mens->error("REGISTRO CALIFICACION CONDUCTOR RIT", $mensaje);
      }
   }
   else
   {
     $this -> InterfRitActive( $_GET[despac] );
   }
   
    
 }
  function VerifyInterfRit ( $mNumDespac = NULL)
  { 
   
    $query = "SELECT ind_calcon
                   FROM ".BASE_DATOS.".tab_transp_tipser
                   WHERE cod_transp =  (SELECT b.cod_transp FROM ".BASE_DATOS.".tab_despac_vehige b WHERE b.num_despac = '".$mNumDespac."')";   
   
     
    $consulta = new Consulta($query, $this -> conexion);
             
    $mReturn =  $consulta -> ret_matriz( "i" );       
    $mReturnX =  end( $mReturn );      
    $mReturnX = $mReturnX[0][0] == '1' ? '1' : '0';
    return  $mReturnX; 
  }
  
  function InterfRitActive ( $mNumDespac = NULL )
  {
    $query = "SELECT UPPER(abr_tercer)
                   FROM ".BASE_DATOS.".tab_tercer_tercer
                   WHERE cod_tercer = ( SELECT b.cod_transp FROM ".BASE_DATOS.".tab_despac_vehige b WHERE b.num_despac = '".$mNumDespac."' )";   
    $consulta = new Consulta($query, $this -> conexion);             
    $mNomTransp =  $consulta -> ret_matriz( "i" );       
   
    $mens = new mensajes();
    $mensaje = "<table><tr><td>Señor Usuario: ".$this -> usuario -> nom_usuari."</td></tr><tr><td>La transportadora <spam style='color:red'><b>".$mNomTransp[0][0]."</b></spam> <br>NO tiene activo el parámetro<br> para hacer interfaz con RIT y enviar la calificación<br>del conductor.</td></tr>
                 <tr><td>Para activar la Interfaz con RIT debe hacerlo en:<br>Trasportadoras -> Tipo servicio<br>opcion: Transmitir Despachos Finalizados y Calificación de Conductores a la RIT.</td></tr></table>";
     $mens->error("REGISTRO CALIFICACION CONDUCTOR RIT", $mensaje);
  }
    

}//FIN CLASE PROC_DESPAC

   $proceso = new Proc_despac($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>