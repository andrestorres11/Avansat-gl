/*! \file: ins_homolo_puesto2.js
 *  \brief: js para ins_homolo_puesto2.php
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
 $("body").ready(function(){
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

	//Filtro por ciudad
	$("select[name='cod_ciudad']").change(function(){
		$("#opcionID").val("2");
		$( "#formularioID" ).submit();
	});

	$("#nom_controID").blur(function(){
		if( $("#nom_controID").val().length > 2 ){
			$("#opcionID").val("2");
			$( "#formularioID" ).submit();
		}
	});

});

/*! \fn: validarForm
 *  \brief: Verifica los campos con asterisco del formulario
 *  \author: Ing. Fabian Salinas
 *	\date: 25/05/2015
 *	\date modified: dia/mes/año
 *  \param: 
 *  \return: 
 */
function validarForm()
{
	try
	{
		var cod_contro = $("select[name='cod_contro']");
		var valCheck = $("[name^='cod_PcHijo']:checked");

		if (cod_contro.val() == '')
			alert("Por Favor Seleccione un Puesto de Control Padre.");
		else if(valCheck.length < 1 )
			alert("Por Favor Seleccione por lo Menos un Puesto de Control Hijo.");
		else
			$("#formularioID").submit();
	}
	catch(e)
	{
		console.log( "Error Fuction validarForm: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}