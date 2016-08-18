<?php
/*! \file: ajax_homolo_puesto.php
 *  \brief: Procesos de homologacion puestos de control
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 25/05/2015
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */

#ini_set('display_errors', true);
#error_reporting(E_ALL & ~E_NOTICE);

/*! \class: Ajax
 *  \brief: Ajax para los procesos de homologacion de puestos de control
 */
class Ajax
{
	var $conexion  = NULL;

	function Ajax()
	{
		@include( "../lib/ajax.inc" );

		$this -> conexion = $AjaxConnection;

    switch( $_AJAX["Case"] )
    {
    	case 'PcHijos':
    		Ajax::PcHijos();
    		break;
    }

	}

  /*! \fn: Style
   *  \brief: Estilos para las tablas
   *  \author: Ing. Fabian Salinas
   *  \date: 25/05/2015
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return:
   */
  function Style()
  {
    echo "  <style>
            .cellHead
            {
              padding:5px 10px;
              background: -webkit-gradient(linear, left top, left bottom, from( #009617 ), to( #00661b )); 
              background: -moz-linear-gradient(top, #009617, #00661b ); 
              background-image: -ms-linear-gradient(top, #00660f 0%,#00660f 100%); 
              background-image: linear-gradient(to bottom, #00660f 0%,#00660f 100%); 
              filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#03ad39', endColorstr='#00660f',GradientType=0 );
              color:#fff;
              text-align:center;
            }
            
            .footer
            {
              padding:5px 10px;
              background: -webkit-gradient(linear, left top, left bottom, from( #009617 ), to( #00661b )); 
              background: -moz-linear-gradient(top, #009617, #00661b ); 
              background-image: -ms-linear-gradient(top, #00660f 0%,#00660f 100%); 
              background-image: linear-gradient(to bottom, #00660f 0%,#00660f 100%); 
              filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#03ad39', endColorstr='#00660f',GradientType=0 );
              color:#fff;
              text-align:left;
            }

            .cellHead2
            {
              padding:5px 10px;
              background: #03ad39;
              background: -webkit-gradient(linear, left top, left bottom, from( #03ad39 ), to( #00660f )); 
              background: -moz-linear-gradient(top, #03ad39, #00660f ); 
              background-image: -ms-linear-gradient(top, #00660f 0%,#00660f 100%); 
              background-image: linear-gradient(to bottom, #00660f 0%,#00660f 100%); 
              filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#03ad39', endColorstr='#00660f',GradientType=0 );
              color:#fff;
              text-align:right;
            }

            tr.row:hover  td
            {
              background-color: #9ad9ae;
            }
            .cellInfo
            {
              padding:5px 10px;
              background-color:#fff;
              border:1px solid #ccc;
            }

            .cellInfo2
            {
              padding:5px 10px;
              background-color:#9ad9ae;
              border:1px solid #ccc;
            }

            .label
            {
              font-size:12px;
              font-weight:bold;
            }

            .select
            {
              background-color:#fff;
              border:1px solid #009617;
            }

            .boton
            {
              background: -webkit-gradient(linear, left top, left bottom, from( #009617 ), to( #00661b )); 
              background: -moz-linear-gradient(top, #009617, #00661b ); 
              filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#009617', endColorstr='#00661b');
              color:#fff;
              border:1px solid #fff;
              padding:3px 15px;
              -webkit-border-radius: 5px;
              -moz-border-radius: 5px;
              border-radius: 5px;
            }

            .boton:hover
            {
              background:#fff;
              color:#00661b;
              border:1px solid #00661b;
              cursor:pointer;
            }
            
            .StyleDIV
            {
              min-height: 300px; 
            }
    </style>";
  }

  /*! \fn: PcHijos
   *  \brief: Lista los puestos de control hijos segun el padre
   *  \author: Ing. Fabian Salinas
   *  \date: 25/05/2015
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return:
   */
	function PcHijos()
  {
    Ajax::Style();
    
    #$mArrayDataPadre = Ajax::getPueControl($_REQUEST[cod_contro], '1' );
    $mArrayDataHijox = Ajax::getPueControl($_REQUEST[cod_contro], '0' );

    $mHtml  = '<table width="100%" cellspacing="1" cellpadding="0" border="0">';

    $mHtml .=   '<tr>';
    $mHtml .=     '<th class="CellHead">C&oacute;digo</th>';
    $mHtml .=     '<th class="CellHead">Descripci&oacute;n</th>';
    $mHtml .=     '<th class="CellHead">Estado</th>';
    $mHtml .=     '<th class="CellHead">Direcci&oacute;n</th>';
    $mHtml .=     '<th class="CellHead">Tel&eacute;fono</th>';
    $mHtml .=     '<th class="CellHead">Encargado</th>';
    $mHtml .=     '<th class="CellHead">Puesto</th>';
    $mHtml .=   '</tr>';

    foreach ($mArrayDataHijox as $row) {
      $mHtml .= '<tr>';
      $mHtml .=   '<td class="cellInfo">'.$row[0].'&nbsp;</td>';
      $mHtml .=   '<td class="cellInfo">'.$row[1].'&nbsp;</td>';
      $mHtml .=   '<td class="cellInfo">'.$row[6].'&nbsp;</td>';
      $mHtml .=   '<td class="cellInfo">'.$row[2].'&nbsp;</td>';
      $mHtml .=   '<td class="cellInfo">'.$row[3].'&nbsp;</td>';
      $mHtml .=   '<td class="cellInfo">'.$row[4].'&nbsp;</td>';
      $mHtml .=   '<td class="cellInfo">'.$row[5].'&nbsp;</td>';
      $mHtml .= '</tr>';
    }

    $mHtml .= '</table>';

    echo $mHtml;
  }

  /*! \fn: getPueControl
   *  \brief: Trae los puestos de control
   *  \author: Ing. Fabian Salinas
   *  \date: 25/05/2015
   *  \date modified: dia/mes/año
   *  \param: codContro, indicador Puesto padre 1 o hijo 0
   *  \return: matriz informacion puesto de control
   */
  function getPueControl($codContro, $ind)
  {
    $mSql = "        SELECT a.cod_contro, a.nom_contro, a.dir_contro, 
                            a.tel_contro, a.nom_encarg, 
                            if(a.ind_virtua = '0','Fisico','Virtual'), 
                            if(a.ind_estado = '1', 'Activo', 'Inactivo'),
                            if(a.ind_urbano = '1',' - Urbano',''), a.cod_colorx 
                       FROM ".BASE_DATOS.".tab_genera_contro a ";
    if($ind == 0){ #Puestos de control Hijos
      $mSql .= " INNER JOIN ".BASE_DATOS.".tab_homolo_pcxeal b 
                         ON a.cod_contro = b.cod_homolo 
                      WHERE b.cod_contro = '$codContro' ";
    }elseif( $ind == 1){ #Puestos de control padres
      $mSql .= "      WHERE a.cod_contro = '$codContro' 
                        AND a.ind_estado = '1' ";
    }
    $mSql .= "          AND a.ind_pcpadr = '$ind' 
                   ORDER BY a.cod_contro ";
    $mConsult = new Consulta($mSql, $this -> conexion);
    return $mResult = $mConsult -> ret_matrix('i');
  }


}

$ajax = new Ajax();

?>