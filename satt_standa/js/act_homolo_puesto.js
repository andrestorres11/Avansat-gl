/*! \file: act_homolo_puesto.js
 *  \brief: js para act_homolo_puesto.php
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
	var cod_contro = $("#cod_controID");
	var checked = $("input[type='checkbox']:checked");

	if( !cod_contro.val() )
	{
		alert("Por Favor Seleccione un Puesto de Control Padre.");
		return false;
	}else if(checked.length <1){
		alert("Por Favor Seleccione por lo Menos un Puesto de Control Hijo.");
		return false;
	}else{
		$("#formularioID").submit();
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

	//Filtro de puestos de Control Hijos
	$("#cod_controID").change(function(){
		$("#opcionID").val("2");
		$("#formularioID").submit();
	});
});