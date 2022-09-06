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
        if(cod_contro !=",'-'" && cod_contro !="")
        {
            var mdata = "Ajax=on&opcion=getReporteGeneral&standa=" + standa +"&cod_contro="+cod_contro;
            $.ajax({
                url:"../" + standa + "/formul/inf_formul_hojeal.php",
                type:'POST',
                //dataType:'json',
                data: mdata,
                beforeSend: function() {
                    $("#tabResult").html("");
                },
                success:function(data){
                    $("#tabResult").html(data);
                    $("#sec1").css({"height":"auto"});
                    $("#tabResult").find(".celda_info").each(function(i,v){
                        $(v).removeClass("celda_info");
                    });
                    $("#tabResult").find("label").each(function(i,v){
                        if($(v).attr("onclick"))
                        {
                            $(v).addClass("CellInfohref");
                        }
                    });
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
        LoadPopupJQNoButton('open', 'Informacion Especifica ', ($(window).height() - 40), ($(window).width() - 40), false, false, true, 'popForumlID');
        var popForuml = $("#popForumlID");

        var parametros = "opcion=getDetailEal&Ajax=on&cod_contro="+cod_contro+"&cod_formul="+cod_formul;
        $.ajax({
            url:"../" + standa + "/formul/inf_formul_hojeal.php",
            type: "POST",
            data: parametros,
            async: false,
            beforeSend: function(obj) {
                popForuml.html('<table align="center"><tr><td><img src="../' + standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
            },
            success: function(data) {
                popForuml.html(data);
                $("#popForumlID").find(".celda_info").each(function(i,v){
                    $(v).removeClass("celda_info");
                });     
            }
        });
    } 
    catch (e) 
    {
        console.log("Error Function detailForm: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    } 

}

/*! \fn: exprtExcel
 *  \brief: exporta a excel
 *  \author: Edward Serrano
 *  \date: 17/01/2017
 *  \date modified: dia/mes/año
 *  \param: campoval tipo de multselect a procesar 
 *  \return: 
*/
function exprtExcel(tipo)
{
    try
    {
        $("#opcionID").val("exprtExcel");
        $("#tExporExcelID").val(tipo);
        $("#form_searchID").submit();
    } 
    catch (e) 
    {
        console.log("Error Function exprtExcel: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    } 

}