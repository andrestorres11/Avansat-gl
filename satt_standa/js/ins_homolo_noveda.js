/*! \file: monit
 *  \brief: archivo con multiples funciones jquey para el archivo ins_horari_monito.php
 *  \author: Ing. Alexander Correa
 *  \author: alexander.correa@intrared.net
 *  \version: 1.0
 *  \date: 05/02/2016
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */


/*! \fn: 
 *  \brief: funcion que ese ejecuta cuando la vista php a cargado completamente    
 *  \author: Ing. Alexander Correa
 *   \date: 05/02/2016
 *   \date modified: dia/mes/año
 *  \param: 
 */
var val = true;
$(function() {
    $("body").removeAttr("class");
    
    $("#contenido").css({
        height: 'auto'
    });
    var standa = $("#standa").val();
   getDataList();
});


/*! \fn: addUserForm
 *  \brief: funcion para agregar una linea completa al formulario de insercion
 *  \author: Ing. Alexander Correa
 *   \date: 05/02/2016
 *   \date modified: dia/mes/año
 *  \param: 
 *  \param: 
 *  \return 
 */
function addUserForm() {
    var total = $("#total").val();
    var standa = $("#standa").val();
    var datos = ' <div class="col-md-12" id="div' + total + '">';
    datos += '<div class="col-md-1 izquierda"><input type="hidden" name="cod_estado" id="cod_estado' + total + 'ID" value=""></div>';
    datos += '<div class="col-md-1 derecha">Nombre Estado<font style="color:red">*</font>:</div>';
    datos += '<div class="col-md-3 izquierda">';
    datos += '<div class="col-md-7"><input type="text" id="nom_estado' + total + 'ID" validate="dir" obl="1" maxlength="30" minlength="5" name="nom_estado" class="ancho"></div>';
    datos += '</div>';
    datos += '<div class="col-md-2 derecha">Estado:</div>';
    datos += '<div class="col-md-3 izquierda">';
    datos += '<div class="col-md-1"><input type="checkbox" id="ind_estado' + total + 'ID" maxlength="30" minlength="5" name="ind_estado" class="ancho"></div>';
    datos += '</div>';
    datos += '</div>';
    if (total > 0) {
        $("#div" + (total - 1)).after(datos);
    } else {
        $("#primero").append(datos);
    }
    $("#total").val(++total);
}


/*! \fn: registrar
 *  \brief: funcion para almacenar los datos en la base de datos
 *  \author: Ing. Alexander Correa
 *   \date: 11/02/2016
 *   \date modified: dia/mes/año
 *  \param: 
 *  \param: 
 *  \return 
 */

function registrar() {
    var conn = checkConnection();
    if (conn) {
        val = validaciones();
        if (val) {
            var table = $("#tablaHomNov").serialize();
            var cod_servic = "&"+$("input[name=cod_servic]").attr("name")+"="+$("input[name=cod_servic]").val();
            var window = "&"+$("input[name=window]").attr("name")+"="+$("input[name=window]").val();
            var standa = "&"+$("input[name=standa]").attr("name")+"="+$("input[name=standa]").val();
            var parametros = table+cod_servic+window+standa;
            var standa = $("#standa").val();
            swal({
                title: "Registrar Homologaci\u00F3n de estados y Novedades",
                text: "\u00BFRealmente Desea registrar las configuraciones ingresadas?",
                type: "info",
                showCancelButton: true, 
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            }, function() {
                $.ajax({
                    type: "POST",
                    url: "../" + standa + "/homnov/ajax_homolo_noveda.php",
                    data: "&Ajax=on&Option=registrar&standa=" + standa + "&" + parametros,
                    async: true,
                    success: function(datos) {
                        if (datos == 1) {
                            swal({
                                title: "Registrar Homologaci\u00f3n de estados y Novedades",
                                text: "Homologaci\u00f3n registrada \u00c9xitosamente",
                                type: "success",
                                showCancelButton: false,
                                closeOnConfirm: true,
                                showLoaderOnConfirm: true,
                            }, function() {
                                limpiar();
                                location.reload();
                            });

                        } else if(datos != 1 && datos != 0 ){
                            var action = datos.indexOf('Homologar Novedades') != -1 ? false : true;

                            //Corrige errores en los estilos del mensaje de error de conexión.
                            datos = datos.replace('<table' , '<table style="width: 100%; background: #257038"');
                            datos = datos.replace('align="right"' , 'align="center"');  
                                                  
                            swal({
                                title: "Registrar Homologaci\u00f3n de estados y Novedades",
                                html: action,
                                text: datos,
                                type: "info",
                                showCancelButton: false,
                                closeOnConfirm: true,
                                showLoaderOnConfirm: true,
                            });
                        }else {
                            swal("Error al registrar los datos.");
                        }
                    }
                });
            });

        }
    } else {
        swal({
            title: "Registrar Pre-planeaci\u00F3n carga laboral",
            text: "Por favor verifica tu conexi\u00F3n a internet.",
            type: "error"
        });

    }

}


/*! \fn: getDataList
 *  \brief: funcion que trae los datos ya almacenados en la base de datos
 *  \author: Ing. Alexander Correa
 *   \date: 11/02/2016
 *   \date modified: dia/mes/año
 *  \param: 
 *  \param: 
 *  \return 
 */

function getDataList() {
    var conn = checkConnection();
    if (conn) {
        var standa = $("#standa").val();
        $.ajax({
            type: "POST",
            url: "../" + standa + "/homnov/ajax_homolo_noveda.php",
            data: "&Ajax=on&Option=getDataList&standa=" + standa,
            async: false,
            beforeSend: function() {
                $("#PopUpID").html('<table align="center"><tr><td><img src="../' + standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
            },
            success: function(datos) {
                $("#lista").html("");
                $("#lista").append(datos)
            },
            complete: function() {
                $("#sec2").css("height", "auto !important");
            }
        });
        $("#sec2").removeAttr('style');


    } else {
        swal("Por favor verifica tu conexi\u00F3n a internet.");
    }
}

/*! \fn: duplicateRow
 *  \brief: funcion para duplicar las filas de una tabla
 *  \author: Ing. Luis Manrique
 *   \date: 21/07/2019
 *   \date modified: dia/mes/año
 *  \param: 
 *  \param: 
 *  \return 
 */

function duplicateRow(idTabla) {
     var clickID = $("#"+idTabla+" tr:last").attr('id');               
     var newID = parseInt(clickID)+1;                               
     fila = $("#"+idTabla+" tr:last").clone(true);
     fila.attr("id",newID);
     fila.find('.deleteRow').attr("onclick","deleteDuplicateRow("+newID+")");
     fila.find('.row').html(newID);
     fila.find('.row').html(newID);
     $("input, select", fila).each(function(){
        var idField = $(this).attr("id");
        idField = idField.split("_");
        $(this).attr("id", idField[0]+"_"+newID);
        $("option:selected", this).removeAttr("selected");
        $(this).val("");
     });
     $("#"+idTabla+"").append(fila);
}

/*! \fn: actualizar
 *  \brief: funcion para actualizar los datos en la base de datos
 *  \author: Ing. Luis Manrique
 *   \date: 21/07/2019
 *   \date modified: dia/mes/año
 *  \param: 
 *  \param: 
 *  \return 
 */

function deleteDuplicateRow(id){
    if(id != 1){
        $("#"+id).remove();
    }else{
        $("#"+id+" input, #"+id+" select").each(function(){
            $("option:selected", this).removeAttr("selected");
            $(this).val("");
         });
    }
}

function limpiar() {
    if($("input[name=aceptar]").val() == 'Actualizar'){
        $("input[name=aceptar]").val("Registrar").unbind('click').click(function(){
            registrar();
        });
    }

    $('select').each(function() {
        $("option:selected", this).val("");
    });

    $(this).val("");
}
