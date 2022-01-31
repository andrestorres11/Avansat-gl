/*! \file: ins_tipvin_tipvin
 *  \brief: JS para todas las acciones del modulo tipo de vinculacion 
 *  \author: Edward serrano
 *  \author: Edward.serrano@intrared.net
 *  \version: 1.0
 *  \date: 14/07/2017
 *  \bug: 
 *  \warning: 
 */
 //variable global utilizada para validar los formularios
var validacion = true;
/*! \fn: NewTipVinculacion
 *  \brief: Inicia popup para generar nuevo tipo de vinculacion
 *  \author: Edward Serrano
 *  \date: 14/07/2017
 *  \date modified: dia/mes/año
 *  \return: 
 */
function NewTipVinculacion(tipAccion, row)
{
    try 
    {  
        //valido si el formulario es de insercion o edicion
        var parametros = "opcion=NewTipVinculacion&Ajax=on";
        cod_tipveh ="";
        if(tipAccion == "1")
        {
            var objeto = $(row).parent().parent();
            var cod_tipveh = objeto.find("input[id^=cod_tipveh]").val();
            parametros += "&accion=editar&cod_tipveh="+cod_tipveh;
        }
        var standa = $("#standaID").val();
        $("#popupID").remove();
        closePopUp('popupID');
        LoadPopupJQNoButton('open', 'Tipo de vinculacion '+(cod_tipveh), "170", "700", false, false, true, 'popupID');
        var popupID = $("#popupID");
        $.ajax({
            url:"../" + standa + "/tarifa/ins_tipvin_tipvin.php",
            type: "POST",
            data: parametros,
            async: false,
            beforeSend: function(obj) {
                popupID.html('<table align="center"><tr><td><img src="../' + standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
            },
            success: function(data) {
                popupID.html(data);
            }
        });
    } 
    catch (e) 
    {
        console.log("Error Function NewTipVinculacion: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: insertar
 *  \brief: Envia informaciona almacenar
 *  \author: Edward Serrano
 *  \date: 14/07/2017
 *  \date modified: dia/mes/año
 *  \return: 
 */
function insertar()
{
    try
    {
        var standa = $("#standaID").val();
        var parametros = "";
        if(parametros = validar( parametros ))
        {
            parametros +="&opcion=insertar&Ajax=on";
            $.ajax({
                url:"../" + standa + "/tarifa/ins_tipvin_tipvin.php",
                type: "POST",
                data: parametros,
                async: false,
                success: function(data) {
                    if(data == "OK")
                    {
                        swal('La operacion se ha realizado con exito.');
                        $("#popupID").remove();
                        closePopUp('popupID'); 
                        mostrar();
                    }
                    else
                    {
                        swal('Error en la operacion, Intente nuevamente.');
                    }
                }
            });
        }
        
    }
    catch (e)
    {
        console.log("Error Funcion insertar: "+ e.message + "·\nLine: "+ e.lineNumber);
    }
}

/*! \fn: validar
 *  \brief: validar la informacion
 *  \author: Edward Serrano
 *  \date: 14/07/2017
 *  \date modified: dia/mes/año
 *  \return: 
 */
function validar( param )
{
    try
    {
        var estado = true;
        param += "accion="+($("#accionID").val()==""?"almacenar":"editar");
        param += "&cod_tipveh="+$("#cod_tipvehID").val();
        $("#popupID").find("input[type=text], select").each(function(i,v){
            if( $(v).val() != "" && $(v).val() != "-" )
            {
                param += "&"+$(v).attr("name")+"="+$(v).val();
            }
            else
            {   
                inc_alerta($(v).attr("id"), "Campo requerido.");
                estado = false;
            }
        });

        if(estado == true )
        {
            return param;
        }
        else
        {
            return null;
        }
    }
    catch (e)
    {
        console.log("Error Funcion validar: "+ e.message + "·\nLine: "+ e.lineNumber);
    }
}

/*! \fn: mostrar
 *  \brief: Visualiza el dinamic list
 *  \author: Edward Serrano
 *  \date: 14/07/2017
 *  \date modified: dia/mes/año
 *  \return: 
 */
function mostrar()
{
    try
    {
        var standa = $("#standaID").val();
        parametros ="opcion=getDinamiList&Ajax=on";
        $.ajax({
            url:"../" + standa + "/tarifa/ins_tipvin_tipvin.php",
            type: "POST",
            data: parametros,
            async: false,
            success: function(data) {
                $("#form1").html("");
                $("#form1").html(data);
                $("#contentID").css("height: auto;");
            }
        });
    }
    catch (e)
    {
        console.log("Error Funcion mostrar: "+ e.message + "·\nLine: "+ e.lineNumber);
    }
}

/*! \fn: CambioEstado
 *  \brief: Envia informaciona almacenar
 *  \author: Edward Serrano
 *  \date: 14/07/2017
 *  \date modified: dia/mes/año
 *  \return: 
 */
function CambioEstado(row)
{
    try
    {
        var standa = $("#standaID").val();
        var objeto = $(row).parent().parent();
        var cod_tipveh = objeto.find("input[id^=cod_tipveh]").val();
        var ind_estado = (objeto.find("input[id^=ind_estado]").val()==1?0:1);

        var parametros ="&opcion=insertar&Ajax=on&accion=CambioEstado&&cod_tipveh="+cod_tipveh+"&ind_estado="+ind_estado;
            $.ajax({
                url:"../" + standa + "/tarifa/ins_tipvin_tipvin.php",
                type: "POST",
                data: parametros,
                async: false,
                success: function(data) {
                    if(data == "OK")
                    {
                        swal('La operacion se ha realizado con exito.');
                        $("#popupID").remove();
                        closePopUp('popupID'); 
                        mostrar();
                    }
                    else
                    {
                        swal('Error en la operacion, Intente nuevamente.');
                    }
                }
            });
        
        
    }
    catch (e)
    {
        console.log("Error Funcion insertar: "+ e.message + "·\nLine: "+ e.lineNumber);
    }
}