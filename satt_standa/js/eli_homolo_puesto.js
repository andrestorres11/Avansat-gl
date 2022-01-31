/*! \file: eli_homolo_puesto.js
 *  \brief: js para eli_homolo_puesto.php
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 25/05/2015
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */

/*! \fn: VerifyData
 *  \brief: Verifica los campos con asterisco del formulario
 *  \author: Ing. Fabian Salinas
 *	\date: 25/05/2015
 *	\date modified: dia/mes/año
 *  \param: 
 *  \return: 
 */
function VerifyData()
{
	var checked = $("input[type='checkbox']:checked");

	if(checked.length <1){
		alert("Por Favor Seleccione por lo Menos un Puesto de Control.");
		return false;
	}else{
		var conf = confirm("¿Esta Seguro que Desea Desactivar el Puesto de Control");
		if (conf == true) {
			$("#formularioID").submit();
		} else {
			return false;
		}
	}
}

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
	//Marca o Desmarca check
	$("input[name='marcarTodo']").click(function(){
		if( $(this).is( ":checked" )  ==  true  )
		{
			var con = 0;
			$("input[type='checkbox']").each(function(){
				if( con != 0){
					this.checked = true;
				}
				con ++;
			});
		}
		else
		{
			var con = 0;
			$("input[type='checkbox']").each(function(){
				if( con != 0){
					this.checked = false;
				}
				con ++;
			});
		}
	});

});