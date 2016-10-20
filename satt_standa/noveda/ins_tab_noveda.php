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
        $this -> Formulario();
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
        }//FIN SWITCH
      }// FIN ELSE GLOBALS OPCION
    }//FIN FUNCION PRINCIPAL

    function Formulario()
    {
      $datos_usuario = $this -> usuario -> retornar();
      $usuario=$datos_usuario["cod_usuari"];
      $mEtapas = Proc_noveda::getEtapa();

      echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/noveda.js\"></script>\n";

      $inicio[0][0]=0;
      $inicio[0][1]='-';

      $formulario = new Formulario ("index.php","post","INSERTAR NOVEDADES","form_insert");
      $formulario -> linea("Datos Basicos de la Noveda",1,"t2");
      $formulario -> nueva_tabla();
      $formulario -> texto ("Nombre o Descripcion de la Novedad","text","nom",1,50,100,"",$_REQUEST["nom"]);
      $formulario -> caja ("Genera Alerta","indala","1",$_REQUEST["indala"],1);
      $formulario -> caja ("Solicita Tiempos","indtiemp","1",$_REQUEST["indtiemp"],1);
      $formulario -> caja ("Novedad Especial","nov_especi","1",$_REQUEST["nov_especi"],1);
      $formulario -> caja ("Mantiene Alarma","ind_manala","1",$_REQUEST["ind_manala"],1);
      $formulario -> caja ("Fuera de Plataforma","ind_fuepla","1",$_REQUEST["ind_fuepla"],1);
      $formulario -> caja ("Notifica Supervisor","ind_notsup","1",$_REQUEST["ind_notsup"],1);
      $formulario -> caja ("Inspección Vehicular","ind_insveh","1",$_REQUEST["ind_insveh"],1);
      $formulario -> caja ("Visible Esferas","ind_ealxxx","1",$_REQUEST["ind_ealxxx"],1);
      $formulario -> caja ("Limpio","ind_limpio","1",$_REQUEST["ind_limpio"],1);
      $formulario -> lista ("Etapa:", "cod_etapax", $mEtapas, 1);
      $formulario -> nueva_tabla();
      
    
    // OPeradores Novedades -------------------------------------------------------------------------------------------------------------
    	$formulario->nueva_tabla();
        $formulario->linea( "Datos Novedades", 1, "t2" );
        
      $formulario->nueva_tabla();      
        $formulario -> lista("Operador Novedad:", "cod_operad", $this -> getOperadores(), 0);
        $formulario -> texto("Código de homologación:","text","cod_homolo\"onkeypress=\"return TextInputAlpha( event )",1,10,11,"",$_REQUEST["cod_homolo"]);
        $formulario -> caja ("Visibilidad (S/N):","ind_visibl","1",$_REQUEST["ind_visibl"],1);
    // ------------------------------------------------------------------------------------------------------------------------
		$formulario->nueva_tabla();
        $formulario->linea( "Filtro de Perfiles", 1, "t2" );
		
		$perfiles = $this -> getPerfiles();
		
		if( $perfiles )
		{
			$formulario->nueva_tabla();
			foreach( $perfiles as $row )
			{
				if( $_POST["sel_todos"] ) 
					$checked = 1;
				
				
				$formulario->caja( ucwords( strtolower( $row[1] ) ), "perfil[$i]", $row[0], $checked, $end );
				$i++;
				
				if( $i % 2 != 0 ) $end = 1;
				else $end = 0;				
			}
			
			$formulario->caja( "Seleccionar Todos", "sel_todos\" onChange=\"form_insert.submit()", 1, $checked, $end );
		}

      $formulario -> nueva_tabla();

      $formulario -> oculto("usuario","$usuario",0);
      $formulario -> oculto("window","central",0);
      $formulario -> oculto("opcion",1,0);
      $formulario -> oculto("maximo",$interfaz -> cant_interf,0);
      $formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],0);
      $formulario -> boton("Insertar","button\" onClick=\"ins_tab_noveda() ",0);
      $formulario -> boton("Borrar","reset",1);
      $formulario -> cerrar();
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


      //trae el consecutivo de la tabla
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

      $cod_operad = $_REQUEST["cod_operad"]; 
      $cod_homolo = $_REQUEST["cod_homolo"];   
      $ind_visibl = $_REQUEST["ind_visibl"] == NULL ? '0' : $_REQUEST["ind_visibl"];   

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
		
		$perfiles = $_POST["perfil"];
		
		if( $perfiles )
		foreach( $perfiles as $row )
		{
			$insert = "INSERT INTO  " . BASE_DATOS . ".tab_perfil_noveda 
						(
							cod_perfil , cod_noveda
						)
						VALUES 
						(
							'$row',  '$nuevo_consec'
						)";
			
			$consulta = new Consulta( $insert, $this -> conexion, "R" );
		}	
		
      if($insercion = new Consulta("COMMIT", $this -> conexion))
      {
        $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$_REQUEST["cod_servic"]." \"target=\"centralFrame\">Insertar Otra Novedad</a></b>";

        $mensaje =  "La Novedad <b>".$_REQUEST["nom"]."</b> Se Inserto con Exito".$mensaje_sat.$link_a;
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

  }//FIN CLASE PROC_NOVEDA
  $proceso = new Proc_noveda($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);
?>