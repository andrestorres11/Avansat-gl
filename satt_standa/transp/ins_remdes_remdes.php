<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
class Proc_tercer
{
 var $conexion,
     $usuario,
     $cod_aplica;

 function __construct($co = null, $us = null, $ca = null)
 {

  if($_REQUEST["Ajax"] === 'on'){
    @include_once( "../lib/ajax.inc" );
    @include_once( "../lib/general/constantes.inc" );
    @include_once('../lib/general/functions.inc');
    $this -> conexion = $AjaxConnection;
    $this -> principal();
  }else{
    $this -> conexion = $co;
    $this -> usuario = $us;
    $this -> cod_aplica = $ca;
    $this -> principal();
  }
  
 }

 function principal()
 {
  if(!isset($_REQUEST["opcion"]))
    $this -> Buscar();
  else
     {
      switch($_REQUEST["opcion"])
       {
        case "2":
          $this -> Resultado();
          break;
        case "3":
          $this -> Datos();
          break;
        case "4":
          $this -> Actualizar();
          break;
        case "5":
          $this -> ListarDespac();
          break;
        case "6":
          $this -> formularios();
          break;
        case "7":
          $this -> getCiudad();
          break;
        case 'exportExcel':
          $this ->exportExcel();
          break;
       }
     }
 }

 function Buscar()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

   $formulario = new Formulario ("index.php","post","BUSCAR TRANSPORTADORAS","form_list");
   $formulario -> radio("NIT","fil",1,0,1);
   $formulario -> radio("Nombre","fil",2,0,1);
   $formulario -> radio("Activas","fil",3,0,0);
   $formulario -> texto ("","text","tercer",1,50,255,"","");
   $formulario -> radio("Inactivas","fil",4,0,1);
   $formulario -> radio("Pendientes","fil",5,0,1);
   $formulario -> radio("Todas","fil",0,1,1);

   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",2,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
   $formulario -> botoni("Buscar","form_list.submit()",0);
   $formulario -> cerrar();
 }

 function Resultado()
 {
   $objciud = new Despachos($_REQUEST[cod_servic],$_REQUEST[opcion],$this -> aplica,$this -> conexion);
   
   $transp = getTranspPerfil($this->conexion,$_SESSION[datos_usuario][cod_perfil]);
   $filtransp = "";
   if(!empty($transp)){
     $filtransp = " AND a.cod_tercer = '".$transp['cod_tercer']."' ";
   }
   $query = "SELECT a.cod_tercer,a.nom_tercer,a.num_telef1,a.cod_ciudad,a.dir_domici
              FROM ".BASE_DATOS.".tab_tercer_tercer a,
                   ".BASE_DATOS.".tab_tercer_activi b
             WHERE a.cod_tercer = b.cod_tercer AND
                   b.cod_activi = ".COD_FILTRO_EMPTRA."
             ".$filtransp."
             ";

   if($_REQUEST[fil] == 1)
    $query .= " AND a.cod_tercer = '".$_REQUEST[tercer]."'";
   else if($_REQUEST[fil] == 2)
    $query .= " AND a.abr_tercer LIKE '%".$_REQUEST[tercer]."%'";
   else if($_REQUEST[fil] == 3)
    $query .= " AND a.cod_estado = ".COD_ESTADO_ACTIVO."";
   else if($_REQUEST[fil] == 4)
    $query .= " AND a.cod_estado = ".COD_ESTADO_INACTI."";
   else if($_REQUEST[fil] == 5)
    $query .= " AND a.cod_estado = ".COD_ESTADO_PENDIE."";

   $query .= " GROUP BY 1 ORDER BY 2";

   $consec = new Consulta($query, $this -> conexion);
   $matriz = $consec -> ret_matriz();

   $formulario = new Formulario ("index.php","post","LISTADO DE TRANSPORTADORAS","form_item");
   $formulario -> linea("Se Encontro un Total de ".sizeof($matriz)." Transportadora(s).",0,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("NIT",0,"t");
   $formulario -> linea("Nombre",0,"t");
   $formulario -> linea("Tel&eacute;fono",0,"t");
   $formulario -> linea("Ciudad",0,"t");
   $formulario -> linea("Direcci&oacute;n",1,"t");

   for($i = 0; $i < sizeof($matriz); $i++)
   {
    $matriz[$i][0]= "<a href=\"index.php?cod_servic=$_REQUEST[cod_servic]&window=central&tercer=".$matriz[$i][0]."&opcion=3 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";
    $ciudad_a = $objciud -> getSeleccCiudad($matriz[$i][3]);

    $formulario -> linea($matriz[$i][0],0,"i");
    $formulario -> linea($matriz[$i][1],0,"i");
    $formulario -> linea($matriz[$i][2],0,"i");
    $formulario -> linea($ciudad_a[0][1],0,"i");
    $formulario -> linea($matriz[$i][4],1,"i");
   }

   $formulario -> nueva_tabla();
   $formulario -> botoni("Volver","javascript:history.go(-1)",0);

   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
   $formulario -> cerrar();
 }

 function Datos()
 {

  $inicio[0][0] = 0;
  $inicio[0][1] = "-";

  $remite[0][0] = 1;
  $remite[0][1] = "Remitente";
  $destin[0][0] = 2;
  $destin[0][1] = "Destinatario";

  $opcird = array_merge($inicio,$remite,$destin);

  $objciud = new Despachos($_REQUEST[cod_servic],$_REQUEST[opcion],$this -> aplica,$this -> conexion);

  $query = "SELECT a.cod_tercer,a.nom_tercer,a.abr_tercer,a.cod_ciudad,a.dir_domici,
                   a.num_telef1,a.num_telef2,a.num_telmov,a.num_faxxxx,d.nom_activi,e.nom_repleg,
                   e.cod_minins,e.num_resolu,e.fec_resolu,f.nom_terreg,e.num_region,e.ran_iniman,ran_finman,
                   e.ind_gracon,e.ind_ceriso,e.fec_ceriso,e.ind_cerbas,e.fec_cerbas,e.otr_certif,
                   e.ind_cobnal,e.ind_cobint,e.nro_habnal,e.fec_resnal
            FROM ".BASE_DATOS.".tab_tercer_tercer a,
                 ".BASE_DATOS.".tab_tercer_activi c,
            	   ".BASE_DATOS.".tab_genera_activi d,
                 ".BASE_DATOS.".tab_tercer_emptra e,
            	   ".BASE_DATOS.".tab_genera_terreg f
           WHERE a.cod_tercer = e.cod_tercer AND
                 a.cod_tercer = c.cod_tercer AND
                 c.cod_activi = d.cod_activi AND
                 a.cod_terreg = f.cod_terreg AND
                 a.cod_tercer = '".$_REQUEST["tercer"]."'";

  $consec = new Consulta($query, $this -> conexion);
  $matriz = $consec -> ret_matriz();

  echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/config.js?".rand(200,10000)."\"></script>\n";
  $formulario = new Formulario ("index.php","post","REMITENTES/DESTINATARIOS","form_item");

  $formulario -> linea("Informaci&oacute; Principal",0,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("Nit o CC",0,"t");
   $formulario -> linea($matriz[0][0],0,"i");
   $formulario -> linea("Nombre",0,"t");
   $formulario -> linea($matriz[0][1],1,"i");
   $formulario -> linea("Abreviatura",0,"t");
   $formulario -> linea($matriz[0][2],0,"i");
   $formulario -> linea("C&oacute;digo de Empresa",0,"t");
   $formulario -> linea($matriz[0][11],1,"i");
   $formulario -> linea("Direcci&oacute;n",0,"t");
   $formulario -> linea($matriz[0][4],0,"i");
   $formulario -> linea("Tel&eacute;fono",0,"t");
   $formulario -> linea($matriz[0][5],1,"i");
   $formulario -> linea("R&eacute;gimen",0,"t");
   $formulario -> linea($matriz[0][14],0,"i");

   $formulario -> nueva_tabla();
   $formulario -> linea("Actividades",1,"t2");

   $formulario -> nueva_tabla();
   for($i=0;$i<sizeof($matriz);$i++)
   {
    $formulario -> linea($matriz[$i][9],1,"i");
   }

   $formulario -> linea("Remitentes y Destinatarios Relacionados",1,"t2");

   $mHtml = new FormLib(2);

    # incluye JS
    $mHtml->SetJs("functions");
    $mHtml->SetJs("new_ajax"); 
    $mHtml->SetJs("dinamic_list"); 
    $mHtml->SetCss("dinamic_list");
    $mHtml->CloseTable("tr");
    # incluye Css
    $mHtml->SetCssJq("jquery");
    $mHtml->Body(array("menubar" => "no"));

    
    #creo el acordeon para el filtro
   	#<DIV fitro>
   		#abre formulario
       	$mHtml->Form(array("action" => "index.php",
            "method" => "post",
            "name" => "form_search",
            "header" => "REMITENTES/DESTINATARIOS",
            "enctype" => "multipart/form-data"));
	        $mHtml->Row("td");
        		$mHtml->OpenDiv("id:sec2");
        			$mHtml->OpenDiv("id:form3; class:contentAccordionForm");
        				 $mSql = "SELECT 	a.cod_remdes,
        				 					if(a.ind_remdes = 1, 'Remitente', 'Destinatario') AS ind_estrem, 
        				 					a.num_remdes,
        				 					a.nom_remdes,
        				 					a.cod_ciudad, 
                          b.nom_ciudad,
        				 					a.dir_remdes, 
        				 					a.cod_latitu, 
        				 					a.cod_longit,
                          a.dir_emailx,
        				 					if(a.ind_estado = 1, 'Activa', 'Inactiva') AS ind_estado
			  		        FROM 	".BASE_DATOS.".tab_genera_remdes a
              INNER JOIN  ".BASE_DATOS.".tab_genera_ciudad b
                      ON  a.cod_ciudad = b.cod_ciudad
			  		       WHERE 	a.cod_transp = '".$_REQUEST['tercer']."'";

				                         
					      $_SESSION["queryXLS"] = $mSql;

					      if(!class_exists(DinamicList)) {
					      	include_once("../".DIR_APLICA_CENTRAL."/lib/general/dinamic_list.inc");									  	  	
					  	  }
					  	  $list = new DinamicList( $this->conexion, $mSql, "1 " , "no", 'ASC');
					      
					      $list->SetClose('no');
					      $list->SetCreate("Crear Remitente/Destinatario", "onclick:crearEditarRemdes(6, null)");
					      $list->SetExcel("Excel", "onclick:exportExcel('opcion=exportExcel')");
					      $list->SetHeader("Código [Editar]", "field:a.cod_remdes; type:link; onclick:crearEditarRemdes(6,this); width:1%; ");
        				$list->SetHeader("Tipo", "field:a.ind_remdes; width:1%;", [0 => ['',''], ['1','Remitente'],['2', 'Destinatario']]);
					      $list->SetHeader("Documento", "field:a.num_remdes; width:1%");
					      $list->SetHeader("Nombre", "field:a.nom_remdes; width:1%");
					      $list->SetHeader("Ciudad (Código)", "field:a.cod_ciudad; width:1%");
                $list->SetHeader("Ciudad", "field:b.nom_ciudad; width:1%");
					      $list->SetHeader("Dirección", "field:a.dir_remdes; width:1%");
					      $list->SetHeader("Latitud", "field:a.cod_latitu" );
					      $list->SetHeader("Longitud", "field:a.cod_longit; width:1%");
                $list->SetHeader("Email", "field:a.dir_emailx; width:1%");
					      $list->SetHeader("Estado", "field:a.ind_estado; width:1%;", [0 => ['',''], ['1','Activa'], ['2','Inactiva']]);
					      $list->SetHidden("cod_transp", "0" );
					      $list->SetHidden("abr_tercer", "1" );
					      $list->Display($this->conexion);
					      $_SESSION["DINAMIC_LIST"] = $list;
					      $Html = $list -> GetHtml();
					      $mHtml->SetBody($Html);
	        			$mHtml->CloseDiv();
	        		$mHtml->CloseDiv();		     
			#$mHtml->CloseRow("td");
		#fin de acordeon con la lista de las tranportadoras
	#</div>
				 $mHtml->Hidden(array( "name" => "standa", "id" => "standaID", 'value'=>DIR_APLICA_CENTRAL));
				 $mHtml->Hidden(array( "name" => "window", "id" => "windowID", 'value'=>'central'));
				 $mHtml->Hidden(array( "name" => "cod_servic", "id" => "cod_servicID", 'value'=>$_REQUEST['cod_servic']));
				 $mHtml->Hidden(array( "name" => "opcion", "id" => "opcionID", 'value'=>''));
				 $mHtml->Hidden(array( "name" => "transp[cod_tercer]", "id" => "cod_tercerID", 'value'=>$_REQUEST['tercer']));
				 $mHtml->Hidden(array( "name" => "cod_remdes", "id" => "cod_remdesID", 'value'=>''));
		 	
	 # Cierra formulario
    	$mHtml->CloseForm();
    # Cierra Body
    $mHtml->CloseBody();

    # Muestra Html
    echo $mHtml->MakeHtml();

    /*$formulario -> nueva_tabla();
    $formulario -> botoni("Otro","form_item.submit()",1);

    $formulario -> nueva_tabla();
    $formulario -> botoni("Aceptar","aceptar_remdes()",0);*/
    $formulario -> botoni("Volver","javascript:history.go(-2)",1);

    $formulario -> nueva_tabla();
    $formulario -> oculto("tercer",$_REQUEST[tercer],0);
    $formulario -> oculto("maximo",$_REQUEST[maximo],0);
    $formulario -> oculto("opcion",$_REQUEST[opcion],0);
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
    $formulario -> oculto("desurb",'0',0);
    $formulario -> cerrar();
  }

 function Actualizar()
 {
  $datos_usuario = $this -> usuario -> retornar();

  $query = "SELECT  a.cod_remdes
              FROM  ".BASE_DATOS.".tab_genera_remdes a
             WHERE  a.cod_remdes = '".$_REQUEST['cod_remdes']."' AND
                    a.ind_remdes = '".$_REQUEST['ind_remdes']."'
             ";
  
  $consulta = new Consulta($query, $this -> conexion);
  $remdest = $consulta -> ret_matriz();

  if(count($remdest) > 0){
    $query = "UPDATE ".BASE_DATOS.".tab_genera_remdes
           SET num_remdes = '".$_REQUEST['num_remdes']."',
               nom_remdes = '".strtoupper($_REQUEST['nom_remdes'])."',
               dir_remdes = '".strtoupper($_REQUEST['dir_remdes'])."',
               cod_ciudad = '".$_REQUEST['cod_ciudad']."',
               cod_latitu = '".$_REQUEST['cod_latitu']."',
               cod_longit = '".$_REQUEST['cod_longit']."',
               dir_emailx = '".$_REQUEST['dir_emailx']."',
               ind_remdes = '".$_REQUEST['ind_remdes']."',
               ind_estado = '".$_REQUEST['ind_estado']."',
               hor_apertu = '".$_REQUEST['hor_apertu']."',
               hor_cierre = '".$_REQUEST['hor_cierre']."',
               usr_modifi = '".$datos_usuario["cod_usuari"]."',
               fec_modifi = NOW()
         WHERE cod_remdes = ".$_REQUEST['cod_remdes']." AND 
               ind_remdes = '".$_REQUEST['ind_remdes']."'
       ";
      $mensaj_adici = "Se Actualizo la Informaci&oacute;n de la Transportadora de Forma Exitosa.";
  }else{
    $query = "INSERT IGNORE ".BASE_DATOS.".tab_genera_remdes
                          (cod_remdes,num_remdes,nom_remdes,
                          dir_remdes,dir_emailx,cod_ciudad,
                          cod_latitu,cod_longit,cod_transp,
                          ind_remdes,ind_estado,hor_apertu,
                          hor_cierre,usr_creaci,fec_creaci)
                 VALUES   (".$_REQUEST['cod_remdes'].",'".$_REQUEST['num_remdes']."','".strtoupper($_REQUEST['nom_remdes'])."',
                          '".strtoupper($_REQUEST['dir_remdes'])."','".strtolower($_REQUEST['dir_emailx'])."',".$_REQUEST['cod_ciudad'].",
                          '".$_REQUEST['cod_latitu']."','".$_REQUEST['cod_longit']."','".$_REQUEST['tercer']."',
                          '".$_REQUEST['ind_remdes']."','".$_REQUEST['ind_estado']."','".$_REQUEST['hor_apertu']."',
                          '".$_REQUEST['hor_cierre']."','".$datos_usuario["cod_usuari"]."',NOW())
         ";
      $mensaj_adici = "Se Registro la Informaci&oacute;n de la Transportadora de Forma Exitosa.";
  }

  $consulta = new Consulta($query, $this -> conexion,"R");

   $link = "<b><a href=\"index.php?cod_servic=".$this -> servic."&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">Buscar Otro Remitente/Destinatario</a></b>";

   $mensaje = $mensaj_adici."<br>".$link;
   $mens = new mensajes();
   $mens -> correcto("REMITENTES/DESTINATARIOS",$mensaje);
  
 }

 function ListarDespac()
 {
  $query = "SELECT a.cod_remdes,a.num_remdes,a.nom_remdes,a.obs_adicio
  		      FROM ".BASE_DATOS.".tab_genera_remdes a
  		     WHERE a.ind_remdes = '".$_REQUEST[tipoxx]."' AND
  		   		   a.ind_estado = '".COD_ESTADO_ACTIVO."' AND
  		   		   a.cod_transp = '".$_REQUEST[transport]."'
  		   ";

  $consulta = new Consulta($query, $this -> conexion);
  $remdest = $consulta -> ret_matriz();

  if($_REQUEST[tipoxx] == 1)
   $nomopcio = "Remitente";
  else if($_REQUEST[tipoxx] == 2)
   $nomopcio = "Destinatario";

  $formulario = new Formulario ("index.php","post","Listado de ".$nomopcio."s","form_item");

  if($remdest)
  {
   $formulario -> linea ("Se Encontro un Total de ".sizeof($remdest)." ".$nomopcio."(s)",1,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("Documento/C&oacute;digo",0,"t");
   $formulario -> linea("Nombre",0,"t");
   $formulario -> linea("Observaciones",1,"t");

   for($i = 0; $i < sizeof($remdest); $i++)
   {
    $remdest[$i][1] = "<a href=# onClick=\"opener.document.forms[0].cod".$_REQUEST[indice].$_REQUEST[codigo].".value='".$remdest[$i][0]."';opener.document.forms[0].doc".$_REQUEST[indice].$_REQUEST[codigo].".value='".$remdest[$i][1]."';opener.document.forms[0].nom".$_REQUEST[indice].$_REQUEST[codigo].".value='".$remdest[$i][2]."';opener.document.forms[0].obs".$_REQUEST[indice].$_REQUEST[codigo].".value='".$remdest[$i][3]."'; top.close()\">".$remdest[$i][1]."</a>";

   	$formulario -> linea($remdest[$i][1],0,"i");
   	$formulario -> linea($remdest[$i][2],0,"i");
   	$formulario -> linea($remdest[$i][3],1,"i");
   }
  }

  $formulario -> cerrar();
 }

 function formularios()
 {
  $remdest = []; 
  $codigos = explode("_", $_REQUEST['cod_remdes']);
  $_REQUEST['cod_remdes'] = $codigos[0];
  $_REQUEST['ind_remdes'] = $codigos[1];
  $accion = "CREAR"; 
  if(!empty($_REQUEST['cod_remdes'])){
    $query = "SELECT  a.cod_remdes,
                      a.ind_remdes, 
                      a.num_remdes,
                      a.nom_remdes,
                      a.cod_ciudad, 
                      b.nom_ciudad,
                      a.dir_remdes, 
                      a.cod_latitu, 
                      a.cod_longit,
                      a.ind_estado,
                      a.hor_apertu,
                      a.hor_cierre,
                      a.dir_emailx
                FROM  ".BASE_DATOS.".tab_genera_remdes a
          INNER JOIN  ".BASE_DATOS.".tab_genera_ciudad b
                  ON  a.cod_ciudad = b.cod_ciudad
               WHERE  a.cod_transp = '".$_REQUEST['transp']['cod_tercer']."' AND
                      a.cod_remdes = '".$_REQUEST['cod_remdes']."' AND
                      a.ind_remdes = '".$_REQUEST['ind_remdes']."'";

    $consulta = new Consulta($query, $this -> conexion);
    $remdest = $consulta -> ret_matriz(a)[0];
    $accion = "MODIFICAR"; 
  }

  $tipo = array(
                    "0" => array('', '--Seleccione--'), 
                    "1" => array('1', 'Remitente'), 
                    "2" => array('2', 'Destinatario')
                  );

  $estado = array(
                    "0" => array('', '--Seleccione--'), 
                    "1" => array('1', 'Activa'),
                    "2" => array('2', 'Inactiva')
                  );

  //Hora de inicio y fin por defecto en caso de venir vacias en el formulario
    if($remdest['hor_apertu']=='' || $remdest['hor_apertu']==NULL){
      $remdest['hor_apertu'] = "07:00:00";
    }

    if($remdest['hor_cierre']=='' || $remdest['hor_cierre']==NULL){
      $remdest['hor_cierre'] = "19:30:00";
    }



  # Abre Form
  $mForm = new FormLib(2);
    $mForm->SetJs("jquery17");
    $mForm->SetJs("jquery"); 
    $mForm->SetJs("functions");
    $mForm->SetJs("validator"); 
    $mForm->SetCssJq("jquery");
    $mForm->CloseTable("tr");
    # incluye Css
    $mForm->Row("td");
        $mForm->Form(array("action" => "index.php",
            "method" => "post",
            "name" => "frm_solSoat",
            "enctype" => "multipart/form-data"));             
        
        $mForm -> Table("tr");
            $mForm -> Line( $accion." REMITENTE/DESTINATARIO", "t2", 0, 0, "center");
        $mForm -> CloseTable("tr");
        
        $mForm -> Table( "tr" );
          $mForm -> Row( "td" );
            $mForm -> OpenDiv( "id:section100" );
        
              $mForm -> Table( "tr" ); 

                  $mForm ->  Label( "*Tipo:", "for:ind_remdes; width:25%;" );
                  $mForm ->  Select2 ($tipo,  array("name" => "ind_remdes", "validate" => "select", "obl"=>"1", "id" => "ind_remdesID", "width" => "25%", "key"=> $remdest['ind_remdes']) );
                  $mForm ->  Label( "*Codigo:", "for:cod_remdesID; width:25%;");
                  $mForm ->  Input( "name:cod_remdes; maxlength:60; size:4; validate:dir; obl:1; end:yes; width:25%; value:".$remdest['cod_remdes']);
                  
                  $mForm ->  Label( "*Documento:", "for:num_remdes; width:25%;" );
                  $mForm ->  Input( "name:num_remdes; maxlength:60; size:60; width:25%; validate:dir; obl:1; value:".$remdest['num_remdes']);
                  $mForm ->  Label( "*Nombre:", "for:nom_remdes; width:25%;" );
                  $mForm ->  Input( "name:nom_remdes; maxlength:60; size:60; end:yes; validate:dir; obl:1; width:25%; value:".$remdest['nom_remdes']);

                  $mForm ->  Label( "*Ciudad:", "for:cod_ciudad; width:25%;" );
                  $mForm ->  Hidden( "name:cod_ciudad; value:".$remdest['cod_ciudad']);
                  $mForm ->  Input( "name:nom_ciudad; maxlength:60; size:60; width:25%; validate:dir; obl:1; value:".$remdest['nom_ciudad']);
                  $mForm ->  Label( "*Dirección:", "for:dir_remdes; width:25%;" );
                  $mForm ->  Input( "name:dir_remdes; maxlength:60; size:60; end:yes; validate:dir; obl:1; width:25%; value:".$remdest['dir_remdes']);

                  $mForm ->  Label( "*Latitud:", "for:cod_latitu; width:25%;" );
                  $mForm ->  Input( "name:cod_latitu; maxlength:60; size:60; width:25%; validate:dir; obl:1; value:".$remdest['cod_latitu']);
                  $mForm ->  Label( "*Longitud:", "for:cod_longit; width:25%;" );
                  $mForm ->  Input( "name:cod_longit; maxlength:60; size:60; end:yes; validate:dir; obl:1; width:25%; value:".$remdest['cod_longit']);

                  $mForm ->  Label( "*Horario Apertura:", "for:hor_apertu; width:25%;" );
                  $mForm ->  SetBody('<td id="hor_apertuIDTD" class="celda_info" align="left" width="25%" height="">
                                        <input type="time" class="campo_texto" onfocus="this.className=\'campo_texto_on\';" onblur="this.className=\'campo_texto\';" name="hor_apertu" id="hor_apertuID" maxlength="60" size="60" end="yes" validate="hora" obl="1" value="'.$remdest['hor_apertu'].'"/>
                                      </td>');
                  $mForm ->  Label( "*Horario Cierre:", "for:hor_cierre; width:25%;" );
                  $mForm ->  SetBody('<td id="hor_cierreIDTD" class="celda_info" align="left" width="25%" height="">
                                        <input type="time" class="campo_texto" onfocus="this.className=\'campo_texto_on\';" onblur="this.className=\'campo_texto\';" name="hor_cierre" id="hor_cierreID" maxlength="60" size="60" end="yes" validate="hora" obl="1" value="'.$remdest['hor_cierre'].'"/>
                                      </td></tr>');
                  $mForm ->  Label( "*Email:", "for:dir_emailx; width:25%;" );
                  $mForm ->  Input( "name:dir_emailx; maxlength:60; size:60; validate:email; obl:1; width:25%; value:".$remdest['dir_emailx']);

                  $mForm ->  Label( "*Estado:", "for:ind_estado; width:25%;" );
                  $mForm ->  Select2 ($estado,  array("name" => "ind_estado", "validate" => "select", "obl"=>"1", "id" => "ind_estadoID", "width" => "25%", "key"=> $remdest['ind_estado']) );
                  
              $mForm -> CloseTable( "tr" ); 
              
              $mForm -> Table( "tr" );
                  $mForm -> StyleButton( "name:but_action; align:center; value:Enviar; onclick:aceptar_remdes();" );
                  $mForm -> StyleButton( "name:but_action; align:center; value:Volver; onclick:history.go(-1); end:yes" );
              $mForm -> CloseTable( "tr" );

            $mForm -> CloseDiv();
          $mForm -> CloseRow( "td" );
        $mForm -> CloseTable("tr");

        $mForm -> Hidden( "name:standar; value:".DIR_APLICA_CENTRAL );
        $mForm -> Hidden( "name:cod_servic; value:" . $_REQUEST['cod_servic'] );
        $mForm -> Hidden( "name:tercer; value:" . $_REQUEST['transp']['cod_tercer'] );
        $mForm -> Hidden( "name:window; value:central" );
        $mForm -> Hidden( "name:opcion; value:4" );
        
        $mForm -> CloseForm();  
      $mForm->CloseRow("td");
      $mForm->SetJs("config"); 
      echo $mForm->MakeHtml();
 }

  function getCiudad()
  {
    $query = "SELECT  a.cod_ciudad, 
                      CONCAT(b.abr_depart,' - ',a.nom_ciudad) AS nom_ciudad
                FROM  ".BASE_DATOS.".tab_genera_ciudad a
          INNER JOIN  ".BASE_DATOS.".tab_genera_depart b
                  ON  a.cod_depart = b.cod_depart
               WHERE  a.ind_estado = 1 AND
                      a.nom_ciudad like '%".$_REQUEST["term"]."%' OR
                      b.abr_depart like '%".$_REQUEST["term"]."%'";

    $consulta = new Consulta($query, $this -> conexion);
    $remdest = $consulta -> ret_matriz(a);

    if( $_REQUEST["term"] )
    {
      $mCiudad = array();
      for($i=0; $i<sizeof( $remdest ); $i++){
        $mTxt = $remdest[$i][cod_ciudad]." - ".utf8_decode($remdest[$i]["nom_ciudad"]);
        $mCiudad[] = array('value' => utf8_decode($remdest[$i]["nom_ciudad"]), 'label' => $mTxt, 'id' => $remdest[$i]["cod_ciudad"], 'ciu' => $remdest[$i]["nom_ciudad"] );
      }
      echo json_encode( $mCiudad );
    }else{
      return $remdest;
    }  
  }


  /*! \fn: exportExcel
   *  \brief: Exporta la tabla a excel
   *  \author: Ing. Luis Manrique
   *  \date: 07/11/2019
   *  \date modified: dd/mm/aaaa
   *  \param:     
   *  \return: Matriz
   */

    private function exportExcel(){

	    $mSql = $_SESSION["queryXLS"];    
	    $mConsult = new Consulta($mSql, $this->conexion );
	    $mRemdes = $mConsult -> ret_matrix('a');

	    $table = "";
	  
	    $table .='<table id="exportData"><tr>';
	      $table .='<th>Código</th>';
	      $table .='<th>Tipo</th>';
	      $table .='<th>Documento</th>';
	      $table .='<th>Nombre</th>';
	      $table .='<th>Ciudad (Código)</th>';
	      $table .='<th>Ciudad</th>';
	      $table .='<th>Dirección</th>';
	      $table .='<th>Latitud</th>';
	      $table .='<th>Longitud</th>';
	      $table .='<th>Email</th>';
	      $table .='<th>Estado</th>';
	    $table .='</tr>';
	    foreach ($mRemdes as $key => $value) {
	      $table .='<tr>';
	        $table .="<td>".$value['cod_remdes']."</td>";
	        $table .="<td>".$value['ind_estrem']."</td>";
	        $table .="<td>".$value['num_remdes']."</td>";
	        $table .="<td>".$value['nom_remdes']."</td>"; 
	        $table .="<td>".$value['cod_ciudad']."</td>";
	        $table .="<td>".$value['nom_ciudad']."</td>"; 
	        $table .="<td>".$value['dir_remdes']."</td>"; 
	        $table .="<td>".$value['cod_latitu']."</td>"; 
	        $table .="<td>".$value['cod_longit']."</td>"; 
	        $table .="<td>".$value['dir_emailx']."</td>"; 
	        $table .="<td>".$value['ind_estado']."</td>"; 
	      $table .='</tr>';
	    }
	    
	    $table .= "</table>";

	    $archivo = "Remitentes/Destinatarios_".date("Y_m_d").".xls";

	    header('Content-Type: application/octetstream');
	    header('Expires: 0');
	    header('Content-Disposition: attachment; filename="'.$archivo.'"');
	    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	    header('Pragma: public');
	    echo $table;
    }

}

if($_REQUEST["Ajax"] == 'on' ){
  $proceso = new Proc_tercer();
}else{
  $proceso = new Proc_tercer($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);  
}
?>
