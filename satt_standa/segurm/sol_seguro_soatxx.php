<?php 

ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);

/*! \Class: SolicitarSOAT
*  \brief: Clase encargada de hacer la conexion para hacer la solicitud de SOAT
*  \author: Ing. Luis Manrique
*  \date: 22/10/2019
*  \param: $mConnection  -  Variable de clase que almacena la conexion de la Base de datosm biene desde el framework
*  \return array
*/

class SolicitarSOAT
{
  var $conexion = NULL;   
  
  /*! \fn: SolicitarSOAT
  *  \brief: constructor de php4 para la clase
  *  \author: Ing. Luis Manrique
  *  \date: 16/07/2015   
  *  \param: fConection  : Conexion de base de datos 
  *  \param: mParams     : Array con los datos a enviar 
  *  \return n/a
  */
  function SolicitarSOAT( $mConnection, $mData )
  {   

      #Se debe eliminar en produccion
      
      if($_REQUEST['Ajax'] == 'on'){
        include '../lib/ajax.inc';
        $this -> conexion = $AjaxConnection;
      }else{
        $this -> conexion = $mConnection;  
      }

      @include_once( '../'.DIR_APLICA_CENTRAL.'/lib/general/functions.inc' );
 

    switch( $_REQUEST["Option"] )
    { 
      case "loadFieldsPlacax":
        $this -> loadFieldsPlacax();
      break;
      case "sendmail":
        $this -> sendMail();
      break;
      default:
        $this -> setFormNoneSOAT();
      break;
    }
  }

  /*! \fn: stylesJS
  *  \brief: Metodo donde ingresa los archivos JS y css necesarios
  *  \author: Ing. Luis Manrique
  *  \date: 23/10/2019
  *  \return n/a
  */ 

  function stylesJS(){



    /*$mForm->SetJs("jquery17");
    $mForm->SetJs("jquery");
    $mForm->SetCssJq("jquery");
    $mForm->SetJs("sweetalert2.all.8.11.8");
    $mForm->SetJs("sol_seguro_soatxx");*/

    IncludeJS( "jquery17.js");  
    IncludeJS( "jquery.js");        
    echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";
    IncludeJS( "sweetalert2.all.8.11.8.js");
    IncludeJS( "sol_seguro_soatxx.js?ran=".rand(5, 20));
  }

  /*! \fn: setFormNoneSOAT
  *  \brief: Metodo que muestra formulario de solicitud de creacion de poliza
  *  \author: Ing. Luis Manrique
  *  \date: 24/10/2019    
  *  \return n/a
  */  

  function setFormNoneSOAT()
  {
    $tipoDoc = array(
                      "0" => array('', '--Seleccione--'), 
                      "1" => array('Nit', 'Nit'), 
                      "2" => array('Cedula', 'Cedula')
                    );
    $this -> stylesJS();

    # Abre Form

    $mForm = new FormLib(2);
      $mForm->Row("td");
          $mForm->Form(array("action" => "index.php?cod_servic=".$_REQUEST["cod_servic"],
              "method" => "post",
              "name" => "frm_solSoat",
              "enctype" => "multipart/form-data"));             
          
          $mForm -> Table("tr");
              $mForm -> Line( "REALIZAR SOLICITUD DE SEGURO SOAT", "t2", 0, 0, "center");
          $mForm -> CloseTable("tr");

          $mForm -> Table("tr");
              $mForm -> Label ( "&nbsp;", "name:info1; align:center; colspan:6; class:Alert; exp:yes; end:yes" );
              $mForm -> Line( "DATOS BASICOS DEL VEHICULO", "t2", 0, 0, "center");
          $mForm -> CloseTable("tr");
          
          $mForm -> Table( "tr" );
            $mForm -> Row( "td" );
              $mForm -> OpenDiv( "id:section100" );
          
                $mForm -> Table( "tr" ); 
                    
                    $mForm ->  Label( "*Placa:", "for:num_placaxID; width:25%;" );
                    $mForm ->  Input( "name:num_placax; maxlength:60; size:6; width:25%;");
                    $mForm ->  Label( "*Modelo:", "for:ano_modeloID; width:25%;" );
                    $mForm ->  Input( "name:ano_modelo; maxlength:60; size:4; end:yes; width:25%;");
                    
                    $mForm ->  Label( "*Marca:", "for:nom_marcaxID; width:25%;" );
                    $mForm ->  Input( "name:nom_marcax; maxlength:60; size:40; width:25%;");
                    $mForm ->  Label( "*Linea vehiculo:", "for:nom_lineaxID; width:25%;" );
                    $mForm ->  Input( "name:nom_lineax; maxlength:60; size:40; end:yes; width:25%;");

                    $mForm ->  Label( "*N° Motor:", "for:num_motorxID; width:25%;" );
                    $mForm ->  Input( "name:num_motorx; maxlength:60; size:40; width:25%;");
                    $mForm ->  Label( "*N° Chasis o N° Serie:", "for:num_seriexID; width:25%;" );
                    $mForm ->  Input( "name:num_seriex; maxlength:60; size:40; end:yes; width:25%;");
                $mForm -> CloseTable( "tr" ); 

                  $mForm -> Table("tr");
                      $mForm -> Label ( "&nbsp;", "name:info1; align:center; colspan:6; class:Alert; exp:yes; end:yes" );
                      $mForm -> Line( "DATOS BASICOS DEL PROPIETARIO", "t2", 0, 0, "center");
                  $mForm -> CloseTable("tr");

                $mForm -> Table( "tr" ); 

                    $mForm ->  Label( "*Tipo Documento:", "for:tip_documID; width:25%;" );
                    $mForm ->  Select2 ($tipoDoc,  array("name" => "tip_docum", "validate" => "select", "obl"=>"1", "id" => "tip_documID", "width" => "25%", "key"=> $tipoDoc) );
                    $mForm ->  Label( "*Documento:", "for:cod_tercerID; width:25%;" );
                    $mForm ->  Input( "name:cod_tercer; maxlength:60; size:60; end:yes; width:25%;");

                    $mForm ->  Label( "*Nombre:", "for:nom_tercerID; width:25%;" );
                    $mForm ->  Input( "name:nom_tercer; maxlength:60; size:60; width:25%;");
                    $mForm ->  Label( "*Primer Apellido:", "for:nom_apell1ID; width:25%;" );
                    $mForm ->  Input( "name:nom_apell1; maxlength:60; size:60; end:yes; width:25%;");

                    $mForm ->  Label( "*Segundo Apellido:", "for:nom_apell2ID; width:25%;" );
                    $mForm ->  Input( "name:nom_apell2; maxlength:60; size:60; width:25%;");
                    $mForm ->  Label( "*Dirección Residencia:", "for:dir_domiciID; width:25%;" );
                    $mForm ->  Input( "name:dir_domici; maxlength:60; size:60; end:yes; width:25%;");

                    $mForm ->  Label( "*N° Celular:", "for:num_telmovID; width:25%;" );
                    $mForm ->  Input( "name:num_telmov; maxlength:60; size:60; width:25%;");
                    $mForm ->  Label( "*Correo electrónico:", "for:dir_emailxID; width:25%;" );
                    $mForm ->  Input( "name:dir_emailx; maxlength:60; size:60; end:yes; width:25%;");

                    $mForm ->  Label( "Pago(Empresa):", "for:pag_empresID; width:25%;" );
                    $mForm ->  CheckBox( "name:pag_empres; maxlength:60; size:60; width:25%; type:checkbox;");
                    $mForm ->  Label( "Tarjeta Propiedad:", "for:tar_propieID; width:25%;" );
                    $mForm ->  File( "name:tar_propie; maxlength:60; size:60; end:yes; width:25%;");
                    
                $mForm -> CloseTable( "tr" ); 
                
                $mForm -> Table( "tr" );
                    $mForm -> StyleButton( "name:but_action; align:center; value:Enviar; onclick:SubmitForm(); end:yes" );
                $mForm -> CloseTable( "tr" );

              $mForm -> CloseDiv();
            $mForm -> CloseRow( "td" );
          $mForm -> CloseTable("tr");

          $mForm -> Hidden( "name:standar; value:".DIR_APLICA_CENTRAL );
          $mForm -> Hidden( "name:cod_servic; value:" . $_REQUEST['cod_servic'] );
          $mForm -> Hidden( "name:window; value:central" );
          
          $mForm -> CloseForm();  
        $mForm->CloseRow("td");
        echo $mForm->MakeHtml();
  }


  /*! \fn: loadFieldsPlacax
  *  \brief: Metodo que consulta y genera la información de los campos si encuentra la Placa en la BD
  *  \author: Ing. Luis Manrique
  *  \date: 24/10/2019    
  *  \return n/a
  */  
  function loadFieldsPlacax(){
      $mSql = "SELECT a.num_placax,
                      a.ano_modelo,
                      b.nom_marcax,
                      c.nom_lineax,
                      a.num_motorx,
                      a.num_seriex,
                      a.cod_propie,
                      d.nom_tercer,
                      d.nom_apell1,
                      d.nom_apell2,
                      d.dir_domici,
                      d.num_telmov,
                      d.dir_emailx
                FROM  ".BASE_DATOS.".tab_vehicu_vehicu a
          INNER JOIN  ".BASE_DATOS.".tab_genera_marcas b
                  ON  a.cod_marcax = b.cod_marcax
          INNER JOIN  ".BASE_DATOS.".tab_vehige_lineas c
                  ON  a.cod_lineax = c.cod_lineax AND b.cod_marcax = c.cod_marcax
          INNER JOIN  ".BASE_DATOS.".tab_tercer_tercer d
                  ON  a.cod_propie = d.cod_tercer
               WHERE  a.num_placax LIKE '%".$_REQUEST['term']."%'
            GROUP BY  a.num_placax
                  ";

      $consulta = new Consulta( $mSql, $this -> conexion );
      $datPlacax = $consulta->ret_matriz();
      
      $data = array();
      for($i=0, $len = count($datPlacax); $i<$len; $i++){
         $data [] = '{"label":"'.$datPlacax[$i]['num_placax'].'",
                      "value":"'. $datPlacax[$i]['num_placax'].'", 
                      "id":"'. $datPlacax[$i]['num_placax'].'", 
                      "nom_marcax":"'. $datPlacax[$i]['nom_marcax'].'", 
                      "ano_modelo":"'. $datPlacax[$i]['ano_modelo'].'", 
                      "nom_lineax":"'. $datPlacax[$i]['nom_lineax'].'", 
                      "num_motorx":"'. $datPlacax[$i]['num_motorx'].'", 
                      "num_seriex":"'. $datPlacax[$i]['num_seriex'].'", 
                      "cod_propie":"'. $datPlacax[$i]['cod_propie'].'", 
                      "nom_tercer":"'. strtoupper($datPlacax[$i]['nom_tercer']).'", 
                      "nom_apell1":"'. strtoupper($datPlacax[$i]['nom_apell1']).'", 
                      "nom_apell2":"'. strtoupper($datPlacax[$i]['nom_apell2']).'", 
                      "dir_domici":"'. $datPlacax[$i]['dir_domici'].'", 
                      "num_telmov":"'. $datPlacax[$i]['num_telmov'].'",
                      "dir_emailx":"'. $datPlacax[$i]['dir_emailx'].'"}'; 
      }
      echo '['.join(', ',$data).']';

  }

  /*! \fn: form_mail
  *  \brief: Metodo que genera la funcionalidad de crear correos
  *  \author: Ing. Luis Manrique
  *  \date: 23/10/2019
  *  \return n/a
  */ 

  function form_mail($sPara, $sParaN, $sAsunto, $sCuerpo, $sDe, $sDeN){ 
    //Variables necesarias
    $bHayFicheros = 0; 
    $sCabeceraTexto = ""; 
    $sAdjuntos = "";
    $adjuntos = array();
    $sTexto = "";

    //Formatea el campo Files organizando los archivos correctamente
    if(!empty($_FILES['tar_propie']['name'])){
      foreach ($_FILES as $key => $value) {
        foreach ($value as $key1 => $value1) {
          foreach ($value1 as $key2 => $value2) {
            $adjuntos[$key][$key2][$key1]= $value2;
          }
        }
      }
    }

    if ($sDe)$sCabeceras = "From:".$sDeN."<".$sDe.">" . "\r\n";
    if ($sPara)$sCabeceras .= 'To: '.$sParaN.' <'.$sPara.'>' . "\r\n";
    else $sCabeceras = ""; 
    
    $sCabeceras .= 'MIME-Version: 2.0' . "\r\n";
    $sTexto = $sCuerpo;

    $sCabeceras .= "Content-type: multipart/mixed;"; 
    $sCabeceras .= "boundary=\"--_Separador-de-mensajes_--\"\n";
    
    $sCabeceraTexto = "----_Separador-de-mensajes_--\n";
    $sCabeceraTexto .= 'Content-type: text/html; charset=utf-8' . "\r\n";
    $sCabeceraTexto .= "Content-transfer-encoding: 7BIT\n";
    
    $sTexto = $sCabeceraTexto.$sTexto;
    if(!empty($_FILES['tar_propie']['name'])){
        foreach ($adjuntos as $key => $zAdjunto){
          foreach ($zAdjunto as $vAdjunto) {
            if ($bHayFicheros == 0){
              $bHayFicheros = 1;
            }
          
          if ($vAdjunto["size"] > 0){
            $sAdjuntos .= "\n\n----_Separador-de-mensajes_--\n";
            $sAdjuntos .= "Content-type: ".$vAdjunto["type"].";name=\"".$vAdjunto["name"]."\"\n";;
            $sAdjuntos .= "Content-Transfer-Encoding: BASE64\n";
            $sAdjuntos .= "Content-disposition: attachment;filename=\"".$vAdjunto["name"]."\"\n\n";
            
            $oFichero = fopen($vAdjunto["tmp_name"], 'r');
            $sContenido = fread($oFichero, filesize($vAdjunto["tmp_name"])); 
            $sAdjuntos .= chunk_split(base64_encode($sContenido));
            fclose($oFichero);
          }
        }
        
          if ($bHayFicheros)
            $sTexto .= $sAdjuntos."\n\n----_Separador-de-mensajes_----\n"; 
        }
    }
    return(mail($sPara, $sAsunto, $sTexto, $sCabeceras));
  }


  /*! \fn: sendMail
  *  \brief: Envia la solicitud correspondiente mediante el correo
  *  \author: Ing. Luis Manrique
  *  \date: 23/10/2019    
  *  \return boolean
  */  
  function sendMail()
  {
    //Información de la empresa solicitante
    $mSql = "SELECT b.nom_perfil,
                    d.dir_emailx
              FROM  ".BASE_DATOS.".tab_genera_usuari a
        INNER JOIN  ".BASE_DATOS.".tab_genera_perfil b
                ON  a.cod_perfil = b.cod_perfil
        INNER JOIN  ".BASE_DATOS.".tab_aplica_filtro_perfil c
                ON  a.cod_perfil = c.cod_perfil
        INNER JOIN  ".BASE_DATOS.".tab_tercer_tercer d
                ON  c.clv_filtro = d.cod_tercer
              WHERE  a.cod_usuari = 'LUIGUICARGA'
                  ";
                  
    $consulta = new Consulta( $mSql, $this -> conexion );
    $datClient = $consulta->ret_matriz();

    //Valida si exite nombre o correo de la empresa
    if($datClient == '' || $datClient[0][0] == '' || $datClient[0][1] == ''){
      echo 2;
      die();
    }

    foreach ($datClient as $key => $value) {
      $nom_empres = $value[0];
      $dir_emassr = $value[1];
    }

    //Creando Variables automaticas
    foreach ($_REQUEST as $nombreCampo => $valorCampo) {
      ${$nombreCampo} = $valorCampo;
    }

    //Valiables enviadas por correo
    $http = $_SERVER['HTTPS'] = "ON" ? "https://" : "http://";
    $serverName = $_SERVER['HTTP_HOST'];
    $fecha = date("d/m/y h:i:s");
    $asunto = "NOTIFICACIÓN SOLICITUD SEGURO SOAT";

    //Generar plantilla
    $thefile = implode("", file('sol_seguro_soatpl.html'));
    $thefile = addslashes($thefile);
    $thefile = "\$r_file=\"" . $thefile . "\";";

    eval($thefile);
    $sTexto = $r_file;

    //Cambiar aqui el email 
    if ($this -> form_mail("asistencias@faro.com.co", "Asistencia Logistica", $asunto, $sTexto, $dir_emassr, $nom_empres)) 
      echo 1;
    else
      echo 0;
  }
}

  if($_REQUEST['Ajax'] == 'on'){
    $solicitud = new SolicitarSOAT( NULL, $_REQUEST );
  }else{
    $solicitud = new SolicitarSOAT( $this -> conexion, $_REQUEST );
  }
?>

