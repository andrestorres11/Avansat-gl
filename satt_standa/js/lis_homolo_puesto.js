/*! \file: lis_homolo_puesto.js
 *  \brief: js para lis_homolo_puesto.php
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 25/05/2015
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */

/*! \fn: showListChildren
 *  \brief: Muestra el PopUp con el listado de los puestos de control Hijos
 *  \author: Ing. Fabian Salinas
 *	\date: 25/05/2015
 *	\date modified: dia/mes/año
 *  \param: codContro (Padre)
 *  \return:
 */
function showListChildren( cod_contro )
{
	try{
		var fStandar = $("input[name=standa]");
		var atributes  = 'Ajax=on&Case=PcHijos';
    		atributes += '&cod_contro=' + cod_contro ;

		//Carga Popup
		LoadPopup('open');
			$.ajax({
				url: "../"+ fStandar.val() +"/homolo/ajax_homolo_puesto.php",
				type: "POST",
				data: atributes,
				beforeSend: function(){
					$("#FormContacID").html("<center>Cargando Formulario...</center>");
				},
				success: function(data){
					$("#FormContacID").html(data);
					CenterDIV();
				} 
			});
	}
	catch(e)
	{
		console.log( "Error Función showListChildren: "+e.message+"\nLine: "+Error.lineNumber );
    	return false;
	}
}

/*! \fn: CenterDiv
 *  \brief: Centra el PopUp
 *  \author: Ing. Fabian Salinas
 *	\date: 25/05/2025
 *	\date modified: dia/mes/año
 *  \param: 
 *  \return:
 */
function CenterDIV()
{
	var WindowH = $(window).width();
	var popupH =  $('.ui-dialog').outerWidth();
	var Left = ((WindowH - popupH) / 2);
	$(".ui-dialog").css({"width": ($(window).width() - 50 )+"px" , "left":"10px", top : "200px"});
}

/*! \fn: LoadPopup
 *  \brief: Carga el PopUp
 *  \author: Ing. Fabian Salinas
 *	\date: 25/05/2015
 *	\date modified: dia/mes/año
 *  \param: type
 *  \return:
 */
function LoadPopup( type )
{
 try
 {
   if(type == 'open')
   {

     $('<div id="FormContacID"><center>Cargando...</center></div>').dialog({
       width : 100,
       heigth: 50,
       modal: true,
       closeOnEscape: false,
       resizable: false,
       draggable: false,
       close: function(){
         $("#FormContacID").dialog("destroy").remove();
       },
       buttons:{
         Cerrar:function(){
           LoadPopup( "close" );
         }
       }
     });
   }
   else
   {
     $("#FormContacID").dialog("destroy").remove();
   }

 }
 catch(e)
 {
   alert("Error en:ShowFormContac "+e.message+"\nLine: "+e.lineNumber);
 }
}