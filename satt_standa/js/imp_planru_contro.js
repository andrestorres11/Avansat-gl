/*! \fn: ready
 *  \brief: pinta el QR
 *  \author: Ing. Miguel Romero
 *	\date: dia/mes/año 
 */

$(document).ready(function(){
	$("[name='url_ubicac']").each(function(i,o){
		url = $(this).val();
		contenedor = $(this).parent().find("div").first(); 
		$(contenedor).qrcode({
		    "size": 100,
		    "color": "#3a3",
		    "text": url
		});
	});
});