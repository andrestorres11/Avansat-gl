<?php

  class Proc_noveda
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
      if(!isset($_REQUEST["opcion"]))
        $this -> listar();
      else
      {
        switch($_REQUEST["opcion"])
        {
          case "1":
            $this -> Formulario();
          break;

          case "2":
            $this -> Insertar();
          break;

          case "3":
            $this -> eliminarNoveda();
          break;
        }//FIN SWITCH
      }// FIN ELSE GLOBALS OPCION
    }//FIN FUNCION PRINCIPAL

    function Formulario()
    {
      echo $this -> GridStyle();
      #preparacion de arrays
      $mEtapas = Proc_noveda::getEtapa();
      $mperfil = $this -> getPerfiles();
      $mCompor = array(
                       'nov_especi' => 'Novedad Especial',
                       'ind_manala' => 'Mantiene Alarma',
                       'ind_fuepla' => 'Fuera de Plataforma',
                       'indtiemp'   => 'Solicita Tiempos',
                       'ind_ealxxx' => 'Visible Esferas',
                       'ind_visibl' => 'Visibilidad (S/N)',
                       'ind_notsup' => 'Notifica Supervisor',
                       'ind_limpio' => 'Limpio'
                       //no estan en el diseño
                       /*'indala'     => 'Genera Alerta',
                       'ind_insveh' => 'Inspección Vehicular'*/
                        );
      $mHtml = new FormLib(2);
       # incluye JS
      $mHtml->SetJs("min");
      $mHtml->SetJs("config");
      $mHtml->SetJs("fecha");
      $mHtml->SetJs("jquery");
      $mHtml->SetJs("functions");
      $mHtml->SetJs("noveda");
      $mHtml->SetJs("new_ajax"); 
      $mHtml->SetJs("dinamic_list");
      $mHtml->SetCss("dinamic_list");
      $mHtml->SetJs("validator");
      $mHtml->SetCssJq("validator");
      # incluye Css
      $mHtml->SetCssJq("jquery");

      #variables ocultas
      $mHtml->Form(array("action" => "index.php", "method" => "post", "name" => "form_list" ));
      if($_REQUEST['accion']==2)
      {
        $datos = $this -> getNoveda($_REQUEST["cod_noveda"]);
        $mHtml->Hidden(array( "name" => "cod_noveda", "id" => "cod_novedaID", 'value'=>$_REQUEST["cod_noveda"]));
      }
      $mHtml->Hidden(array( "name" => "standa", "id" => "standaID", 'value'=>DIR_APLICA_CENTRAL));
      $mHtml->Hidden(array( "name" => "window", "id" => "windowID", 'value'=>'central'));
      $mHtml->Hidden(array( "name" => "cod_servic", "id" => "cod_servicID", 'value'=>$_REQUEST['cod_servic']));
      $mHtml->Hidden(array( "name" => "opcion", "id" => "opcionID", 'value'=>$_REQUEST['opcion']));
      $mHtml->Hidden(array( "name" => "accion", "id" => "accionID", 'value'=>($_REQUEST['accion']==""?"1":$_REQUEST['accion'])));

      # Construye accordion
      $mHtml->Row("td");
        $mHtml->OpenDiv("id:contentID; class:contentAccordion");
        # Accordion1
          $mHtml->OpenDiv("id:DatosBasicosID; class:accordion");
            $mHtml->SetBody("<h1 style='padding:6px'><b>Novedades</b></h1>");
            $mHtml->OpenDiv("id:sec1;");
              $mHtml->OpenDiv("id:form1; class:contentAccordionForm");
                $mHtml->Table("tr");
                  $mHtml->Label( ($_REQUEST["cod_noveda"]?"Actualizar Novedad - ".$_REQUEST["cod_noveda"]:"Registro de Novedad"), array("colspan"=>"12", "align"=>"center", "width"=>"25%", "class"=>"CellHead") );
                  $mHtml->Row();
                    $mHtml->Label( "Nombre de la novedad", array("colspan"=>"7", "align"=>"right", "width"=>"25%", "class"=>"cellInfo2") );
                    $mHtml->Input( array("name"=>"nom", "id"=>"nom", "width"=>"50%", "value"=>"" , "class"=>"cellInfo2", "colspan"=>"7", "value" =>($_REQUEST['accion']==2?$datos["datos"][0]["nom_noveda"]:"")) );
                  $mHtml->CloseRow();
                $mHtml->CloseTable("tr");
                $mHtml->Table("tr");  
                    $mHtml->Label( "Caracteristicas de la Novedad", array("colspan"=>((sizeof($mEtapas)*2)+2), "align"=>"center", "width"=>"25%", "class"=>"CellHead") );
                  $mHtml->CloseRow();
                $mHtml->CloseTable("tr");
                $mHtml->Table("tr");
                    $mHtml->Label( "Estapas de seguimiento", array( "colspan"=>((sizeof($mEtapas)*2)+2), "align"=>"left", "width"=>"25%", "class"=>"cellInfo2", "end" => "true") );
                    foreach ($mEtapas as $NomEtapa => $valorEtapa) 
                    {
                      $mHtml->Radio(array("colspan"=>"1", "value"=>$valorEtapa[0], "name" => "cod_etapax", "id" => "cod_etapax", "class"=>"cellInfo2", "width" => "8%", "checked"=>($datos["datos"][0]["cod_etapax"] == $valorEtapa[0] ?"checked":($valorEtapa[0]==3 && !$_REQUEST['accion']?"checked":null))));
                      $mHtml->Label( $valorEtapa[1], array("colspan"=>"1", "align"=>"right", "width"=>"8%", "class"=>"cellInfo2") );
                    }
                  $mHtml->CloseRow();
                $mHtml->CloseTable("tr");
                $mHtml->Table("tr");
                    $mHtml->Label( "Comportamiento", array( "align"=>"left", "width"=>"25%", "class"=>"cellInfo2","end" => "true") );
                    $saltoLn = 0;
                    foreach ($mCompor as $idCompor => $valCompor) 
                    {
                      $saltoLn++;
                      $mHtml->CheckBox(array("name" => $idCompor, "id"=>$idCompor, "width" => "25%", "colspan" => "1", "class"=>"cellInfo2", "align"=>"right", "value"=>"1", "checked"=>($datos["datos"][0][$idCompor] == 1 || $datos["datos"][0][$idCompor] == "S"?"checked":null) ));
                      $mHtml->Label( $valCompor, array("align"=>"right", "width"=>"2%", "class"=>"cellInfo2", "colspan" =>"3" ) );
                      if($saltoLn>=3)
                      {
                        $mHtml->CloseRow();
                        $mHtml->Row();
                        $saltoLn=0;
                      }
                    }
                  $mHtml->CloseRow();
                $mHtml->CloseTable("tr");
                $mHtml->Table("tr");
                    $mHtml->Label( "Homologacion Novedad", array("colspan"=>"6", "align"=>"center", "width"=>"25%", "class"=>"CellHead") );
                    $mHtml->CloseRow();
                    $mHtml->Row();
                      $mHtml->Label( "Operador GPS", array("align"=>"right", "width"=>"2%", "class"=>"cellInfo2", "colspan" =>"1" ) );
                      $mHtml->Select2 (self::getOpgps(),  array("name" => "cod_opegps", "id"=>"cod_opegps", "width" => "25%", "key" => $datos["perfiles"][0]["cod_opegps"]?$datos["perfiles"][0]["cod_opegps"]:null) );
                      $mHtml->Label( "Codigo Evento", array("align"=>"right", "width"=>"2%", "class"=>"cellInfo2", "colspan" =>"1" ) );
                      $mHtml->Input( array("name"=>"cod_evento", "id"=>"cod_evento", "align"=>"right", "class"=>"cellInfo2", "type"=>"numeric", "maxlength"=>"8", "value"=>($datos["perfiles"][0]["cod_evento"]?$datos["perfiles"][0]["cod_evento"]:"")));
                      $mHtml->Label( "Nombre Evento", array("align"=>"right", "width"=>"2%", "class"=>"cellInfo2", "colspan" =>"1" ) );
                      $mHtml->Input( array("name"=>"nom_evento", "id"=>"nom_evento", "align"=>"right", "class"=>"cellInfo2", "value"=>($datos["perfiles"][0]["nom_evento"]?$datos["perfiles"][0]["nom_evento"]:"")));
                $mHtml->CloseTable("tr");
                $mHtml->Table("tr");
                    $mHtml->Label( "Filtro de perfil", array("colspan"=>((sizeof($mEtapas)*2)+2), "align"=>"center", "width"=>"25%", "class"=>"CellHead") );
                  $mHtml->CloseRow();
                  $mHtml->Row();
                    $saltoLn=0;
                    $numReco = 0;
                    foreach ($mperfil as $idPefil => $valPefil) 
                    {
                      $saltoLn++;
                      $mHtml->CheckBox(array("name" => "perfil[".$valPefil[0]."]", "id"=>"perfil[".$valPefil[0]."]", "width" => "25%", "colspan" => "1", "class"=>"cellInfo2", "align"=>"right", "value"=>$valPefil[0], "checked"=>($datos['perfiles'] && $this->getBuscarArr($valPefil[0],"cod_perfil",$datos['perfiles'])==1?"checked":null)));
                      $mHtml->Label( $valPefil[1], array("align"=>"right", "width"=>"2%", "class"=>"cellInfo2", "colspan" =>"3" ) );
                      if($saltoLn>=3)
                      {
                        $mHtml->CloseRow();
                        $mHtml->Row();
                        $saltoLn=0;
                      }
                    }
                  $mHtml->CloseRow();
                $mHtml->CloseTable("tr");
                $mHtml->Table("tr");
                  $mHtml->Button( array("value"=>($_REQUEST['accion']!=2?"Insertar":"Actualizar"), "id"=>"Insertar","name"=>"Insertar", "class"=>"crmButton small save", "align"=>"center", "onclick"=>"ins_tab_noveda()") );
                  $mHtml->Button( array("value"=>"Volver", "id"=>"volver","name"=>"volver", "class"=>"crmButton small save", "align"=>"center", "onclick"=>"VolverNovedad()", "end"=>"1") );
                $mHtml->CloseTable("tr");
              $mHtml->CloseDiv();
            $mHtml->CloseDiv();
          $mHtml->CloseDiv();
        # Fin accordion1    
        $mHtml->CloseDiv();
      $mHtml->CloseRow("td");
        # Cierra formulario
      $mHtml->CloseForm();
      # Cierra Body
      $mHtml->CloseBody();
      $mHtml->CloseForm();
      # Muestra Html
      echo $mHtml->MakeHtml();

    }//FIN FUNCTION CAPTURA
	
	function getPerfiles()
	{
		$query = "SELECT a.cod_perfil, a.nom_perfil
				  FROM " . BASE_DATOS . ".tab_genera_perfil  a
				  ORDER BY 2";

    $consulta = new Consulta($query, $this->conexion);
    return $matriz = $consulta -> ret_matriz( "i" );
	}
  
  function getOperadores()
  {
     $query = "SELECT a.cod_operad, a.nom_operad
				        FROM ".CENTRAL.".tab_genera_opegps  a
               WHERE a.ind_estado = '1'
				  ORDER BY 2";
        $consulta = new Consulta($query, $this->conexion);        
        return array_merge(array(array("--","--")),$consulta -> ret_matriz( "i" ) );
  }

    function Insertar()
    {
      $fec_actual = date("Y-m-d H:i:s");
      //trae el consecutivo de la tabla si es para nuevo dato
      if($_REQUEST["accion"]==1)
      {
        $query = "SELECT Max(cod_noveda) AS maximo
                    FROM ".BASE_DATOS.".tab_genera_noveda
                   WHERE cod_noveda != ".CONS_NOVEDA_CAMALA." AND
                         cod_noveda != ".CONS_NOVEDA_ACAEMP." AND
                         cod_noveda != ".CONS_NOVEDA_ACAFAR." AND
                         cod_noveda != ".CONS_NOVEDA_CAMRUT." AND
                         cod_noveda != ".CONS_NOVEDA_TRNAUT." AND
                         cod_noveda != ".CONS_NOVEDA_CONDES." AND
  					   cod_noveda != ".CONS_NOVEDA_CAMCEC." AND
  					   cod_noveda != ".CONS_NOVEDA_GPSXXX."
  					   ";

        $consec = new Consulta($query, $this -> conexion);
        $ultimo = $consec -> ret_matriz();

        $ultimo_consec = $ultimo[0][0];
        $nuevo_consec = $ultimo_consec+1;
      }
      else if($_REQUEST["accion"] == 2 && $_REQUEST["cod_noveda"])
      {
        $nuevo_consec = $_REQUEST["cod_noveda"]; 
      }

      //valida el indicador de alarma
      if($_REQUEST["indala"] == 1)
        $alarma = 'S';
      else
        $alarma = 'N';

      //valida el indicador de solicitud de tiempos por novedad
      if($_REQUEST["indtiemp"])
        $tiempo = $_REQUEST["indtiemp"];
      else
        $tiempo = 0;

      //valida novedad especial
      if($_REQUEST["nov_especi"])
        $nov_especi = 1;
      else
        $nov_especi = 0;

      //valida Mantiene Alarma
      if($_REQUEST["ind_manala"])
        $ind_manala = 1;
      else
        $ind_manala = 0;
      
      //valida Inspeccion Vehicular
      if($_REQUEST["ind_insveh"])
        $ind_insveh = 1;
      else
        $ind_insveh = 0;

      //valida Visible Esferas
      if($_REQUEST["ind_ealxxx"])
        $ind_ealxxx = 1;
      else
        $ind_ealxxx = 0;


      //valida Visible Limpio
      if($_REQUEST["ind_limpio"])
        $ind_limpio = 1;
      else
        $ind_limpio = 0;

      //valida Fuera de Plataforma
      if($_REQUEST["ind_fuepla"])
      {
        $ind_fuepla = 1;
        $tiempo = 1;
      }
      else
        $ind_fuepla = 0;
        
      //valida Notifica Supervisor
      if($_REQUEST["ind_notsup"])
      {
        $ind_notsup = 1;
        $ind_manala = 1;
      }
      else
        $ind_notsup = 0;   

      $cod_operad = $_REQUEST["cod_opegps"]; 
      $cod_homolo = $_REQUEST["cod_homolo"];   
      $ind_visibl = $_REQUEST["ind_visibl"] == NULL ? '0' : $_REQUEST["ind_visibl"];   

      if($_REQUEST["accion"]==1)
      {
        //query de insercion
        $mQuery = "INSERT INTO ".BASE_DATOS.".tab_genera_noveda 
                  (cod_noveda  , nom_noveda  , obs_preted  , ind_alarma  , 
                   ind_tiempo  , nov_especi  , ind_manala  , ind_notsup  , 
                   ind_agenci  , cod_operad  , cod_homolo  , ind_visibl  , 
                   ind_fuepla  , ind_insveh  , ind_ealxxx  , ind_limpio  ,
                   cod_etapax  , usr_creaci  , fec_creaci ) 
                   VALUES 
                  ('$nuevo_consec','$_REQUEST[nom]',NULL       ,'$alarma'  ,
                   '$tiempo'  , '$nov_especi','$ind_manala', '$ind_notsup', 
                   '0'       ,'$cod_operad','$cod_homolo', '$ind_visibl', 
                   '$ind_fuepla', '$ind_insveh', '$ind_ealxxx', '$ind_limpio' , 
                   '".$_REQUEST[cod_etapax]."', '$_REQUEST[usuario]','$fec_actual') ;
                   ";
        #echo "<pre>"; print_r($mQuery); echo "</pre>";

        $consulta = new Consulta($mQuery, $this -> conexion,"BR");
      }
      else if($_REQUEST["accion"]==2 && $_REQUEST["cod_noveda"])
      {
        $mQuery = "UPDATE ".BASE_DATOS.".tab_genera_noveda SET
                   nom_noveda  = '$_REQUEST[nom]', 
                   obs_preted  = NULL, 
                   ind_alarma  = '$alarma', 
                   ind_tiempo  = '$tiempo', 
                   nov_especi  = '$nov_especi', 
                   ind_manala  = '$ind_manala', 
                   ind_notsup  = '$ind_notsup', 
                   ind_agenci  = 0, 
                   cod_operad  = '$cod_operad', 
                   cod_homolo  = '$cod_homolo', 
                   ind_visibl  = '$ind_visibl', 
                   ind_fuepla  = '$ind_fuepla', 
                   ind_insveh  = '$ind_insveh', 
                   ind_ealxxx  = '$ind_ealxxx', 
                   ind_limpio  = '$ind_limpio',
                   cod_etapax  = '".$_REQUEST[cod_etapax]."', 
                   usr_modifi  = '$_REQUEST[usuario]', 
                   fec_modifi  = '$fec_actual' 
                  WHERE
                    cod_noveda =".$nuevo_consec;
        #echo "<pre>"; print_r($mQuery); echo "</pre>";

        $consulta = new Consulta($mQuery, $this -> conexion,"BR");
      }

      $operad = $_REQUEST["operad"];
      $novedaint = $_REQUEST["novedaint"];

      //Manejo de la Interfaz Aplicaciones SAT
      $interfaz = new Interfaz_SAT(BASE_DATOS,NIT_TRANSPOR,$_REQUEST["usuario"],$this -> conexion);

      for($i = 0; $i < $interfaz -> totalact; $i++)
      {
        if($novedaint[$i] && $operad[$i] == $interfaz -> interfaz[$i]["operad"])
        {
          $resultado_sat = $interfaz -> insHomoloNoveda($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$nuevo_consec,$novedaint[$i]);

          if($resultado_sat["Confirmacion"] == "OK")
            $mensaje_sat .= "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\">La Novedad Se Homologo en la Interfaz  <b>".$interfaz -> interfaz[$i]["nombre"].".</b><br>";
          else
            $mensaje_sat .= "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/advertencia.gif\">Se Presento el Siguiente Error al Insertar la Homologacion : <b>".$resultado_sat["Confirmacion"]."</b><br>";
        }
      }
    if($_REQUEST["accion"]==2 && $_REQUEST["cod_noveda"])
    {
		  $query = "DELETE FROM " . BASE_DATOS . ".tab_perfil_noveda
          WHERE cod_noveda = '$nuevo_consec' ";

      $consulta = new Consulta( $query, $this->conexion, "R" );
    }

		$perfiles = $_POST["perfil"];
		
		if( $perfiles )
    {
  		foreach( $perfiles as $row )
  		{
        $cod_opegps = ($_REQUEST["cod_opegps"]=="--"?null:$_REQUEST["cod_opegps"]);
        $cod_evento = ($_REQUEST["cod_evento"] && $_REQUEST["cod_opegps"]!="--"?$_REQUEST["cod_evento"]:null);
        $nom_evento = ($_REQUEST["nom_evento"] && $_REQUEST["cod_opegps"]!="--"?$_REQUEST["nom_evento"]:null);
  			$insert = "INSERT INTO  " . BASE_DATOS . ".tab_perfil_noveda 
  						(
  							cod_perfil , cod_noveda , cod_opegps , cod_evento , nom_evento
  						)
  						VALUES 
  						(
  							'$row',  '$nuevo_consec', '$cod_opegps', '$cod_evento', '$nom_evento'
  						)";
  			
  			$consulta = new Consulta( $insert, $this -> conexion, "R" );
  		}	
		}
      if($insercion = new Consulta("COMMIT", $this -> conexion))
      {
        $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$_REQUEST["cod_servic"]." \"target=\"centralFrame\">". ($_REQUEST['accion']!=2?"Insertar":"Actualizar") ." Otra Novedad</a></b>";

        $mensaje =  "La Novedad <b>".$_REQUEST["nom"]."</b> Se ". ($_REQUEST['accion']!=2?"Inserto":"Actualizo") ." con Exito".$mensaje_sat.$link_a;
        $mens = new mensajes();
        $mens -> correcto("INSERTAR NOVEDADES",$mensaje);
      }

    }//FIN FUNCTION INSERTAR

    /*! \fn: getEtapa
     *  \brief: Trae las Etapas de un despacho
     *  \author: Ing. Fabian Salinas
     *  \date: 26/06/2015
     *  \date modified: dia/mes/año
     *  \param: 
     *  \return: 
     */
    private function getEtapa()
    {
      $mSql = "SELECT a.cod_queryx 
                 FROM ".BD_STANDA.".tab_genera_filtro a 
                WHERE a.nom_filtro = 'Etapa' ";
      $mConsult = new Consulta($mSql, $this -> conexion);
      $mSql = $mConsult -> ret_arreglo();

      $mConsult = new Consulta($mSql[0], $this -> conexion);
      return $mResult = $mConsult -> ret_matrix('i');
    }

    /*! \fn: listar
    * \brief: Lista las novedades existentes
    * \author: Edward Serrano
    * \date: 31/03/2017
    * \date modified: dia/mes/año
    * \param: paramatro
    * \return valor que retorna
    */

    private function listar()
    {
      $mHtml = new FormLib(2);
       # incluye JS
      $mHtml->SetJs("min");
      $mHtml->SetJs("config");
      $mHtml->SetJs("fecha");
      $mHtml->SetJs("jquery");
      $mHtml->SetJs("functions");
      $mHtml->SetJs("noveda");
      $mHtml->SetJs("new_ajax"); 
      $mHtml->SetJs("dinamic_list");
      $mHtml->SetCss("dinamic_list");
      $mHtml->SetJs("validator");
      $mHtml->SetCssJq("validator");
      # incluye Css
      $mHtml->SetCssJq("jquery");

        #variables ocultas
      
      $mHtml->Hidden(array( "name" => "standa", "id" => "standaID", 'value'=>DIR_APLICA_CENTRAL));
      $mHtml->Hidden(array( "name" => "window", "id" => "windowID", 'value'=>'central'));
      $mHtml->Hidden(array( "name" => "cod_servic", "id" => "cod_servicID", 'value'=>$_REQUEST['cod_servic']));
      $mHtml->Hidden(array( "name" => "opcion", "id" => "opcionID", 'value'=>$_REQUEST['opcion']));

      # Construye accordion
      $mHtml->Row("td");
        $mHtml->OpenDiv("id:contentID; class:contentAccordion");
        # Accordion1
          $mHtml->OpenDiv("id:DatosBasicosID; class:accordion");
            $mHtml->SetBody("<h1 style='padding:6px'><b>Novedades</b></h1>");
            $mHtml->OpenDiv("id:sec1;");
              $mHtml->OpenDiv("id:form1; class:contentAccordionForm");
                $mHtml->SetBody($this -> getDinamiList());
              $mHtml->CloseDiv();
            $mHtml->CloseDiv();
          $mHtml->CloseDiv();
        # Fin accordion1    
        $mHtml->CloseDiv();
      $mHtml->CloseRow("td");
        # Cierra formulario
      $mHtml->CloseForm();
      # Cierra Body
      $mHtml->CloseBody();

      # Muestra Html
      echo $mHtml->MakeHtml();
    }

    /*! \fn: getDinamiList 
    * \brief: trae los datos en dinami list
    * \author: Edward Serrano
    * \date: 31/03/2017
    * \date modified: dia/mes/año
    * \param: paramatro
    * \return valor que retorna
    */
    private function getDinamiList()
    {
      $mSql = "SELECT  a.cod_noveda AS cod_noveda,
                            UPPER(a.nom_noveda) AS nom_noveda,
                            c.nom_etapax AS nom_etapax, 
                            if(a.nov_especi = '1', 'SI', 'NO') AS nov_especi, 
                            if(a.ind_manala = '1', 'SI', 'NO') AS ind_manala, 
                            if(a.ind_fuepla = '1', 'SI', 'NO') AS ind_fuepla, 
                            if(a.ind_tiempo = '1', 'SI', 'NO') AS ind_tiempo, 
                            IF(a.ind_ealxxx = '1', 'SI', 'NO') AS ind_ealxxx, 
                            IF(a.ind_visibl = '1', 'SI', 'NO') AS ind_visibl,
                            if(a.ind_notsup = '1', 'SI', 'NO') AS ind_notsup, 
                            IF(a.ind_limpio = '1', 'SI', 'NO') AS ind_limpio,
                            IF(b.nom_operad IS NULL, '---',b.nom_operad) AS nom_operad
                  FROM ".BASE_DATOS.".tab_genera_noveda a 
            INNER JOIN ".BASE_DATOS.".tab_genera_etapax c 
                    ON a.cod_etapax = c.cod_etapax 
            LEFT JOIN ".CENTRAL.".tab_genera_opegps b 
                    ON a.cod_operad = b.cod_operad
                  WHERE a.ind_estado=1";
                                         
      $_SESSION["queryXLS"] = $mSql;

      if(!class_exists(DinamicList)) {
        include_once("../".DIR_APLICA_CENTRAL."/lib/general/dinamic_list.inc");                         
      }
      $list = new DinamicList($this -> conexion, $mSql, "2" , "no", 'ASC');
      $list->SetClose('no');
      $list->SetCreate("Nueva Novedad", "onclick:getFormNoveda(1)");
      $list->SetHeader("Codigo",              "field:cod_noveda; width:1%;  ");
      $list->SetHeader("Descripcionr",        "field:nom_noveda; width:1%");
      $list->SetHeader("Etapa",               "field:nom_etapax; width:1%");
      $list->SetHeader("Novedad Especial",    "field:nov_especi" );
      $list->SetHeader("Mantiene Alerta",     "field:ind_manala" );
      $list->SetHeader("Fuera de Plataforma", "field:ind_fuepla" );
      $list->SetHeader("Solicitud Tiempo",    "field:ind_tiempo" );
      $list->SetHeader("Visible Esferas",     "field:ind_ealxxx" );
      $list->SetHeader("Visibilidad (N/S)",   "field:ind_visibl" );
      $list->SetHeader("Notifica Supervisor", "field:ind_notsup" );
      $list->SetHeader("Limpio",              "field:ind_limpio" );
      $list->SetHeader("Operador GPS",        "field:nom_operad" );
      $list->SetOption("Opciones","field:cod_noveda; onclickCopy:eliminarNove( this ); onclikEdit:editarNove( this );" );
      $list->SetHidden("cod_noveda", "cod_noveda");
      $list->Display($this -> conexion);

      $_SESSION["DINAMIC_LIST"] = $list;

      return $list -> GetHtml();

    }

    /*! \fn: getNoveda 
    * \brief: retorna la informacion relacionada a la novedad
    * \author: Edward Serrano
    * \date: 03/04/2017
    * \date modified: dia/mes/año
    * \param: paramatro
    * \return valor que retorna
    */
    function getNoveda($cod_noveda)
    {
      try
      {
        $mSql = "SELECT a.nom_noveda AS nom_noveda, a.cod_etapax AS cod_etapax, a.obs_preted AS obs_preted,    
                        a.ind_alarma AS ind_alarma, a.ind_tiempo AS ind_tiempo, a.nov_especi AS nov_especi,   
                        a.ind_manala AS ind_manala, a.ind_fuepla AS ind_fuepla, a.ind_insveh AS ind_insveh,      
                        a.ind_agenci AS ind_agenci, a.cod_operad AS cod_operad, a.cod_homolo AS cod_homolo,   
                        a.ind_visibl AS ind_visibl, a.ind_limpio AS ind_limpio 
                 FROM ".BASE_DATOS.".tab_genera_noveda a 
                WHERE a.cod_noveda =".$cod_noveda;
        $mConsult = new Consulta($mSql, $this -> conexion);

        $mSql2 = "SELECT b.cod_perfil AS cod_perfil, b.cod_noveda AS cod_noveda, b.cod_opegps AS cod_opegps,
                         b.cod_evento AS cod_evento, b.nom_evento AS nom_evento
                 FROM ".BASE_DATOS.".tab_perfil_noveda b 
                WHERE b.cod_noveda =".$cod_noveda;

        $mConsult2 = new Consulta($mSql2, $this -> conexion);
        return array('datos' => $mConsult -> ret_matrix('a'), 'perfiles' => $mConsult2 -> ret_matrix('a')); 
        //return array_merge($mConsult -> ret_matrix('i'), $mConsult2 -> ret_matrix('i'));
      }catch(Exception $e)
      {
          echo 'Error getNoveda: ',  $e->getMessage(), "\n";
      }
    }

    /*! \fn: getBuscarArr 
    * \brief: Busca un dato en un array
    * \author: Edward Serrano
    * \date: 03/04/2017
    * \date modified: dia/mes/año
    * \param: paramatro
    * \return valor que retorna
    */
    function getBuscarArr($paramatro, $indice, $mArray)
    {
      try
      {
        $result = null;
        foreach ($mArray as $key => $value) 
        {
          if($value[$indice]==$paramatro)
          {
            $result = "1";
            break;
          }
          else
          {
            $result = "0";
          }  
        }               
        return $result;
      }catch(Exception $e)
      {
          echo 'Error getNoveda: ',  $e->getMessage(), "\n";
      }
    }

    /*! \fn: eliminarNoveda 
    * \brief: inabilita la noveda
    * \author: Edward Serrano
    * \date: 04/04/2017
    * \date modified: dia/mes/año
    * \param: paramatro
    * \return valor que retorna
    */
    function eliminarNoveda()
    {
      try
      {
        $fec_actual = date("Y-m-d H:i:s");
  
        $insercion = new Consulta( "START TRANSACTION", $this -> conexion );
        
        $query = "UPDATE ".BASE_DATOS.".tab_genera_noveda SET
                    ind_estado=0
                 WHERE cod_noveda = '$_REQUEST[cod_noveda]'";
        $insercion = new Consulta($query, $this -> conexion,"R");
        if($insercion = new Consulta("COMMIT", $this -> conexion))
        {
         $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$_REQUEST["cod_servic"]." \"target=\"centralFrame\">Eliminar Otra Novedad</a></b>";

         $mensaje =  "La Novedad Se Elimino con Exito".$mensaje_sat.$link_a;
         $mens = new mensajes();
         $mens -> correcto("ELIMINAR NOVEDADES",$mensaje);
        }
      }catch(Exception $e)
      {
          echo 'Error eliminarNoveda: ',  $e->getMessage(), "\n";
      }
    }

    /*! \fn: getOpgps
     *  \brief: Trae los operadores gps 
     *  \author: Edward Serrano
     *  \date: 11/04/2017
     *  \date modified: dia/mes/año
     *  \param: 
     *  \return: 
     */
    private function getOpgps()
    {

      $mSql = "SELECT cod_operad, nom_operad 
                 FROM ".BD_STANDA.".tab_genera_opegps a 
                WHERE   ind_estado = 1 ";
      $mConsult = new Consulta($mSql, $this -> conexion);
      return array_merge(array(array("--","--")),$mConsult -> ret_matriz( "i" ) );
      
    }

    /*! \fn: GridStyle 
    * \brief: estilos actualizados
    * \author: Edward Serrano
    * \date: 31/03/2017
    * \date modified: dia/mes/año
    * \param: paramatro
    * \return valor que retorna
    */
    function GridStyle()
    {
        echo "<style>
                .cellth-ltb{
                     background: #E7E7E7;
                     border-left: 1px solid #999999; 
                     border-bottom: 1px solid #999999; 
                     border-top: 1px solid #999999;
                }
                .cellth-lb{
                     background: #E7E7E7;
                     border-left: 1px solid #999999; 
                     border-bottom: 1px solid #999999; 
                }
                .cellth-b{
                     background: #E7E7E7;
                     border-bottom: 1px solid #999999; 
                }
                .cellth-tb{
                     background: #E7E7E7;
                     border-bottom: 1px solid #999999; 
                     border-top: 1px solid #999999;
                }
                .celltd-ltb{
                     border-left: 1px solid #999999; 
                     border-bottom: 1px solid #999999; 
                     border-top: 1px solid #999999;
                }
                .celltd-tb{
                     border-bottom: 1px solid #999999; 
                     border-top: 1px solid #999999;
                }
                .celltd-lb{
                     border-bottom: 1px solid #999999; 
                     border-left: 1px solid #999999;
                }
                .celltd-l{
                     border-left: 1px solid #999999;
                }
                .fontbold{
                    font-weight: bold;
                }
                .divGrilla{
                    margin: 0;
                    padding: 0;
                    border: none;
                    border-top: 1px solid #999999;
                    border-bottom: 1px solid #999999;
                }

                .CellHead {
                    background-color: #35650f;
                    color: #ffffff;
                    font-family: Times New Roman;
                    font-size: 11px;
                    padding: 4px;
                }
                .cellInfo1 {
                    background-color: #ebf8e2;
                    font-family: Times New Roman;
                    font-size: 11px;
                    padding: 2px;
                    height: 10px;
                }
                /*.campo_texto {
                    background-color: #ffffff;
                    border: 1px solid #bababa;
                    color: #000000;
                    font-family: Times New Roman;
                    font-size: 11px;
                    padding-left: 5px;
                }*/
                .crmButton {
                  width:25%;
                  height: 20px;
                }
                
              .error{
                  background-color: #45930b;
                  border-radius: 4px 4px 4px 4px;
                  color: white;
                  font-weight: bold;
                  margin-left: 6px;
                  margin-top: 3px;
                  padding: 3px 6px;
                  position: absolute;
              }
              .error:before{
                  border-color: transparent #45930b transparent transparent;
                  border-style: solid;
                  border-width: 3px 4px;
                  content: '';
                  display: block;
                  height: 0;
                  left: -16px;
                  position: absolute;
                  top: 4px;
                  width: 0;
              }
              .campo_texto, .campo_texto_on{
                border: 1px solid #DBE1EB;
                font-size: 10px;
                font-family: Arial, Verdana;
                padding-left: 5px;
                padding-right: 5px;
                padding-top: 5px;
                padding-bottom: 5px;
                border-radius: 4px;
                -moz-border-radius: 4px;
                -webkit-border-radius: 4px;
                -o-border-radius: 4px;
                background: #FFFFFF;
                background: linear-gradient(left, #FFFFFF, #F7F9FA);
                background: -moz-linear-gradient(left, #FFFFFF, #F7F9FA);
                background: -webkit-linear-gradient(left, #FFFFFF, #F7F9FA);
                background: -o-linear-gradient(left, #FFFFFF, #F7F9FA);
                color: #2E3133;
                width: 300px;
              }
              .CellInfohref{
                cursor:pointer;
                background-color: #ebf8e2;
                font-family: Times New Roman;
                font-size: 11px;
                padding: 2px;
                height: 10px;
              }
              </style>";
    }

  }//FIN CLASE PROC_NOVEDA
  $proceso = new Proc_noveda($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);
?>