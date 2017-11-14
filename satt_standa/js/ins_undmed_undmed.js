/*! \file: ins_undmed_undmed
 *  \brief: JS para todas las acciones del modulo tipo de transporte 
 *  \author: Edward serrano
 *  \author: Edward.serrano@intrared.net
 *  \version: 1.0
 *  \date: 14/07/2017
 *  \bug: 
 *  \warning: 
 */
 //variable global utilizada para validar los formularios
var validacion = true;
/*! \fn: NewUnidadMedida
 *  \brief: Inicia popup para generar nuevo tipo de transporte
 *  \author: Edward Serrano
 *  \date: 14/07/2017
 *  \date modified: dia/mes/año
 *  \return: 
 */
function NewUnidadMedida(tipAccion, row)
{
    try 
    {  
        //valido si el formulario es de insercion o edicion
        var parametros = "opcion=NewUnidadMedida&Ajax=on";
        cod_empaqu ="";
        if(tipAccion == "1")
        {
            var objeto = $(row).parent().parent();
            var cod_empaqu = objeto.find("input[id^=cod_empaqu]").val();
            parametros += "&accion=editar&cod_empaqu="+cod_empaqu;
        }
        var standa = $("#standaID").val();
        $("#popupID").remove();
        closePopUp('popupID');
        LoadPopupJQNoButton('open', 'Tipo de transporte '+(cod_empaqu), "170", "700", false, false, true, 'popupID');
        var popupID = $("#popupID");
        $.ajax({
            url:"../" + standa + "/tarifa/ins_undmed_undmed.php",
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
        console.log("Error Function NewUnidadMedida: " + e.message + "\nLine: " + e.lineNumber);
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
                url:"../" + standa + "/tarifa/ins_undmed_undmed.php",
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
        param += "&cod_empaqu="+$("#cod_empaquID").val();
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
            url:"../" + standa + "/tarifa/ins_undmed_undmed.php",
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
        var cod_empaqu = objeto.find("input[id^=cod_empaqu]").val();
        var ind_activa = (objeto.find("input[id^=ind_activa]").val()==1?0:1);

        var parametros ="&opcion=insertar&Ajax=on&accion=CambioEstado&&cod_empaqu="+cod_empaqu+"&ind_activa="+ind_activa;
            $.ajax({
                url:"../" + standa + "/tarifa/ins_undmed_undmed.php",
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