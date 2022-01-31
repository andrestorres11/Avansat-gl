/*! \file: act_pcontr_pcontr.js
 *  \brief: js para act_pcontr_pcontr.php
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 25/05/2015
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */

/*! \fn: 
 *  \brief: procesos cuando el documento este cargado
 *  \author: Ing. Fabian Salinas
 *	\date: 25/05/2015
 *	\date modified: dia/mes/año
 *  \param: 
 *  \return:
 */
$("body").ready(function()
{
	//Filtro de Sentido vial
	$("#sentidoID").change(function(){
		var sentido = $("#sentidoID").val();

		if( sentido == 'S-N/N-S' || sentido == 'E-W/W-E' ){
			$("#sent1ID").attr('readonly', false);
			$("#sent2ID").attr('readonly', false);
		}else{
			$("#sent1ID").attr('readonly', false);
			$("#sent2ID").attr('readonly', true);
			$("#sent2ID").val("");
		}
	});


});