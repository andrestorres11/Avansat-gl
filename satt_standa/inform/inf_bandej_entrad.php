<?php

/* * *****************************************************************************************************
 * @class: InformBandejEntrad                                                                           *
 * @company: Intrared.net                                                                               *
 * @author: Christiam Barrera( The Messias, The Only One, The Choosen One, The Unique )                 *
 * @date: 2012-05-31                                                                                    *
 * @brief: Clase que dibuja la Bandeja de Entrada de los Despachos en Transito según Clase DespacTransi *
 * ***************************************************************************************************** */

class InformBandejEntrad
{

    var $conexion = NULL;
    var $genera_alarma = NULL;
    var $cod_perfil = NULL;
    var $cod_usuari = NULL;
    var $superusuario = NULL;
    var $tmp_transp = NULL;
    var $cod_serlis = 33020;
    var $tot_despac = array();

    //----------------------------------------------------------------------------------------------------
    //@method: InformBandejEntrad                                                                        |
    //@brief : Constructor de la Clase                                                                   |
    //----------------------------------------------------------------------------------------------------
    function __construct( $conexion, $mData )
    {
     
      //----------------------------
      $this -> conexion = $conexion;
      
      $this->genera_alarma = $this->GetGeneraAlarma();
      //----------------------------------------------------
      $datos_usuario = $_SESSION[datos_usuario];
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
        $html .= '<script type="text/javascript" src="../' . DIR_APLICA_CENTRAL . '/js/inf_bandej_entrad.js"></script>';
        $html .= '<script type="text/javascript" src="../' . DIR_APLICA_CENTRAL . '/js/jquery.blockUI.js"></script>';
        $html .= '<script type="text/javascript" src="../' . DIR_APLICA_CENTRAL . '/js/functions.js"></script>';
        $html .= '<form action="index.php?" method="post" name="frm_report" id="frm_reportID">';
        $html .= '<div id="informDIV">';
        $html .= $this->DrawReport($mData);
        $html .= '</div>';
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
        $html .= '.classHead{ border-right: 1px solid #ffffff; border-bottom: 1px solid #ffffff; padding:5px 15px 5px 15px; color:#333333; }';
        $html .= '.classTotal{ border-right: 1px solid #eeeeee; border-bottom: 1px solid #eeeeee; padding:5px 15px 5px 15px; color:#333333; background:#ffffff; }';
        $html .= '.classCell{ border-right: 1px solid #ffffff; border-bottom: 1px solid #ffffff; padding:2px 5px 2px 5px; color:#444444; }';
        $html .= '.classList{ background:#eeeeee; font-family:Arial; font-size:11px; color:#444444; font-weight:bold; padding:0px 5px 0px 5px; }';
        for ($h = 0, $size_alarma = count($this->genera_alarma); $h < $size_alarma; $h++)
        {
            $html .= '.bg' . $h . '{ background:#' . $this->genera_alarma[$h]['cod_colorx'] . '; }';
            $html .= '.co' . $h . '{ color:#' . $this->genera_alarma[$h]['cod_colorx'] . '; font-weight:bold; }';
        }
        $html .= '.bgEAL{ background:#7FFF99; }';
        $html .= '.cp{ cursor:pointer; }';
        $html .= '.classLink { background:#eeeeee; font-family:Arial; font-size:11px; color:#006600; font-weight:bold; }';
        $html .= '.classLinkTotal{ font-family:Arial; font-size:11px; color:#bb0000; font-weight:bold; text-decoration:none; }';
        $html .= '.classLinkTotal:hover{ font-family:Arial; font-size:11px; color:#111111; font-weight:bold; text-decoration:underline; }';
        $html .= '.classMenu{ border-left: 1px solid #ffffff; border-right: 1px solid #ffffff; background:#009900; cursor:pointer; width:7px; }';
        $html .= '.bt{ border-top: 1px solid #ffffff; }';
        $html .= '</style>';
        return $html;
    }

    //-------------------------------------------------------------------------------------------------------------------|
    //@method: set_link                                                                                                  |
    //@brief : Retorna el html de los hipervinculos de las cantidades de los despachos                                   |                                                                |
    //-------------------------------------------------------------------------------------------------------------------|
    function set_link($val_link, $data = NULL, $all = NULL )
    {
        if ( $val_link == '-' )
            return '-';
        $a = '<a class="classLink" href="index.php?';
        $a .= 'cod_servic=' . $this -> cod_serlis;
        $a .= '&window=central';
        $a .= '&atras=si';
        if ( $all == NULL ) :
          $a .= '&transp=' . $this -> tmp_transp;
        endif;
        $a .= $data;
        $a .= '"';
        $a .= ' target="centralFrame">';
        $a .= $val_link;
        $a .= '</a>';
        return $a;
    }
    
    //-------------------------------------------------------------------------------------------------------------------|
    //@method: set_link                                                                                                  |
    //@brief : Retorna el html de los hipervinculos de los Totales                                                       |        
    //-------------------------------------------------------------------------------------------------------------------|
    function set_link_totals($val_link, $data = NULL )
    {
        if ( $val_link == '-' )
            return '-';
        $a = '<a class="classLinkTotal" href="index.php?';
        $a .= 'cod_servic=' . $this -> cod_serlis;
        $a .= '&window=central';
        $a .= '&atras=si';
        $a .= $data;
        $a .= '"';
        $a .= ' target="centralFrame">';
        $a .= $val_link;
        $a .= '</a>';
        return $a;
    }

    //----------------------------------------------------------------------------------------------------------------------------------|
    //@method: DrawReport                                                                                                                 |
    //@brief : Retorna el html de la tabla principal del informe en la cual se realiza la instancia del objeto de la clase DespacTransi |                                 |                                                                |
    //----------------------------------------------------------------------------------------------------------------------------------|
    function DrawReport($mData)
    {
        //-----------------------------------------------------------------------------
        include_once( '../' . DIR_APLICA_CENTRAL . '/inform/class_despac_transi.php' );
        //-----------------------------------------------------------------------------
        $obj_destra = new DespacTransi($this->conexion, $mData);
        //-----------------------------------------------------------------------------
        $_REPORT = $obj_destra->GetDespacTransiReport();
        $size_report = count($_REPORT);
        
        /*echo "<hr><pre>";
        print_r( $_REPORT );
        echo "<pre>";*/
        //-----------------------------------------------------------------------------
        $_TIPSER = $this->GetGeneraTipser();
        $size_tipser = sizeof($_TIPSER);
        //-----------------------------------------------------------------------------
        $html = NULL;
        $html .= $this->DrawStyle();
        $html .= '<table class="classTable" align="center" width="100%" cellspacing="0" cellpadding="0">';
        $html .= '<tr>';
        
		$html .= '<tr>';
		
		$html .= '<th nowrap colspan="3" align="right" style="background:#009900; color:#ffffff; padding:2px 5px 2px 5px;">';
		$html .= '<label>Celular Conductor:</label>';
		$html .= '</th>';
		$html .= '<th nowrap colspan="1" align="left" style="background:#009900; color:#ffffff; padding:2px 5px 2px 5px;">';
		$html .= '<input type="text" style="border:1px solid transparent; font-family:Arial; font-size:11px; color:#009900; font-weight:bold;" maxlength="10" size="10" onkeypress="return NumericInput( event );" onblur="FormatNumericInput( this ); if( !this.value ) return false; if( this.value.length == 10){location.href=\'index.php?window=central&cod_servic=3302&menant=3302&opcion=8&celu=\'+this.value;}else{alert(\'El Teléfono Celular del Conductor debe tener 10 números.\'); this.value=\'\'; this.focus();}" />';
		$html .= '</th>';
		
		$html .= '<th nowrap colspan="3" align="right" style="background:#009900; color:#ffffff; padding:2px 5px 2px 5px;">';
		$html .= '<label>Placa:</label>';
		$html .= '</th>';
		$html .= '<th nowrap colspan="1" align="left" style="background:#009900; color:#ffffff; padding:2px 5px 2px 5px;">';
		$html .= '<input type="text" style="border:1px solid transparent; font-family:Arial; font-size:11px; color:#009900; font-weight:bold;" maxlength="10" size="10" onblur=" 
		
		
		if( !this.value ) 
			return false; 
		
		if( this.value.length == 6 )
		{
			location.href=\'index.php?window=central&cod_servic=3302&menant=3302&opcion=8&placa=\'+this.value;
		}
		else
		{
			alert(\'La Placa no es valida.\'); 
			this.value=\'\'; 
			this.focus();
		}" />';
		$html .= '</th>';
		
		$html .= '</tr>';
		
		
		
		
        //--------------------------
        $rowspan = $size_report + 3;
        //--------------------------
        $html .= '<th nowrap class="classMenu bt" align="center" rowspan="' . $rowspan . '" onclick="Hide();">&nbsp;</th>';
        $html .= '<th nowrap class="classHead bt" align="center">NO.</th>';
        $html .= '<th nowrap class="classHead bt" align="center">';
       
        
		for ($s = 0; $s < $size_tipser; $s++)
        {
           $checked = ""; 
			
			if( $_POST[servic][$s] == $_TIPSER[$s]['cod_tipser'] )
				$checked = " checked "; 
			
			$html .= $_TIPSER[$s]['nom_tipser'] . '<input type=checkbox value="' . $_TIPSER[$s]['cod_tipser'] . '" '.$checked.' name="servic['.$s.']" id="servic_'.$s.'" >&nbsp;';
        }
		
        //$html .= '</select>';
        $html .= '</th>';

        $html .= '<th nowrap class="classHead bt" align="center">EMPRESA</th>';
        $html .= '<th nowrap class="classHead bt" align="center">NO. DESPACHOS</th>';
        $html .= '<th nowrap class="classHead bt" align="center">SIN RETRASO</th>';

        for ($h = 0, $size_alarma = count($this->genera_alarma); $h < $size_alarma; $h++)
        {
            //------------------------------------
            $_GENALA = $this->genera_alarma[$h];
            //------------------------------------
            $nom_alarma[$h] = $_GENALA['nom_alarma'] . ' - ' . $_GENALA['can_tiempo'];
            $html .= '<th nowrap class="classHead bt bg' . $h . '" align="center">' . $nom_alarma[$h] . '</th>';
        }
        if ($this->superusuario)
        {
            $html .= '<th nowrap class="classHead bt bgEAL" align="center">EAL</th>';
        }
        $html .= '<th nowrap class="classHead bt" align="center">FUERA DE PLATAFORMA</th>';
        $html .= '<th nowrap class="classHead bt" align="center">POR LLEGADA</th>';
        $html .= '<th nowrap class="classHead bt" align="center">A CARGO EMPRESA</th>';
        $html .= '<th nowrap class="classHead bt" align="center">NOVEDAD ESPECIAL</th>';
        if ($this->superusuario)
        {
            //----------------------------------------
            $_ASIGNA = $this->GetListSuperUsuario();
            $size_asigna = sizeof($_ASIGNA);
            //----------------------------------------
            $html .= '<th nowrap class="classHead bt" align="center">';
            $html .= '<select class="classList" name="usr_asigna" id="usr_asignaID">';
            $html .= '<option value="">USUARIO ASIGNADO</option>';
            for ($a = 0; $a < $size_asigna; $a++)
            {
                $selected = $mData['usr_asigna'] == $_ASIGNA[$a][0] ? 'selected="selected"' : NULL;
                $html .= '<option value="' . $_ASIGNA[$a][0] . '" ' . $selected . '>' . $_ASIGNA[$a][0] . '</option>';
            }
            $html .= '</select>';
            $html .= '</th>';
        }
        $html .= '</tr>';
        //----------------------------
        $html .= '<totals>|</totals>';
        //--------------------------------------
        if ($size_report == 0)
        {
            $colspan = $this->superusuario ? 14 : 12;
            $html .= '<tr>';
            $html .= '<th nowrap class="classCell" align="center" colspan="' . $colspan . '">NO SE ENCONTRARON DESPACHOS</th>';
            $html .= '</tr>';
        }
        else
        {
            $i = 1;


            foreach ($_REPORT as $row)
            {
                $nom_tipser = $row['nom_tipser'];
                $cod_transp = $row['cod_transp'];
                $nom_transp = $row['nom_transp'];
                $can_despac = $row['can_despac'];
                $can_sinret = $row['can_sinret'] ? $row['can_sinret'] : '-';
                $can_penlle = $row['can_penlle'] ? $row['can_penlle'] : '-';
                $can_alaEAL = $row['can_alaEAL'] ? $row['can_alaEAL'] : '-';
                $can_fuepla = $row['can_fuepla'] ? $row['can_fuepla'] : '-';
                $can_defini = $row['can_defini'] ? $row['can_defini'] : '-';
                $can_especi = $row['can_especi'] ? $row['can_especi'] : '-';
                $usr_asigna = $row['usr_asigna'];

                $this->tot_despac['can_despac'] += $row['can_despac'];
                $this->tot_despac['can_sinret'] += $row['can_sinret'];
                $this->tot_despac['can_penlle'] += $row['can_penlle'];

                $this->tot_despac['can_fuepla'] += $row['can_fuepla'];
                $this->tot_despac['can_defini'] += $row['can_defini'];
                $this->tot_despac['can_especi'] += $row['can_especi'];
                //--------------------------------
                $this->tmp_transp = $cod_transp;
                //--------------------------------

                $can_despac = $this->set_link($can_despac, '&tip_alarma=can_despac&totregif=' . $can_despac);
                $can_sinret = $this->set_link($can_sinret, '&tip_alarma=can_sinret&totregif=' . $can_sinret . '&defini=0&alacla=S');
                $can_alaEAL = $this->set_link($can_alaEAL, '&tip_alarma=can_alaEAL&EAL=1');
                $can_fuepla = $this->set_link($can_fuepla, '&tip_alarma=can_fuepla&FUEPLA=1');
                $can_penlle = $this->set_link($can_penlle, '&tip_alarma=can_penlle&totregif=' . $can_penlle . '&alacla=L');
                $can_defini = $this->set_link($can_defini, '&tip_alarma=can_defini&defini=1');
                $can_especi = $this->set_link($can_especi, '&tip_alarma=can_especi&defini=2');


                $line = NULL;
                $line .= '<tr onclick="this.style.background=this.style.background==\'#ceecf5\'?\'#eeeeee\':\'#ceecf5\';">';
                $line .= '<th nowrap class="classCell" align="left">' . $i . '</th>';
                $line .= '<td nowrap class="classCell" align="left">' . $nom_tipser . '</td>';
                $line .= '<td nowrap class="classCell" align="left" title="EMPRESAS">' . $nom_transp . '</td>';
                $line .= '<td nowrap class="classCell" align="center" title="NO. DESPACHOS">' . $can_despac . '</td>';
                $line .= '<td nowrap class="classCell" align="center" title="SIN RETRASO">' . $can_sinret . '</td>';
                for ($h = 0, $size_alarma = count($this->genera_alarma); $h < $size_alarma; $h++)
                {
                    //------------------------------------
                    $_GENALA = $this -> genera_alarma[$h];
                    //------------------------------------
                    $can_tiempo = $row['can_' . $_GENALA['can_tiempo'] . 'mins'] ? $row['can_' . $_GENALA['can_tiempo'] . 'mins'] : '-';
                    $can_tiempo = $this->set_link($can_tiempo, '&tip_alarma=can_' . $_GENALA['can_tiempo'] . 'mins&totregif=' . $can_tiempo . '&defini=0&alacla=' . ( $h + 1 ));

                    $line .= '<td nowrap class="classCell" align="center" title="' . $nom_alarma[$h] . '">' . $can_tiempo . '</td>';
                  
                    $this->tot_despac['can_alarma'][$h] += $row['can_' . $_GENALA['can_tiempo'] . 'mins'];
                      
                    
                }
                if ($this->superusuario)
                {
                    $line .= '<td nowrap class="classCell" align="center" title="EAL">' . $can_alaEAL . '</td>';
                    $this->tot_despac['can_alaEAL'] += $row['can_alaEAL'];
                }
                $line .= '<td nowrap class="classCell" align="center" title="FUERA DE PLATAFORMA">' . $can_fuepla . '</td>';
                $line .= '<td nowrap class="classCell" align="center" title="PENDIENTES POR LLEGAR">' . $can_penlle . '</td>';
                $line .= '<td nowrap class="classCell" align="center" title="A CARGO EMPRESA">' . $can_defini . '</td>';
                $line .= '<td nowrap class="classCell" align="center" title="NOVEDAD ESPECIAL">' . $can_especi . '</td>';
                if ($this->superusuario)
                {
                    $line .= '<td nowrap class="classCell" align="left" title="USUARIO ASIGNADO">' . $usr_asigna . '</td>';
                }
                $line .= '</tr>';
                //-------------
                $html .= $line;
                //-------------
                $i++;
            }
        }
        $html .= '<totals>|</totals>';

        $html .= '</table>';

        $tota = NULL;
        $tota .= '<tr>';
        $tota .= '<th nowrap class="classTotal" align="right" colspan="3">TOTALES:</th>';
        
      
       
        $tota .= '<th nowrap class="classTotal" align="center">' . $this -> set_link_totals( $this -> tot_despac['can_despac'], '&tip_alarma=can_despac&totregif=' . $this -> tot_despac['can_despac'] ) . '</th>';
        $tota .= '<th nowrap class="classTotal" align="center">' . $this -> set_link_totals( $this -> tot_despac['can_sinret'], '&tip_alarma=can_sinret&totregif=' . $this -> tot_despac['can_sinret'] ) . '</th>';
        
        /*
        echo '<pre>';
        print_r( $this -> tot_despac );
        echo '</pre>';
        */
        
        $w = 0;
        foreach ( $this -> tot_despac['can_alarma'] as $tot_tiempo )
        {
          //------------------------------------
          $_GENALA = $this -> genera_alarma[$w];
		  
		  if( $_POST[servic] )
			$fil_servic = "&tip_servic=".implode( ".", $_POST[servic] );	
          //------------------------------------
          //$tota .= '<th nowrap class="classTotal" align="center">' . $tot_tiempo . '</th>';
          $tota .= '<th nowrap class="classTotal" align="center">' . $this -> set_link_totals( $tot_tiempo, '&tip_alarma=can_' . $_GENALA['can_tiempo'] . 'mins&despac&totregif=' . $tot_tiempo . $fil_servic ) . '</th>';
        
          $w ++;
        }
        if ( $this -> superusuario )
        {
          //$tota .= '<th nowrap class="classTotal" align="center">' . $this -> tot_despac['can_alaEAL'] . '</th>';
          $tota .= '<th nowrap class="classTotal" align="center">' . $this -> set_link_totals( $this -> tot_despac['can_alaEAL'], '&tip_alarma=can_alaEAL&totregif=' . $this -> tot_despac['can_alaEAL'] ) . '</th>';
        }
        
        $tota .= '<th nowrap class="classTotal" align="center">' . $this -> set_link_totals( $this -> tot_despac['can_fuepla'], '&tip_alarma=can_fuepla&totregif=' . $this -> tot_despac['can_fuepla'] ) . '</th>';
        $tota .= '<th nowrap class="classTotal" align="center">' . $this -> set_link_totals( $this -> tot_despac['can_penlle'], '&tip_alarma=can_penlle&totregif=' . $this -> tot_despac['can_penlle'] ) . '</th>';
        $tota .= '<th nowrap class="classTotal" align="center">' . $this -> set_link_totals( $this -> tot_despac['can_defini'], '&tip_alarma=can_defini&totregif=' . $this -> tot_despac['can_defini'] ) . '</th>';
        $tota .= '<th nowrap class="classTotal" align="center">' . $this -> set_link_totals( $this -> tot_despac['can_especi'], '&tip_alarma=can_especi&totregif=' . $this -> tot_despac['can_especi'] ) . '</th>';
        if ( $this -> superusuario )
        {
          $tota .= '<th nowrap class="classTotal" align="center">&nbsp;</th>';
        }  
        $tota .= '</tr>';

        if ($size_report == 0)
        {
            $tota = NULL;
        }
        //---------------------------------------------------------
        $html = str_replace('<totals>|</totals>', $tota, $html);
        //---------------------------------------------------------
        return $html;
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
        $_SUPER[] = COD_PERFIL_DIRECTOR;
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

}

$_DATA = count($_POST) == 0 ? $_GET : $_POST;
$InformBandejEntrad = new InformBandejEntrad( $this -> conexion, $_DATA );
?>