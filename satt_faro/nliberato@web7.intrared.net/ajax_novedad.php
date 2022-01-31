<?php
/*! \file: ajax.novedad.php
 *  \brief: ajax para procesos de novedades
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 25/05/2015
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */

ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);

session_start();

/*! \class: Ajax
 *  \brief: Clase para procesos de novedades
 */
class Ajax
{
	var $conexion  = NULL;

	function Ajax()
	{
		@require( "../constantes.inc" );
		@require( "lib/conexion.inc" );
		$this -> conexion = new Conexion( HOST, USUARIO, CLAVE, BASE_DATOS, BD_STANDA );

		switch( $_REQUEST["Case"] )
		{
			case 'showCamara':
				Ajax::showCamara();
				break;

			case 'SoluciRecome':
				Ajax::SoluciRecome();
				break;

			case 'UpdSoluciRecome':
				Ajax::UpdSoluciRecome();
				break;

			default:
				echo "me jodio";
				break;
		}
	}

	/*! \fn: Style
	 *  \brief: Estilos
	 *  \author: Ing. Fabian Salinas
	 *	\date: 25/05/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	function Style()
	{
  		echo "
  		<style>
			.contenedor{ 
				width: 350px; 
				float: left;
			}

			.titulo{ 
				font-size: 12pt; 
				font-weight: bold;
			}

			#camara, #foto{
				width: 320px;
				min-height: 240px;
				border: 1px solid #008000;
				float: left;
			}
		</style>";
	}

	/*! \fn: showCamara
	 *  \brief: Muestra la Camara
	 *  \author: Ing. Fabian Salinas
	 *	\date: 25/05/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	function showCamara()
	{
		Ajax::Style();

		$mHtml  = '<div id=botonera">';
		$mHtml .= 	'<input  class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" id=botonIniciar" type="button" value="Iniciar" onclick="Inicam(this)" />';
		$mHtml .= 	'<input  class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" id=botonDetener" type="button" value="Detener" onclick="Detcam()" />';
		$mHtml .= 	'<input  class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" id=botonFoto" type="button" value="Foto" onclick="Fotocam()" />';
		$mHtml .= 	'<input  class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" id=botonCargar" type="button" value = "Cargar" onclick="guardarFoto()" />';
		$mHtml .= '</div>';
		$mHtml .= '<div class="contenedor">';
		$mHtml .= 	'<div class="titulo">Cámara</div>';
		$mHtml .= 		'<video id="camara" autoplay controls></video>';
		$mHtml .= 	'</div>';
		$mHtml .= '<div class="contenedor">';
		$mHtml .= 	'<div class="titulo">Foto</div>';
		$mHtml .= 	'<canvas id="foto" ></canvas>';
		$mHtml .= '</div>';

		$mHtml .= '<input type="hidden" id="num_fotoxxID" name="num_fotoxx" value="'.$_REQUEST[num_fotoxx].'" />';

		echo $mHtml;
	}

	/*! \fn: SoluciRecome
	 *  \brief: Formulario para dar solucion a las recomendaciones del despacho
	 *  \author: Ing. Fabian Salinas
	 *	\date: 25/05/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	function SoluciRecome()
	{
		Ajax::Style();

		$mCodRecome = '';
		$mNumDespac = '';

		$mHtml  = '<form name="formSoluciRecome" id="formSoluciRecomeID" method="POST" action="?" >';
		$mHtml .= '<table width="100%" cellspacing="1" cellpadding="0" border="0">';

		$mHtml .=   '<tr>';
		$mHtml .=     '<th class="CellHead">#</th>';
		$mHtml .=     '<th class="CellHead">Obligatorio</th>';
		$mHtml .=     '<th class="CellHead">Recomendaci&oacute;n</th>';
		$mHtml .=     '<th class="CellHead">Respuesta</th>';
		$mHtml .=   '</tr>';

		$mCon = 0;
		for ($i=0; $i < sizeof($_REQUEST[num_despac]); $i++) {
			$mNumDespac .= $mNumDespac == '' ? $_REQUEST[num_despac][$i] : '|'.$_REQUEST[num_despac][$i] ;
		}

		foreach ($_SESSION[RecomeAsigna] as $row) 
		{
			$mCodRecome .= $mCodRecome == '' ? $row[cod_recome] : '|'.$row[cod_recome];
			$mHtml .= '<tr>';
			$mHtml .=   '<td class="cellInfo" align="center" >'.($mCon+1).'</td>';
			$mHtml .=   '<td class="cellInfo" align="center" >'.( $row[ind_requer] == 0 ? 'NO' : 'SI' ).'</td>';
			$mHtml .=   '<td class="cellInfo">'.$row[des_texto].'</td>';
			$mHtml .=   '<td class="cellInfo">
							<span id="mInput'.$mCon.'ID" >
								'.$row[htm_config].'
								<input id="InputsForm'.$mCon.'ID" type="hidden"  ind_requer="'.$row[ind_requer].'" num_condes="'.$row[num_condes].'" cod_contro="'.$row[cod_contro].'" cod_noveda="'.$row[cod_noveda].'" cod_rutasx="'.$row[cod_rutasx].'" cod_recome="'.$row[cod_recome].'" cod_tipoxx="'.$row[cod_tipoxx].'"  />
							</span>
						</td>';

			$mHtml .= '</tr>';
			$mCon ++;
		}

		$mHtml .= '<tr><td colspan="4" align="center"> <input class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="button" value="Aceptar" onclick="setSoluciRecome();" /> </td></tr>';

		$mHtml .= '<input type="hidden" name="dir_aplica" id="dir_aplicaID" value="'.$_REQUEST[standa].'" >';
		$mHtml .= '<input type="hidden" name="tot_inputs" id="tot_inputsID" value="'.$mCon.'" >';
		$mHtml .= '<input type="hidden" name="cod_recome" id="cod_recomeID" value="'.$mCodRecome.'" >';
		$mHtml .= '<input type="hidden" name="num_despac" id="num_despacID" value="'.$mNumDespac.'" >';

		$mHtml .= '</table>';
		$mHtml .= '</form>';

		echo $mHtml;
	}

	/*! \fn: UpdSoluciRecome
	 *  \brief: Guarda la solucion a las recomendaciones
	 *  \author: Ing. Fabian Salinas
	 *	\date: 25/05/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	function UpdSoluciRecome()
	{
		$mNumDespac = '';
		foreach ($_REQUEST[num_despac] as $value) {
			$mNumDespac .= $mNumDespac == '' ? $value : ','.$value;
		}
		foreach ($_REQUEST[data] as $row) 
		{
			$mSql = " UPDATE tab_recome_asigna 
						 SET obs_ejecuc = '$row[obs_ejecuc]', 
						 	 ind_ejecuc = '1', 
						 	 cod_noved2 = '71', 
						 	 usr_ejecut = '".$_SESSION[satt_movil][cod_usuari]."', 
						 	 fec_ejecut = NOW(), 
						 	 usr_modifi = '".$_SESSION[satt_movil][cod_usuari]."', 
						 	 fec_modifi = NOW() 
					   WHERE num_despac IN (".$mNumDespac.") 
						 AND cod_recome = '$row[cod_recome]' 
						 AND cod_contro = '$row[cod_contro]' ";
			$this -> conexion -> Start();
			$insercion = $this -> conexion -> Consultar( $mSql );
			if( $insercion )
				$this -> conexion -> Commit();
			
		}
	}

}

$ajax = new Ajax();

?>