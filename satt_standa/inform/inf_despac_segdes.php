<?php

/* * *******************************************************************************************************
 * @class: InformDespacSeguim                                                                             *
 * @company: Intrared.net                                                                                 *
 * @author: Christiam Barrera( The Messias, The Only One, The Choosen One, The Unique )                   *
 * @date: 2012-06-01                                                                                      *
 * @brief: Clase que dibuja el Informe detallado de los Despachos según Transportadora y estado de alarma *
 * ******************************************************************************************************* */

class InformDespacSeguim
{

    var $conexion = NULL;
    var $cod_perfil = NULL;
    var $cod_usuari = NULL;
    var $superusuario = NULL;
    var $cod_serdet = 3302;
    var $cod_transp = NULL;
    var $all_despac = NULL;

    //----------------------------------------------------------------------------------------------------
    //@method: InformDespacSeguim                                                                        |
    //@brief : Constructor de la Clase                                                                   |
    //----------------------------------------------------------------------------------------------------
    function __construct($conexion, $mData)
    {
    
      // echo "<pre>";
      // print_r( $mData );
      // echo "</pre>";
        if ($mData['opcion'] == 'getPC')
        {
            $this->getPC();
            die();
        }
		
		// echo '<pre>';
		// print_r( $mData );
		// echo '<7pre>';
        //-------------------------------------------------------------------------------
        $this->conexion = $conexion;
        $this->cod_transp = $mData['transp'] ? $mData['transp'] : $mData['cod_transp'];

        //-------------------------------------------------------------------------------
        include_once( '../' . DIR_APLICA_CENTRAL . '/inform/class_despac_trans2.php' );
        //-------------------------------------------------------------------------------
        //$this -> genera_alarma = $this -> GetGeneraAlarma();
        //----------------------------------------------------
        $datos_usuario = $_SESSION["datos_usuario"];
        $this->cod_perfil = $datos_usuario['cod_perfil'];
        $this->cod_usuari = $datos_usuario['cod_usuari'];
        //----------------------------------------------------
        $this->superusuario = $this->VerifySuperUsuario();
        //----------------------------------------------------
		
        echo $this->FirstLoad($mData);
    }

    //-------------------------------------------------------------------------------------------------------------------|
    //@method: FirstLoad                                                                                                 |
    //@brief : Retorna el html de la primera carga del módulo que diseñé para humillar el index en las cargas asincrónas |                                                                |
    //-------------------------------------------------------------------------------------------------------------------|
    function FirstLoad($mData)
    {
        $html = NULL;
        $html .= '<tr>';
        $html .= '<td align="center" width="100%">';
        $html .= '<script type="text/javascript" src="../' . DIR_APLICA_CENTRAL . '/js/min.js"></script>';
        $html .= '<script type="text/javascript" src="../' . DIR_APLICA_CENTRAL . '/js/jquery.js"></script>';
        $html .= '<script type="text/javascript" src="../' . DIR_APLICA_CENTRAL . '/js/inf_bandej_entrad.js"></script>';
        $html .= '<script type="text/javascript" src="../' . DIR_APLICA_CENTRAL . '/js/jquery.blockUI.js"></script>';
        $html .= '<link href="../' . DIR_APLICA_CENTRAL . '/estilos/jquery.css" rel="stylesheet" type="text/css">';
        $html .= '<form action="index.php?" method="post" name="frm_report" id="frm_reportID">';
        $html .= '<div id="informDIV">';
        $html .= $this->DrawReport($mData);
        $html .= '</div>';
        $html .= '<div id="pc" style="background:#eeeeee;"></div>';
        $html .= '<input type="hidden" name="tip_alarma" id="tip_alarmaID" value="' . $mData['tip_alarma'] . '" />';
        $html .= '<input type="hidden" name="cod_transp" id="cod_transpID" value="' . $this->cod_transp . '" />';
        $html .= '<input type="hidden" name="dir_centra" id="dir_centraID" value="' . DIR_APLICA_CENTRAL . '" />';
        $html .= '<input type="hidden" name="cod_servic" id="cod_servicID" value="' . $_REQUEST['cod_servic'] . '" />';
        $html .= '<input type="hidden" name="window" id="windowID" value="central" />';
        $html .= '</form>';
        $html .= '<script language="javascript">ReloadReport();</script>';
        $html .= '</td>';
        $html .= '</tr>';
        return $html;
    }

    //-------------------------------------------------------------------------------------------------------------------|
    //@method: DrawStyle                                                                                                 |
    //@brief : Retorna el html de los estilos css utilizados en la UI de la Bandeja de Entrada                           |                                                                |
    //-------------------------------------------------------------------------------------------------------------------|
    function DrawStyle()
    {
        $html = NULL;
        $html .= '<style>';
        $html .= '.classTable{ font-family:Arial; font-size:11px; color:#444444; background:#eeeeee; }';
        $html .= '.classTitle{ padding:5px 5px 5px 5px; color:#006600; font-weight:bold; text-align:left; background:#ffffff; }';
        $html .= '.classHead{ border-right: 1px solid #ffffff; border-bottom: 1px solid #ffffff; padding:5px 10px 5px 10px; color:#333333; }';
        $html .= '.classCell{ border-right: 1px solid #ffffff; border-bottom: 1px solid #ffffff; padding:2px 5px 2px 5px; color:#444444; }';
        $html .= '.classList{ background:#eeeeee; font-family:Arial; font-size:11px; color:#444444; font-weight:bold; padding:0px 5px 0px 5px; }';
        $html .= '.bgEAL{ background:#7FFF99; }';
        $html .= '.cp{ cursor:pointer; }';
        $html .= '.classLink{ font-family:Arial; font-size:11px; color:#111111; font-weight:bold; }';
        $html .= '.classMenu{ border-left: 1px solid #ffffff; border-right: 1px solid #ffffff; background:#009900; cursor:pointer; width:7px; }';
        $html .= '</style>';
        return $html;
    }

    //-------------------------------------------------------------------------------------------------------------------|
    //@method: set_link                                                                                                  |
    //@brief : Retorna el html de los hipervinculos de los despachos                                                     |                                                                |
    //-------------------------------------------------------------------------------------------------------------------|
    function set_link($num_despac, $tie_ultnov = NULL, $add = NULL)// jorge 120404
    {
        $a = '<a class="classLink" style="'.$add.'" href="index.php?';
        $a .= 'cod_servic=' . $this->cod_serdet;
        $a .= '&window=central';
        $a .= '&despac=' . $num_despac;
        if ( $tie_ultnov )
            $a .= '&tie_ultnov=' . $tie_ultnov;
        $a .= '&opcion=1">';
        $a .= $num_despac;
        $a .= '</a>';
        return $a;
    }
	
	//------------------------------------------------------------------------------------
	//@method: GetDespacTiempoPropio                                                     |
	//@brief : Determina si un despacho está con novedad de tiempo propio                |
	//------------------------------------------------------------------------------------
  function GetDespacTiempoPropio( $num_despac ) {
    $mSql  = "SELECT 1  
                FROM ".BASE_DATOS.".tab_despac_despac a 
               WHERE a.num_despac = '".$num_despac."' 
                 AND a.ind_tiemod = '1' ";
    $consul = new Consulta( $mSql, $this -> conexion );
    $matriz = $consul -> ret_matrix( 'a' );
    return count( $matriz ) == 0 ? FALSE : TRUE;
  }

    //----------------------------------------------------------------------------------------------------------------------------------|
    //@method: set_link                                                                                                                 |
    //@brief : Retorna el html de la tabla principal del informe en la cual se realiza la instancia del objeto de la clase DespacTransi |                                 |                                                                |
    //----------------------------------------------------------------------------------------------------------------------------------|
    function DrawReport($mData)
    {
        //-------------------------------------------------------------
        $obj_destra = new DespacTransi($this->conexion, $mData);
      
        //-------------------------------------------------------------
        $_TRANSP = $obj_destra -> GetTranspData( $this -> cod_transp );
        
        
        /*echo "<pre>";
        print_r( $_TRANSP );
        echo "</pre>";*/
        
        //$_ACARGO = explode( ',', str_replace( ' ', NULL, $_TRANSP['can_defini'] ) );
        
        
        $size_transp = sizeof($_TRANSP);
        //-------------------------------------------------------------
        
        $_DESPAC_TIPALA = explode(', ', $_TRANSP[$mData['tip_alarma']]);
        

        
        //sort($_DESPAC_TIPALA);
        
        
        $size_tipala = sizeof($_DESPAC_TIPALA);
       
        /*echo "<hr><pre>";
        print_r($size_tipala);
        echo "</pre>";*/
        //-------------------------------------------------------------
        
        if( $_TRANSP['can_penlle'] )
          $_DESPAC_PENLLE = explode(', ', $_TRANSP['can_penlle']);

          $size_penlle = sizeof($_DESPAC_PENLLE);
          
          if ( $size_penlle > 0 ) :
            sort( $_DESPAC_PENLLE );
          endif;
        //-------------------------------------------------------------
		
		//-------------------------------------------------------------
        
        if( $_TRANSP['can_tiepro'] )
          $_DESPAC_TIEPRO = explode(', ', $_TRANSP['can_tiepro']);

          $size_tiepro = sizeof($_DESPAC_TIEPRO);
          
          if ( $size_tiepro > 0 ) :
            sort( $_DESPAC_TIEPRO );
          endif;
        //-------------------------------------------------------------

       
        //------------------------
        echo $this->DrawStyle();
        //------------------------
        $back = NULL;
        $back .= '<div style="height:20px;">';
        $back .= '<label class="send_back" style="cursor:pointer; color:#006600; font-weight:bold;" onclick="history.back();">[ volver ]</label>';
        $back .= '</div>';
        //-------------
        $html = NULL;
        $html .= '<table class="classTable" align="center" width="100%" cellspacing="0" cellpadding="0">';

        $html .= '<tr>';
        
      
        //-------------------------------------------------------------------------------------------------
        $tot_regist = $mData['tip_alarma'] == 'can_despac' ? ( $size_tipala - $size_penlle ) : $size_tipala ;
      
        $lab_alarma = $tot_regist == 1 ? 'SE ENCONTRÓ ' : 'SE ENCONTRARON ';
        $lab_alarma .= '|*| ';
        
        //$lab_alarma .= $mData['tip_alarma'] == 'can_despac' ? ( $size_tipala - $size_penlle ) . ' ' : $size_tipala . ' ';
        
        $lab_alarma .= $tot_regist == 1 ? 'DESPACHO EN RUTA ' : 'DESPACHOS EN RUTA ';
        if ($mData['tip_alarma'] == 'can_sinret')
        {
            $lab_alarma .= 'SIN RETRASO';
        }
        if ($mData['tip_alarma'] == 'can_30mins')
        {
            $lab_alarma .= $size_tipala == 1 ? 'RETRASADO ENTRE 1 Y 30 MINUTOS' : 'RETRASADOS ENTRE 1 Y 30 MINUTOS';
        }
        if ($mData['tip_alarma'] == 'can_60mins')
        {
            $lab_alarma .= $size_tipala == 1 ? 'RETRASADO ENTRE 31 Y 60 MINUTOS' : 'RETRASADOS ENTRE 31 Y 60 MINUTOS';
        }
        if ($mData['tip_alarma'] == 'can_90mins')
        {
            $lab_alarma .= $size_tipala == 1 ? 'RETRASADO ENTRE 61 Y 90 MINUTOS' : 'RETRASADOS ENTRE 61 Y 90 MINUTOS';
        }
        if ($mData['tip_alarma'] == 'can_120mins')
        {
            $lab_alarma .= $size_tipala == 1 ? 'RETRASADO EN MAS DE 91 MINUTOS' : 'RETRASADOS EN MAS DE 91 MINUTOS';
        }
        if ($mData['tip_alarma'] == 'can_alaEAL')
        {
            $lab_alarma .= 'CON ALARMA EAL';
        }
        if ($mData['tip_alarma'] == 'can_fuepla')
        {
            $lab_alarma .= 'FUERA DE PLATAFORMA';
        }
        if ($mData['tip_alarma'] == 'can_defini')
        {
            $lab_alarma .= 'A CARGO EMPRESA';
        }
        if ($mData['tip_alarma'] == 'can_especi')
        {
            $lab_alarma .= 'CON NOVEDAD ESPECIAL';
        }
        if ($mData['tip_alarma'] == 'can_penlle')
        {
            $lab_alarma .= $size_tipala == 1 ? 'PENDIENTE POR LLEGAR' : 'PENDIENTES POR LLEGAR';
        }
        $html2 = NULL;
        if(($_REQUEST[transp]=='860068121' || $_REQUEST[cod_transp]=='860068121')) {
            $html2 .= '<tr>';
                $html2 .= '<td nowrap colspan="14">';
                    $html2 .= '<div style="border: 2px solid black; width: 16px; background: #EEEEEE; display: inline-block;">&nbsp;</div>&nbsp;Unico   Viaje&nbsp;';
                    $html2 .= '<div style="border: 2px solid black; width: 16px; background: #FFFF66; display: inline-block;">&nbsp;</div>&nbsp;Primer  Viaje&nbsp;';
                    $html2 .= '<div style="border: 2px solid black; width: 16px; background: #ffc266; display: inline-block;">&nbsp;</div>&nbsp;Segundo Viaje&nbsp;';
                $html2 .= '</td>';
            $html2 .= '</tr>';
        }
        $html2 .= '<tr>';
        $html2 .= '<th nowrap colspan="14">';
        $html2 .= '<div class="classTitle">' . $lab_alarma . '</div>';
        //-------------------------------------------------------------------------------------------------
        $html2 .= '</tr>';
        $html2 .= '<tr>';
        $html2 .= '<th nowrap class="classHead" align="center" width="15">#</th>';
        $html2 .= '<th nowrap class="classHead" align="center" width="75">DESPACHO</th>';
        $html2 .= '<th nowrap class="classHead" align="center" width="25">TIEMPO</th>';
        $html2 .= '<th nowrap class="classHead" align="center">FECHA CITA DESCARGUE</th>';
        $html2 .= '<th nowrap class="classHead" align="center">A C. EMPRESA</th>';
        $html2 .= '<th nowrap class="classHead" align="center">NO. TRANSPORTE</th>';
        $html2 .= '<th nowrap class="classHead" align="center">NOVEDADES</th>';
        $html2 .= '<th nowrap class="classHead" align="center">ORIGEN</th>';
        $html2 .= '<th nowrap class="classHead" align="center">DESTINO</th>';
        $html2 .= '<th nowrap class="classHead" align="center">TRANSPORTADORA</th>';
        $html2 .= '<th nowrap class="classHead" align="center">PLACA</th>';
        $html2 .= '<th nowrap class="classHead" align="center">CONDUCTOR</th>';
        $html2 .= '<th nowrap class="classHead" align="center">CELULAR</th>';
        $html2 .= '<th nowrap class="classHead" align="center">UBICACI&Oacute;N</th>';
        $html2 .= '</tr>';

        if ($size_tipala == 0)
        {
            echo 'No hay registros...';
        }
        else
        {
          
            //------------------------------------------------------------------
            //@ORDENAMIENTO DE MAYOR A MENOR POR TIEMPOS
            //------------------------------------------------------------------
            $_TEMP = array();
            
            // if($_SESSION['datos_usuario']['cod_usuari'] == 'soporte' )
            // {
              // echo "<pre>";
              // print_r($_DESPAC_TIPALA);
              // echo "</pre>";  
            // }
            
            foreach ( $_DESPAC_TIPALA as $num_despac )
            {
              //----------------------------------------------------------
              $obj_destra = new DespacTransi( $this -> conexion, $mData );
              //----------------------------------------------------------
              $_DESPAC = $obj_destra -> GetDespacData( $num_despac );
              
              $DataDescar = $this -> getDiffMinuto( $num_despac, $_DESPAC['cod_transp'] );
             
              $_DESPAC['num_despac'] = $num_despac;

              $_DESPAC['dif_minuto'] = $DataDescar[0];
              $_DESPAC['fec_citdes'] = $DataDescar[1];
              $_DESPAC['col_citdes'] = $DataDescar[2];
              //----------------------------------------------------------
              $_TEMP[] = $_DESPAC;
              
            }
            
            // echo "<pre>";
            // print_r( $_TEMP );
            // echo "</pre>";
            
            //---------------------------------------------------------------
            $_SORT = $this -> array_sort( $_TEMP, 'dif_minuto', SORT_DESC );
            // if($_SESSION['datos_usuario']['cod_usuari'] == 'soporte' )
            // {
              // echo "<pre>";
              // print_r($_SORT);
              // echo "</pre>";  
            // }
            
            //---------------------------------------------------------------

            if(($_REQUEST[transp]=='860068121' || $_REQUEST[cod_transp]=='860068121')) {
                foreach ($_SORT as $row) {
                    if(!empty( $row[num_desext] )){
                        $DESPAC_PLACA[ $row[num_placax] ][] = $row[num_desext];
                    }
                }
                $_DESPAC_PLACA_ = array();
                foreach ($DESPAC_PLACA as $i => $ARR_PLACA) {
                    asort($DESPAC_PLACA[$i]);
                    $c=0;
                    foreach ($DESPAC_PLACA[$i] as $j => $VAL_VIAJE) {
                        if($c>1)
                            unset( $DESPAC_PLACA[$i][$j] );
                        else
                            $_DESPAC_PLACA_[$i][] = $VAL_VIAJE;
                        $c++;
                    }
                }
            }

            /*
            $_SORT = array();
            foreach ( $_TEMP as $_DESPAC )
            {
              $_SORT[] = $_DESPAC['num_despac'];
            }
            //-----------------------
            $_DESPAC_TIPALA = $_SORT;
            //-----------------------
            */
          
          $_TIPSER = explode( '.', $mData['tip_servic'] );
			 
            $i = 1;
        foreach ( $_SORT as $_DESPAC )
        {


            if(($_REQUEST[transp]=='860068121' || $_REQUEST[cod_transp]=='860068121')){ 
                if(count($_DESPAC_PLACA_[ $_DESPAC['num_placax'] ])>1){
                    if(!in_array($_DESPAC['num_desext'], $_DESPAC_PLACA_[ $_DESPAC['num_placax'] ])){
                        continue;
                    }else{
                        $color = array_search($_DESPAC['num_desext'], $_DESPAC_PLACA_[ $_DESPAC['num_placax'] ]) == max(array_keys($_DESPAC_PLACA_[ $_DESPAC['num_placax'] ])) ? '#ffc266' : '#FFFF66';
                    }
                }else{
                    $color = "transparent";
                }
            }

          $num_despac = $_DESPAC['num_despac'];
          $ult_noveda = $obj_destra->GetDespacUltimaNovedaContro($num_despac);
          $propio = $this -> GetDespacTiempoPropio ( $num_despac );
			  
        if( !$propio )
			  {
          /*echo "<pre>";
          print_r( $_DESPAC );
          echo "</pre>";*/
				  /*
				  if ( in_array( $num_despac, $_ACARGO ) && $mData['tip_alarma'] != 'can_defini' ) :
					continue;
				  endif;
				  */
					//----------------------------------------------------------
					//$obj_destra = new DespacTransi( $this -> conexion, $mData );
					//----------------------------------------------------------
					//$_DESPAC = $obj_destra -> GetDespacData( $num_despac );
					//----------------------------------------------------------
					if ($mData['tip_alarma'] == 'can_despac' && $_DESPAC['can_penlle'] == 1)
					{
					  continue;
					}
				  


							if( $_TIPSER[0] != NULL )
					{
					  $tip_servic = $this -> GetTranspTipser( $_DESPAC['cod_transp'] );
					  $tip_servic = $tip_servic['cod_tipser'];
			  
					  if ( !in_array( $tip_servic, $_TIPSER ) ) 
					  { 
						$tot_regist --;
						continue;
					  }
					}
										
					$can_alaEAL = $_DESPAC['can_alaEAL'];
					$col_alarma = $_DESPAC['col_alarma'];
					$dif_minuto = $_DESPAC['dif_minuto'];
					$fec_citdes = $_DESPAC['fec_citdes'];
					$col_citdes = $_DESPAC['col_citdes'];
					$fec_progra = $_DESPAC['fec_progra'];
					$ind_defini = $_DESPAC['can_defini'] == 1 ? 'SI' : 'NO';
					$cod_manifi = $_DESPAC['cod_manifi'];
					$nom_ciuori = $_DESPAC['nom_ciuori'] . ' (' . substr($_DESPAC['nom_depori'], 0, 4) . ')';
					$nom_ciudes = $_DESPAC['nom_ciudes'] . ' (' . substr($_DESPAC['nom_depdes'], 0, 4) . ')';
					$nom_transp = $_DESPAC['nom_transp'];
					$num_placax = $_DESPAC['num_placax'];
					if ($_DESPAC['ap1_conduc'])
					{
						$nom_conduc = $_DESPAC['nom_conduc'] . ' ' . $_DESPAC['ap1_conduc'];
					}
					else
					{
						$nom_conduc = $_DESPAC['abr_conduc'];
					}
					$cel_conduc = $_DESPAC['cel_conduc'];

					$border = strtolower($col_alarma) == 'ffffff' ? 'border-bottom:1px solid #f9f9f9;' : NULL;
          
          $add = '';
          
          if( $mData['tip_alarma'] == 'pri_sitiox' )
          {
            $col_alarma = 'F291B0';
            $add = 'color:#FFFFFF;';
          }
          elseif( $mData['tip_alarma'] == 'des_cargue' )
          {
            $col_alarma = 'F25285';
            $add = 'color:#FFFFFF;';
          }
          elseif( $mData['tip_alarma'] == 'sin_noveda' )
            $col_alarma = 'FAE3EA';
          
          if( $mData['tip_alarma'] == 'cit_descar' )
          {
            $col_alarma = 'EAA8FF';
          }
          
          if( $mData['tip_alarma'] == 'pro_descar' )
          {
            $col_alarma = 'B2E887';
          }
          elseif( $mData['tip_alarma'] == 'enx_descar' )
          {
            $col_alarma = '51B300';
            $add = 'color:#FFFFFF;';
          }
          elseif( $mData['tip_alarma'] == 'sin_descar' )
          {
            $col_alarma = '35650F';
            $add = 'color:#FFFFFF;';
          }
          
          
					$line = NULL;
					$line .= '<tr onclick="this.style.background=this.style.background==\'#ceecf5\'?\'#eeeeee\':\'#ceecf5\';">';
					$line .= '<th nowrap class="classCell" align="left">' . $i . '</th>';
					$line .= '<th nowrap class="classCell" align="left" style="background:#' . $col_alarma . '; ' . $border . '">';
					$line .= $this->set_link($num_despac, $dif_minuto, $add );
					$line .= '</th>';
					$line .= '<th nowrap style="background:#'.$col_citdes.';" class="classCell" align="left">';
					$line .= '<label class="cp" title="' . $fec_progra . '">' . $dif_minuto . '</label>';
					$line .= '</th>';
					$line .= '<th nowrap class="classCell" align="center">' . $fec_citdes . '</th>';
					$line .= '<th nowrap class="classCell" align="center">' . $ind_defini . '</th>';
					if ($can_alaEAL == 1)
					{
						$line .= '<th nowrap class="classCell" align="left" style="background:#7fff99;">';
						$line .= '<label class="cp" onclick="poppc( ' . $num_despac . ' );">' . $cod_manifi . '</label>';
						$line .= '</th>';
					}
					else
					{
						$line .= '<td nowrap class="classCell" align="left">' . $cod_manifi . '</td>';
					}
					
					$llamadas1 = "SELECT COUNT( 1 )
								  FROM " . BASE_DATOS . ".tab_despac_contro a
								  WHERE a.num_despac = '$num_despac'";

					$consulta = new Consulta($llamadas1, $this->conexion);
					$llamadas1 = $consulta->ret_matriz();
					$llamadas1 = $llamadas1[0][0];

					$llamadas2 = "SELECT COUNT( 1 )
								  FROM " . BASE_DATOS . ".tab_despac_noveda a
								  WHERE a.num_despac = '$num_despac'";

					$consulta = new Consulta($llamadas2, $this->conexion);
					$llamadas2 = $consulta->ret_matriz();
					$llamadas2 = $llamadas2[0][0];
          
          /*echo "<pre>";
          print_r( $ult_noveda );
          echo "</pre>";*/

					$line .= '<td nowrap class="classCell" align="left">' . ( (int)$llamadas1 + (int)$llamadas2 ) . '</td>';
					$line .= '<td nowrap class="classCell" align="left">' . $nom_ciuori . '</td>';
					$line .= '<td nowrap class="classCell" align="left">' . $nom_ciudes . '</td>';
					$line .= '<td nowrap class="classCell" align="left">' . $nom_transp . '</td>';
					$line .= '<td nowrap class="classCell" align="center" style="background: '.$color.'">' . $num_placax . '</td>';
					$line .= '<td nowrap class="classCell" align="left">' . $nom_conduc . '</td>';
					$line .= '<td nowrap class="classCell" align="left">' . $cel_conduc . '</td>';
          $line .= '<td nowrap class="classCell" align="left">' . ( $ult_noveda['nom_sitiox'] != '' ? $ult_noveda['nom_sitiox'] : '---' ) . '</td>';
					$line .= '</tr>';
					//-------------
					$html2 .= $line;
					//-------------
					$i++;
				}
        }
        }
		
        // echo "--> ".$size_penlle;
        if ( $mData['tip_alarma'] == 'can_despac' && $size_penlle > 0 && $this -> cod_transp )
        { 
            $html3 = NULL;
            $html3 .= '<tr>';
            $html3 .= '<th nowrap colspan="13">';
            //-------------------------------------------------------------------------------------------------
            $lab_alarma = $size_penlle == 1 ? 'SE ENCONTRÓ ' : 'SE ENCONTRARON ';
            $lab_alarma .= $size_penlle . ' ';
            $lab_alarma .= $size_penlle == 1 ? 'DESPACHO PENDIENTE POR LLEGAR' : 'DESPACHOS PENDIENTES POR LLEGAR';
            $html3 .= '<div class="classTitle">' . $lab_alarma . '</div>';
            //-------------------------------------------------------------------------------------------------
            $html3 .= '</th>';
            $html3 .= '</tr>';

            $html3 .= '</tr>';
            $html3 .= '<th nowrap class="classHead" align="center">#</th>';
            $html3 .= '<th nowrap class="classHead" align="center" colspan="3">DESPACHO</th>';
            $html3 .= '<th nowrap class="classHead" align="center">NO. TRANSPORTE</th>';
            $html3 .= '<th nowrap class="classHead" align="center">NOVEDADES</th>';
            $html3 .= '<th nowrap class="classHead" align="center">ORIGEN</th>';
            $html3 .= '<th nowrap class="classHead" align="center">DESTINO</th>';
            $html3 .= '<th nowrap class="classHead" align="center">TRANSPORTADORA</th>';
            $html3 .= '<th nowrap class="classHead" align="center">PLACA</th>';
            $html3 .= '<th nowrap class="classHead" align="center">CONDUCTOR</th>';
            $html3 .= '<th nowrap class="classHead" align="center">CELULAR</th>';
            $html3 .= '<th nowrap class="classHead" align="center">UBICACI&Oacute;N</th>';
            $html3 .= '</tr>';

            $i = 1;
            foreach ($_DESPAC_PENLLE as $num_despac)
            {
                //----------------------------------------------------------
                $obj_destra = new DespacTransi($this->conexion, $mData);
                //----------------------------------------------------------
                $_DESPAC = $obj_destra->GetDespacData($num_despac);
                $ult_noveda = $obj_destra->GetDespacUltimaNovedaContro($num_despac);
                //----------------------------------------------------------
                $can_alaEAL = $_DESPAC['can_alaEAL'];
                $col_alarma = $_DESPAC['col_alarma'];
                $cod_manifi = $_DESPAC['cod_manifi'];
                $nom_ciuori = $_DESPAC['nom_ciuori'] . ' (' . substr($_DESPAC['nom_depori'], 0, 4) . ')';
                $nom_ciudes = $_DESPAC['nom_ciudes'] . ' (' . substr($_DESPAC['nom_depdes'], 0, 4) . ')';
                $nom_transp = $_DESPAC['nom_transp'];
                $num_placax = $_DESPAC['num_placax'];
                if ($_DESPAC['ap1_conduc'])
                {
                    $nom_conduc = $_DESPAC['nom_conduc'] . ' ' . $_DESPAC['ap1_conduc'];
                }
                else
                {
                    $nom_conduc = $_DESPAC['abr_conduc'];
                }
                $cel_conduc = $_DESPAC['cel_conduc'];

                $border = strtolower($col_alarma) == 'ffffff' ? 'border-bottom:1px solid #f9f9f9;' : NULL;

                $line = NULL;
                $line .= '<tr onclick="this.style.background=this.style.background==\'#ceecf5\'?\'#eeeeee\':\'#ceecf5\';">';
                $line .= '<th nowrap class="classCell" align="left">' . $i . '</th>';
                $line .= '<th nowrap class="classCell" align="left" colspan="3">';
                $line .= $this->set_link($num_despac);
                $line .= '</th>';
				
                if ($can_alaEAL == 1)
                {
                    $line .= '<th nowrap class="classCell" align="left" style="background:#7fff99;">';
                    $line .= '<label class="cp" onclick="poppc( ' . $num_despac . ' );">' . $cod_manifi . '</label>';
                    $line .= '</th>';
                }
                else
                {
                    $line .= '<td nowrap class="classCell" align="left">' . $cod_manifi . '</td>';
                }
				
				
				$llamadas1 = "SELECT COUNT( 1 )
							  FROM " . BASE_DATOS . ".tab_despac_contro a
							  WHERE a.num_despac = '$num_despac'";

				$consulta = new Consulta($llamadas1, $this->conexion);
				$llamadas1 = $consulta->ret_matriz();
				$llamadas1 = $llamadas1[0][0];

				$llamadas2 = "SELECT COUNT( 1 )
							  FROM " . BASE_DATOS . ".tab_despac_noveda a
							  WHERE a.num_despac = '$num_despac'";

				$consulta = new Consulta($llamadas2, $this->conexion);
				$llamadas2 = $consulta->ret_matriz();
				$llamadas2 = $llamadas2[0][0];
				
				$line .= '<td nowrap class="classCell" align="left">' . ( (int)$llamadas1 + (int)$llamadas2) . '</td>';
                $line .= '<td nowrap class="classCell" align="left">' . $nom_ciuori . '</td>';
                $line .= '<td nowrap class="classCell" align="left">' . $nom_ciudes . '</td>';
                $line .= '<td nowrap class="classCell" align="left">' . $nom_transp . '</td>';
				
                
				
                $line .= '<td nowrap class="classCell" align="center">' . $num_placax . '</td>';
                $line .= '<td nowrap class="classCell" align="left">' . $nom_conduc . '</td>';
                $line .= '<td nowrap class="classCell" align="left">' . $cel_conduc . '</td>';
                $line .= '<td nowrap class="classCell" align="left">' . ( $ult_noveda['nom_sitiox'] != '' ? $ult_noveda['nom_sitiox'] : '---' )  . '</td>';
                $line .= '</tr>';
                //-------------
                $html3 .= $line;
                //-------------
                $i++;
            }
        }

        $html3 .= '</table>';
		
        $html3 .= '<input type="hidden" name="tip_servic" id="tip_servicID" value="'.$mData['tip_servic'].'" />';
        
        //echo "----> ".$tot_regist - count($_NUEVO1);
        
        if ( $tot_regist - count($_NUEVO1) )
          $html = $html . $html2 . $html3 ; 
        else
          $html = $html . $html3 ; 
       
        $html  = str_replace( '|*|', $tot_regist - count($_NUEVO1), $html );

        //----------------------
        $_SESSION["HTML"] = $html;
        //----------------------
        return $back . $html . $back;
    }

    //----------------------------------------------------------------------------------------------------------------------------------|
    //@method: GetGeneraAlarma                                                                                                          |
    //@brief : Retorna la matriz con los datos de las Alarmas                                                                           |                                   
    //----------------------------------------------------------------------------------------------------------------------------------|
    function GetGeneraAlarma()
    {
        $mSql = "SELECT a.cod_alarma, 
                     UPPER( a.nom_alarma ) AS nom_alarma, 
                     a.cant_tiempo AS can_tiempo, 
                     a.cod_colorx 
                FROM " . BASE_DATOS . ".tab_genera_alarma a ORDER BY 1 ";
        $consul = new Consulta($mSql, $this->conexion);
        $matriz = $consul->ret_matrix('a');
        return $matriz;
    }

    //----------------------------------------------------------------------------------------------------------------------------------|
    //@method: GetDespacUsuariAsigna                                                                                                    |
    //@brief : Retorna la cadena con los usarios asignados al contro de los despachos                                                   |                                   
    //----------------------------------------------------------------------------------------------------------------------------------|
    function GetDespacUsuariAsigna($cod_usuari = NULL)
    {
        $mSql = "SELECT b.cod_tercer
                    FROM " . BASE_DATOS . ".tab_monito_encabe a,
                         " . BASE_DATOS . ".tab_monito_detall b
                   WHERE a.ind_estado = '1' 
                     AND b.ind_estado = '1' 
                     AND a.cod_consec = b.cod_consec 
                     AND a.fec_inicia <= NOW() 
                     AND a.fec_finalx >= NOW() ";
        $mSql .= $cod_usuari == NULL ? NULL : " AND a.cod_usuari = '" . $cod_usuari . "' ";
        $consul = new Consulta($mSql, $this->conexion);
        $matriz = $consul->ret_matrix('a');
        $in = NULL;
        for ($i = 0; $i < sizeof($matriz); $i++)
        {
            $in .= $in == NULL ? $matriz[$i]['cod_tercer'] : ", " . $matriz[$i]['cod_tercer'];
        }
        return $in;
    }

    //----------------------------------------------------------------------------------------------------------------------------------|
    //@method: GetGeneraTipser                                                                                                          |
    //@brief : Retorna la matriz de los Tipos de Servicios                                                                              |                                   
    //----------------------------------------------------------------------------------------------------------------------------------|
    function GetGeneraTipser()
    {
        $mSql = "SELECT a.cod_tipser, 
                     UPPER( TRIM( a.nom_tipser ) ) AS nom_tipser 
                FROM " . BASE_DATOS . ".tab_genera_tipser a ORDER BY 2 ";
        $consul = new Consulta($mSql, $this->conexion);
        $matriz = $consul->ret_matrix('a');
        return $matriz;
    }

    //----------------------------------------------------------------------------------------------------------------------------------|
    //@method: VerifySuperUsuario                                                                                                       |
    //@brief : Retorna la matriz de los Tipos de Servicios                                                                              |                                   
    //----------------------------------------------------------------------------------------------------------------------------------|
    function VerifySuperUsuario()
    {
        $_SUPER[] = COD_PERFIL_ADMINIST;
        $_SUPER[] = COD_PERFIL_SUPEGENE;
        $_SUPER[] = COD_PERFIL_SUPEFARO;
        $_SUPER[] = COD_PERFIL_SUPERUSR;
        $_SUPER[] = COD_PERFIL_AUDITORX;
        return in_array($this->cod_perfil, $_SUPER) ? TRUE : FALSE;
    }

    //----------------------------------------------------------------------------------------------------------------------------------|
    //@method: GetListSuperUsuario                                                                                                      |
    //@brief : Retorna la matriz con los usuarios de Monitoreo                                                                          |                                   
    //----------------------------------------------------------------------------------------------------------------------------------|
    function GetListSuperUsuario()
    {
        $mSql = "SELECT a.cod_usuari  
                FROM " . BASE_DATOS . ".tab_monito_encabe a,
                     " . BASE_DATOS . ".tab_monito_detall b,
                     " . BASE_DATOS . ".tab_genera_usuari c
               WHERE a.ind_estado = '1' 
                 AND b.ind_estado = '1' 
                 AND a.cod_consec = b.cod_consec 
                 AND a.fec_inicia <= NOW() 
                 AND a.fec_finalx >= NOW() 
                 AND a.cod_usuari = c.cod_usuari 
                 AND c.cod_perfil NOT IN ( " . CONS_PERFIL . " )  GROUP BY 1 ORDER BY 1 ";
        $consult = new Consulta($mSql, $this->conexion);
        $_USUARI = $consult->ret_matrix('i');
        return array_merge(array(array('SIN ASIGNAR')), $_USUARI);
    }

    function getPC()
    {

        @session_start();
        include( "../lib/general/conexion_lib.inc" );
        include( "../lib/general/tabla_lib.inc" );
        include( "../lib/general/constantes.inc" );
        define('BASE_DATOS', $_SESSION['BASE_DATOS']);
        $this->conexion = new Conexion("bd7.intrared.net", $_SESSION["USUARIO"], $_SESSION["CLAVE"], BASE_DATOS);

        $num_despac = $_REQUEST["num_despac"];

        $mQuery = "SELECT a.num_despac, a.cod_rutasx, c.nom_rutasx, b.nom_contro
                   FROM " . BASE_DATOS . ".tab_despac_seguim a,
                        " . BASE_DATOS . ".tab_genera_contro b,
                        " . BASE_DATOS . ".tab_genera_rutasx c
                  WHERE a.num_despac = '" . $num_despac . "'
                    AND a.cod_contro = b.cod_contro
                    AND a.cod_rutasx = c.cod_rutasx
                    AND a.ind_estado = 0
                    AND b.ind_virtua = '0'
                    AND a.cod_contro NOT IN ( SELECT cod_contro FROM " . BASE_DATOS . ".tab_despac_noveda WHERE num_despac = '" . $num_despac . "' GROUP BY cod_contro ) ";

        $consulta = new Consulta($mQuery, $this->conexion);
        $consulta = $consulta->ret_matriz();

        $mHtml = "<table class='formulario' width='100%' cellspacing='0' cellpadding='4'>";
        $mHtml .= "<tbody>";
        $mHtml .= "<tr>";
        $mHtml .= "<td class='celda_titulo2' align='left' colspan='4'>";
        $mHtml .= "<b>Informaci&oacute;n Basica</b>";
        $mHtml .= "</td>";
        $mHtml .= "</tr>";
        $mHtml .= "<tr>";
        $mHtml .= "<td class='celda_titulo' align='right'>N&uacute;mero de Despacho: </td>";
        $mHtml .= "<td class='celda_info'>" . $consulta[0]["num_despac"] . "</td>";
        $mHtml .= "<td class='celda_titulo' align='right'>C&oacute;digo de Ruta: </td>";
        $mHtml .= "<td class='celda_info'>" . $consulta[0]["cod_rutasx"] . "</td>";
        $mHtml .= "</tr>";
        $mHtml .= "<tr>";
        $mHtml .= "<td class='celda_titulo' align='right'>Ruta: </td>";
        $mHtml .= "<td class='celda_info' colspan='3'>" . utf8_decode($consulta[0]["nom_rutasx"]) . "</td>";
        $mHtml .= "</tr>";
        $mHtml .= "<tr>";
        $mHtml .= "<tD class='celda_titulo' align='center' colspan='4'>Detalle de Puestos de Control</td>";
        $mHtml .= "</tr>";
        $mHtml .= "</tbody>";
        $mHtml .= "</table>";

        $mHtml .= "<div style='width: 100% overflow: auto; height: 250px;'>";
        $mHtml .= "<table class='formulario' width='100%' cellspacing='0' cellpadding='4'>";
        $mHtml .= "<tbody>";
        for ($i = 0, $len = sizeof($consulta); $i < $len; $i++)
        {
            $mHtml .= "<tr>";
            $mHtml .= "<td class='celda_info' align='center' colspan='4'>" . $consulta[$i]["nom_contro"] . "</td>";
            $mHtml .= "</tr>";
        }
        $mHtml .= "<tbody>";
        $mHtml .= "</table>";
        $mHtml .= "</div>";

        echo $mHtml;
    }
    
    
    function array_sort( $array, $on, $order=SORT_ASC ) { 
      $new_array = array();
      $sortable_array = array();
      if (count($array) > 0) {
        foreach ($array as $k => $v) {
          if (is_array($v)) {
            foreach ($v as $k2 => $v2) {
              if ($k2 == $on) {
                $sortable_array[$k] = $v2;
              }
            }
          } 
          else {
            $sortable_array[$k] = $v;
          }
        }
        switch ( $order ) {
          case SORT_ASC:
          asort( $sortable_array );
        break;
        case SORT_DESC:
          arsort( $sortable_array );
        break;
      }
      foreach ($sortable_array as $k => $v) {
        $new_array[$k] = $array[$k];
      }
    }
    return $new_array;
  }
  
  function getDiffMinuto( $num_despac, $cod_transp )
  {
    $to_return = array();
    $fec_actual = date('Y-m-d H:i:s');
/*    
    $mSelect = "SELECT CONCAT(fec_citdes, ' ', hor_citdes) AS date_citdes 
                  FROM ".BASE_DATOS.".tab_despac_destin 
                 WHERE num_despac = '".$num_despac."' 
                   AND fec_citdes != '0000-00-00'
                   AND hor_citdes != '00:00:00'
                 ORDER BY 1
                 LIMIT 1

               UNION

                SELECT CONCAT(fec_citdes, ' ', hor_citdes) AS date_citdes 
                  FROM ".BASE_DATOS.".tab_despac_inddes 
                 WHERE num_despac = '".$num_despac."' 
                   AND fec_citdes != '0000-00-00'
                   AND hor_citdes != '00:00:00'
                 ORDER BY 1
                 LIMIT 1
                 ";
                 */
    $mSelect = "SELECT CONCAT(fec_citdes, ' ', hor_citdes) AS date_citdes 
                  FROM ".BASE_DATOS.".tab_despac_inddes 
                 WHERE num_despac = '".$num_despac."' 
                   AND fec_citdes != '0000-00-00'
                   AND hor_citdes != '00:00:00'
                 ORDER BY 1
                 LIMIT 1
                 ";


    $consult = new Consulta( $mSelect, $this -> conexion );
    $dat_citdes = $consult -> ret_matrix( 'a' );
    
    $fec_citdes = $dat_citdes[0]['date_citdes'];
    
    $dif_minuto = $this -> diferenciaMinutosFecha( $fec_actual, $fec_citdes );
    
    $to_return[0] = $dif_minuto;
    $to_return[1] = $fec_citdes;
    
    $mSelect = "SELECT cod_colorx, min_ranini, min_ranfin 
                  FROM ".BASE_DATOS.".tab_genera_impact 
                 WHERE cod_transp = '".$cod_transp."'
                 ORDER BY 2";
    $consult = new Consulta( $mSelect, $this -> conexion );
    $rangos = $consult -> ret_matrix( 'a' );
    $size_rangos = sizeof( $rangos );
    if( $size_rangos > 0 && $dif_minuto > 0 )
    {
      $ran_ini = $rangos[0]['min_ranini'];
      $ran_fin = $rangos[$size_rangos-1]['min_ranfin'];
      if( $dif_minuto < $ran_ini )
        $to_return[2] = $rangos[0]['cod_colorx'];
      elseif( $dif_minuto > $ran_fin )
        $to_return[2] = $rangos[$size_rangos-1]['cod_colorx'];
      else
      {
        foreach( $rangos as $row )
        {
          if( $dif_minuto <= $row['min_ranfin'] && $dif_minuto >= $row['min_ranini'] )
            $to_return[2] = $row['cod_colorx'];
        }
      }
      
    }
    else
    {
      $to_return[2] = 'EEEEEE';
    }
    return $to_return;
  }
  
  function diferenciaMinutosFecha( $fecha_inicio, $fecha_fin ) 
  {
    $time_inicio = strtotime( $fecha_inicio );
    $time_fin = strtotime( $fecha_fin );
    
    return round( ( $time_inicio - $time_fin ) / 60 );
    
    // if ( $time_inicio > $time_fin ) {
      // return (int)abs( round( ( $time_inicio - $time_fin ) / 60 ) );
    // }
    // else {
      // return (int)abs( round( ( $time_fin - $time_inicio ) / 60 ) );
    // }
  }
  
  //------------------------------------------------------------------------------------
  //@method: GetTranspTipser                                                           |
  //@brief : Retorna la Información de los tipos de servicio según la transportadora   |
  //------------------------------------------------------------------------------------
  function GetTranspTipser( $cod_transp ) {
    $mSql  = "SELECT e1.num_consec, 
                     e1.cod_transp, 
                     e1.tie_contro, 
                     e1.tie_conurb, 
                     e1.cod_tipser, 
                     e2.nom_tipser 
                FROM ".BASE_DATOS.".tab_transp_tipser e1, 
                     ".BASE_DATOS.".tab_genera_tipser e2, 
                     ( SELECT MAX( num_consec ) AS max_consec 
                         FROM ".BASE_DATOS.".tab_transp_tipser 
                        WHERE cod_transp = '".$cod_transp."' ) e3 
               WHERE e1.cod_tipser = e2.cod_tipser 
                 AND e1.cod_transp = '".$cod_transp."' 
                 AND e1.num_consec = e3.max_consec ";
    $consul = new Consulta( $mSql, $this -> conexion );
    $matriz = $consul -> ret_matrix( 'a' );
    return $matriz[0];
  }
}

$_DATA = count($_POST) == 0 ? $_GET : $_POST;
$InformDespacSeguim = new InformDespacSeguim($this->conexion, $_DATA);

/*
$m[0] = array( 'name' => 'willy', 'age' => '30', 'city' => 'san bernardo' );
$m[1] = array( 'name' => 'smith', 'age' => '22', 'city' => 'Bogota' );
$m[2] = array( 'name' => 'jovo', 'age' => '25', 'city' => 'tulua' );
$m[3] = array( 'name' => 'hugh', 'age' => '24', 'city' => 'Girardot' );
$m[4] = array( 'name' => 'mick', 'age' => '27', 'city' => 'cucuta' );

echo '<pre>';
print_r( $m );
echo '</pre>';


echo '<pre>';
print_r( $InformDespacSeguim -> array_sort( $m, 'age' ) );
echo '</pre>';
*/

?>