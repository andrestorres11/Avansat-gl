<?php 

 
/*! \class: AjaxFotos
*  \brief: clase ajax de las fotos 
*/
class AjaxFotos
{
	
	function __construct($datos)
	{ 
		if($datos['AJAX'] == 'on'){

 			$opcion = $datos['op']; 
 			$data = $this -> $opcion($datos);
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
	function style()
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

		$this -> style();
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

}
if( $_REQUEST['AJAX'] == 'on' ){ 
	$clase = new AjaxFotos($_REQUEST);
}

?>