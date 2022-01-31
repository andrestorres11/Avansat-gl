/*! \fn: Registrar
 *  \brief: Valida los mamafokas datos del tormulario
 *  \author: Ing. Miguel Romero
 *	\date: 08/05/2015
 *	\date modified: 08/05/2015
 *  \return hace submit si todo se cumple
 */


function registrar(){


	var cod_cliente = $("input[name='cod_client']").val();

	
try{

		var nom_cliente = $("input[name='nom_client']").val();
		var flag = true;
		var message = "Revise Los siguientes campos\n"

		if(cod_cliente == '' || cod_cliente == null){
			message += "-Codigo\n";
			flag = false;
		}

		if(nom_cliente == '' || nom_cliente == null){
			message += "-Nombre\n";
			flag = false;
		}

		if(flag == false){
			alert(message);
		}

		else{

			if(confirm("Desea ingresar el Cliente: " + nom_cliente)){
				$("#opcionID").val(1);
				formulario = document.form_item;
	            formulario.submit();
			}
			
		}

	}
catch(e)
{
	console.log( "Error Fuction registrar: "+e.message+"\nLine: "+e.lineNumber );
	return false;
}
}

/*! \fn: justNumbers
 *  \brief: sulo deja digitar numeros
 *  \author: Ing. Miguel Romero
 *	\date: 08/05/2015
 *	\date modified: 08/05/2015
 *  \param: palabra
 *  \return palabra sin numeros
 */

function justNumbers(e)
{
	var keynum = window.event ? window.event.keyCode : e.which;
	if ((keynum == 8) || (keynum == 46))
		return true;	

	return /\d/.test(String.fromCharCode(keynum));
}