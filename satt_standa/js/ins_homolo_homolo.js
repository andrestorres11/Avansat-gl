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
    $(".accordion").accordion({
        collapsible: true,
        heightStyle: "content",
        icons: {
            "header": "ui-icon-circle-arrow-e",
            "activeHeader": "ui-icon-circle-arrow-s"
        }
    }).click(function() {
        $("body").removeAttr("class");
    });
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
    datos += '<div class="col-md-1 izquierda"><input type="hidden" name="cod_estcli" id="cod_estcli' + total + 'ID" value=""></div>';
    datos += '<div class="col-md-1 derecha">Nombre Estado<font style="color:red">*</font>:</div>';
    datos += '<div class="col-md-3 izquierda">';
    datos += '<div class="col-md-7"><input type="text" id="nom_estcli' + total + 'ID" validate="dir" obl="1" maxlength="30" minlength="5" name="nom_estcli" class="ancho"></div>';
    datos += '</div>';
    datos += '<div class="col-md-2 derecha">Estado:</div>';
    datos += '<div class="col-md-3 izquierda">';
    datos += '<div class="col-md-1"><input type="checkbox" id="ind_estcli' + total + 'ID" maxlength="30" minlength="5" name="ind_estcli" class="ancho"></div>';
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
            var cod_servic = $("input[name=cod_servic]").attr("name")+"="+$("input[name=cod_servic]").val();
            var window = "&"+$("input[name=window]").attr("name")+"="+$("input[name=window]").val();
            var standa = "&"+$("input[name=standa]").attr("name")+"="+$("input[name=standa]").val();
            var nom_estcli = "&"+$("#nom_estcli").attr("name")+"="+$("#nom_estcli").val();
            var ind_estcli = "&"+$("#ind_estcli").attr("name")+"="+($("#ind_estcli").is(":checked")?'1':'0');
            var parametros = cod_servic+window+standa+nom_estcli+ind_estcli;
            var standa = $("#standa").val();
            swal({
                title: "Registrar Estado",
                text: "\u00BFRealmente deseas registrar el estado?",
                type: "info",
                showCancelButton: true,
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            }, function() {
                $.ajax({
                    type: "POST",
                    url: "../" + standa + "/homnov/ajax_homolo_homolo.php",
                    data: "&Ajax=on&Option=registrar&standa=" + standa + "&" + parametros,
                    async: true,
                    success: function(datos) {
                        if (datos == 1) {
                            swal({
                                title: "Registro Exitoso",
                                text: "Datos Registrados de manera \u00c9xitosa",
                                type: "success",
                                showCancelButton: false,
                                closeOnConfirm: true,
                                showLoaderOnConfirm: true,
                            }, function() {
                                limpiar();
                                getDataList();
                            });

                        } else {
                            swal("Error al registrar los datos.");
                        }
                    }
                });
            });

        }
    } else {
        swal({
            title: "Registrar Estado",
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
            url: "../" + standa + "/homnov/ajax_homolo_homolo.php",
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
                $(".accordion").accordion({
                    collapsible: true,
                    heightStyle: "content",
                    icons: {
                        "header": "ui-icon-circle-arrow-e",
                        "activeHeader": "ui-icon-circle-arrow-s"
                    }
                }).click(function() {
                    $("body").removeAttr("class");
                });
            }
        });
        $("#sec2").removeAttr('style');


    } else {
        swal("Por favor verifica tu conexi\u00F3n a internet.");
    }
}

/*! \fn: linkCodEstado
 *  \brief: funcion Retornar el registro a actualizar
 *  \author: Ing. Luis Manrique
 *   \date: 21/07/2019
 *   \date modified: dia/mes/año
 *  \param: 
 *  \param: 
 *  \return 
 */

function linkCodEstado(codigo){
    codigo = $(codigo).parent().parent().find('input:hidden').val();
    var conn = checkConnection();
    if (conn) {
        if (val) {
            var standa = $("#standa").val();
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "../" + standa + "/homnov/ajax_homolo_homolo.php",
                data: "&Ajax=on&Option=actualizarCod&standa=" + standa + "&cod_estcli=" + codigo,
                async: true,
                success: function(datos) {
                    if(datos['ind_estcli'] == 1){
                        $("#ind_estcli").attr('checked', true);
                    }else{
                        $("#ind_estcli").attr('checked', false);
                    }
                   $("#cod_estcli").val(datos['cod_estcli']);
                   $("#nom_estcli").val(datos['nom_estcli']);
                   $("input[name=aceptar]").val("Actualizar").removeAttr("onclick").unbind('click').click(function(){
                        actualizar();
                    });
                }
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

/*! \fn: actualizar
 *  \brief: funcion para actualizar los datos en la base de datos
 *  \author: Ing. Luis Manrique
 *   \date: 21/07/2019
 *   \date modified: dia/mes/año
 *  \param: 
 *  \param: 
 *  \return 
 */

function actualizar() {
    var conn = checkConnection();
    if (conn) {
        if (val) {
            var cod_servic = $("input[name=cod_servic]").attr("name")+"="+$("input[name=cod_servic]").val();
            var window = "&"+$("input[name=window]").attr("name")+"="+$("input[name=window]").val();
            var standa = "&"+$("input[name=standa]").attr("name")+"="+$("input[name=standa]").val();
            var cod_estcli = "&"+$("#cod_estcli").attr("name")+"="+$("#cod_estcli").val();
            var nom_estcli = "&"+$("#nom_estcli").attr("name")+"="+$("#nom_estcli").val();
            var ind_estcli = "&"+$("#ind_estcli").attr("name")+"="+($("#ind_estcli").is(":checked")?'1':'0');
            var parametros = cod_servic+window+standa+cod_estcli+nom_estcli+ind_estcli;
            var standa = $("#standa").val();
            swal({
                title: "Actualizar Estado",
                text: "\u00BFRealmente desea actualizar el estado?",
                type: "info",
                showCancelButton: true,
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            }, function() {
                $.ajax({
                    type: "POST",
                    url: "../" + standa + "/homnov/ajax_homolo_homolo.php",
                    data: "&Ajax=on&Option=actualizarRegistro&standa=" + standa + "&" + parametros,
                    async: true,
                    success: function(datos) {
                        if (datos == 1) {
                            swal({
                                title: "Actualizar Estado",
                                text: "Estado actualizado \u00c9xitosamente",
                                type: "success",
                                showCancelButton: false,
                                closeOnConfirm: true,
                                showLoaderOnConfirm: true,
                            }, function() {
                                limpiar();
                                getDataList();
                            });

                        } else {
                            swal("Error al registrar los datos.");
                        }
                    }
                });
            });

        }
    } else {
        swal({
            title: "Actualizar Estado",
            text: "Por favor verifica tu conexi\u00F3n a internet.",
            type: "error"
        });

    }

}

function limpiar() {
    if($("input[name=aceptar]").val() == 'Actualizar'){
        $("input[name=aceptar]").val("Registrar").unbind('click').click(function(){
            registrar();
        });
    }
       
    $('input[type=text], input[type=checkbox], #cod_estcli').each(function() {
        $(this).attr('checked', false);
        $(this).val("");
    });
}
