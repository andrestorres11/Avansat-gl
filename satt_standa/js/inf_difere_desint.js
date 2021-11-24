/*! \file: inf_difere_desint
 *  \brief: JS para todas las acciones del modulo  Diferencia de Despachos.
 *  \author: Ing. Nelson Liberato
 *  \author: nelson.liberato@eltransporte.org
 *  \version: 1.0
 *  \date: 2021-07-08
 *  \bug: 
 *  \warning: 
 */


/*! \fn: llenarBoceto
 *  \brief: Llena el boceto del campo formulario
 *  \author: Ing. Nelson Liberato
 *  \date: 22/08/2016
 *  \date modified: dd/mm/aaaa
 *  \param: obj  object  objeto
 *  \return: 
 */
function generarInforme(obj) {
        
        var standa = $("#standaID").val();

        var attributes = 'Ajax=on&Option=informeGeral';
        attributes += "&cod_tercer=" + $("#cod_tercerID").val();
        
        $.ajax({
            url: "../" + standa + "/inform/inf_difere_desint.php",
            type: "POST",
            data: attributes,
            async: true,
            beforeSend: function() {
                BlocK('Cargando informacion...', true);
            },
            success: function(datos) {
              $("#infoID").html(datos).css({'background-color':'#EBF8E2'}).attr({'class':'cellInfo1'}).parent().css({'height':'auto'});
            },
            complete: function() {
                BlocK();
            }
        });
}


 
/*! \fn: verDespachos
 *  \brief: Mostrar popup para el detalle de despachos
 *  \author: Ing. Nelson Liberato
 *  \date: 22/08/2016
 *  \date modified: dd/mm/aaaa
 *  \param: obj  object  objeto
 *  \return: 
 */
function verDespachos(obj) {
    try { 

        var standa = $("#standaID").val();
        var attributes = 'Ajax=on&Option=informeDetallado';
        attributes += "&nom_tercer=" + obj;

          $.ajax({
            url: "../" + standa + "/inform/inf_difere_desint.php",
            type: "POST",
            data: attributes,
            async: true,
            beforeSend: function() {
                BlocK('Cargando informacion...', true);
            },
            success: function(datos) {
              $("#infoID").html(datos).css({'background-color':'#EBF8E2'}).attr({'class':'cellInfo1'}).parent().css({'height':'auto'});
            },
            complete: function() {
                BlocK();
            }
        });
    }
    catch(e)
    {
      console.log("Error Fuction llenarBoceto: " + e.message + "\nLine: " + e.lineNumber);
      return false;
    }
  }
 