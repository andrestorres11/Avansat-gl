/*! \file: par_autori_usuari.js
 *  \brief: JS para Seguridad > Autorizaciones Usuario 
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 22/02/2016
 *  \bug: 
 *  \warning: 
 */

/*! \fn: editAutoriUsuari
 *  \brief: Genera el ajax para solicitar el formulario de las autorizaciones del usuario
 *  \author: Ing. Fabian Salinas
 *  \date: 22/02/2016
 *  \date modified: dd/mm/aaaa
 *  \param: 
 *  \return: 
 */
function editAutoriUsuari(ind, obj) {
	try {
		var standa = $("#standaID").val();
		var attributes = "Ajax=on&Option=editAutoriUsuari";
		attributes += "&ind_option=" + ind;

		if( ind == 'update' )
			attributes += "&cod_consec=" + $(obj).parent().parent().children().html();

		LoadPopupJQNoButton('open', 'Registrar Autorizacion', 'auto', '50%', false, false, true, 'popupAutoriUsuID');
		var popup = $("#popupAutoriUsuID");

		$.ajax({
			url: "../" + standa + "/seguridad/par_autori_usuari.php",
			type: "POST",
			data: attributes,
			async: false,
			beforeSend: function() {
				popup.parent().children().children('.ui-dialog-titlebar-close').hide();
				popup.html("<center><img src=\"../" + standa + "/imagenes/ajax-loader.gif\"></center>");
			},
			success: function(data) {
				popup.html(data);
			}
		});
	} catch (e) {
		console.log("Error Function editAutoriUsuari: " + e.message + "\nLine: " + e.lineNumber);
		return false;
	}
}

/*! \fn: saveAutoriUsuari
 *  \brief: Solicita el ajax para guardar las autorizaciones del usuario
 *  \author: Ing. Fabian Salinas
 *  \date: 22/02/2016
 *  \date modified: dd/mm/aaaa
 *  \param: 
 *  \return: 
 */
function saveAutoriUsuari() {
	try {
		var cod_conusu = $("#popupAutoriUsuID #cod_conusuID").val();

		if( cod_conusu == '' ){
			alert( 'Por favor seleccione un usuario.' );
			return false;
		}

		var standa = $("#standaID").val();
		var popup = $("#popupAutoriUsuID");
		var attributes = "Ajax=on&Option=saveAutoriUsuari";
		attributes += "&cod_consec=" + $("#popupAutoriUsuID #cod_consecID").val();
		attributes += "&cod_conusu=" + cod_conusu;

		$("#popupAutoriUsuID input[type=checkbox]:checked").each(function(ind, obj) {
			attributes += "&" + $(this).attr('name') + "=1";
		});

		$.ajax({
			url: "../" + standa + "/seguridad/par_autori_usuari.php",
			type: "POST",
			data: attributes,
			async: false,
			beforeSend: function() {
				popup.html("<center><img src=\"../" + standa + "/imagenes/ajax-loader.gif\"></center>");
			},
			success: function(data) {
				popup.html(data);
				//closePopUp('popupAutoriUsuID');
			}
		});
	} catch (e) {
		console.log("Error Function saveAutoriUsuari: " + e.message + "\nLine: " + e.lineNumber);
		return false;
	}
}