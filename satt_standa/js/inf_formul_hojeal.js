/*! \file: if_formul_formul
 *  \brief: JS para todas las acciones del modulo Hoja de Vida EAL 
 *  \author: Edward serrano
 *  \author: Edward.serrano@intrared.net
 *  \version: 1.0
 *  \date: 11/07/2017
 *  \bug: 
 *  \warning: 
 */
 //variable global utilizada para validar los formularios
var validacion = true;
$(document).ready(function(){
    //inicio multiselet
    $("#nom_esferaID").multiselect().multiselectfilter();    
});

function getReporteGeneral()
{
    try 
    {   
        var standa = $("#standaID").val();
        var cod_contro = validateChekBox("nom_esferaID");
        if(cod_contro !=",'-'")
        {
            var mdata = "Ajax=on&opcion=getReporteGeneral&standa=" + standa +"&cod_contro="+cod_contro;
            $.ajax({
                url:"../" + standa + "/formul/inf_formul_hojeal.php",
                type:'POST',
                //dataType:'json',
                data: mdata,
                beforeSend: function() {
                    $("#report").html("");
                },
                success:function(data){
                    console.log(data);
                    $("#report").html(data);
                    $("#sec1").css({"height":"auto"});
                    /*if(data.resp == "ok")
                    {
                        PopupRespueta("Se "+(accion=="edit"?"edito":"almaceno")+" Correctamente la hoja de vida EAL.",true);
                    }
                    else
                    {
                        PopupRespueta("No se pudo realizar la solicitud, intente nuevamente.",true);
                    }*/
                }
            }); 
        }
        else
        {
            inc_alerta("nom_esferaID", "Campo requerido.");
        }
    } 
    catch (e) 
    {
        console.log("Error Function getReporteGeneral: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: validateChekBox
 *  \brief: valida los campos seleccionados en el multiselect
 *  \author: Edward Serrano
 *  \date: 17/01/2017
 *  \date modified: dia/mes/año
 *  \param: campoval tipo de multselect a procesar 
 *  \return: 
*/
function validateChekBox(campoval)
{
    try
    {
        var cod_Respon = "";
        var box_checke = $("input[type=checkbox]:checked");
        box_checke.each(function(i, o) {
          if ($(this).attr("name") == 'multiselect_'+campoval)
          {
            if($(this).val()!="0"){
              cod_Respon += ",'" + $(this).val() + "'";
            }
          }
        });
        return cod_Respon;
    } 
    catch (e) 
    {
        console.log("Error Function validateChekBox: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    } 
}

/*! \fn: detailForm
 *  \brief: valida los campos seleccionados en el multiselect
 *  \author: Edward Serrano
 *  \date: 17/01/2017
 *  \date modified: dia/mes/año
 *  \param: campoval tipo de multselect a procesar 
 *  \return: 
*/
function detailForm(cod_contro, cod_formul)
{
    try
    {
        var standa = $("#standaID").val();
        $("#popForumlID").remove();
        closePopUp('popForumlID');
        LoadPopupJQNoButton('open', 'Informacion Especifica ', '250', '500', false, false, true, 'popForumlID');
        var popForuml = $("#popForumlID");

        var parametros = "opcion=getDetailEal&Ajax=on&cod_contro="+cod_contro+"&cod_formul="+cod_formul;
        $.ajax({
            url:"../" + standa + "/formul/inf_formul_hojeal.php",
            type: "POST",
            data: parametros,
            async: false,
            success: function(data) {
                popForuml.html(data); // pinta los datos de la consulta      
            }
        });
    } 
    catch (e) 
    {
        console.log("Error Function detailForm: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    } 

}