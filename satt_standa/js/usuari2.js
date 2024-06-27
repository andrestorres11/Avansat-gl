/* ! \file: usuari.js
 *  \brief: archivo con multiples funciones javascript para la creacion, edicion, activacion e inactivacion de usuarios
 *  \author: Ing. Alexander Correa
 *  \author: alexander.correa@intrared.net
 *  \version: 1.0
 *  \date: 11/04/2016
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */


var standa;
$(function() {
    setTimeout(function() {
        $("div").css("height", "auto");
    }, 300);
    standa = $("#standa").val();
    mostrarOcultos();
});

/* ! \fn: editarUsuario
 *  \brief: abre el formulario de edicion de usuario
 *  \author: Ing. Alexander Correa
 *  \date: 11/04/2016
 *  \date modified: dia/mes/año
 *  \param: row  => objeto => objeto con la informacion del usuario a editar    
 *  \return 
 */
function editarUsuario(row) {
    var objeto = $(row).parent().parent();
    var cod_usuari = objeto.find("input[id^=cod_usuari]").val();
    var nom_usuari = objeto.find("input[id^=nom_usuari]").val();
    var cod_consec = objeto.find("input[id^=cod_consec]").val();

    swal({
        title: "Editar Usuario",
        text: "¿Realmente Deseas Editar el Usuario " + nom_usuari + "?",
        type: "warning",
        showCancelButton: true,
        closeOnConfirm: true,
        showLoaderOnConfirm: true,
    }, function() {
        $("#opcion").val(2);
        $("#cod_usuari").val(cod_usuari);
        $("#nom_usuari").val(nom_usuari);
        $("#cod_consec").val(cod_consec);
        $("#form_searchID").submit();
    });
}


/* ! \fn: activarUsuario
 *  \brief: funcion para activar un usuarios
 *  \author: Ing. Alexander Correa
 *  \date: 11/04/2016
 *  \date modified: dia/mes/año
 *  \param: row  => objeto => objeto con la informacion del usuario a editar    
 *  \return 
 */
function activarUsuario(row) {
    var objeto = $(row).parent().parent();
    var cod_usuari = objeto.find("input[id^=cod_usuari]").val();
    var nom_usuari = objeto.find("input[id^=nom_usuari]").val();
    var cod_consec = objeto.find("input[id^=cod_consec]").val();

    swal({
            title: "Activar Usuario",
            text: "¿Realmente Deseas Activar el Usuario " + nom_usuari + "?",
            type: "input",
            showCancelButton: true,
            closeOnConfirm: false,
            animation: "slide-from-top",
            inputPlaceholder: "Justificación"
        },
        function(inputValue) {
            if (inputValue === false) return false;

            if (inputValue === "") {
                swal.showInputError("Por favor justifica la operación");
                return false
            }
            $("#obs_histor").val(inputValue);
            ajaxOperacion("1", cod_consec);
        });


}


/* ! \fn: inactivarUsuario
 *  \brief: abre el formulario de edicion de usuario
 *  \author: Ing. Alexander Correa
 *  \date: 11/04/2016
 *  \date modified: dia/mes/año
 *  \param: row  => objeto => objeto con la informacion del usuario a editar    
 *  \return 
 */
function inactivarUsuario(row) {
    var objeto = $(row).parent().parent();
    var cod_usuari = objeto.find("input[id^=cod_usuari]").val();
    var nom_usuari = objeto.find("input[id^=nom_usuari]").val();
    var cod_consec = objeto.find("input[id^=cod_consec]").val();

    swal({
            title: "Inactivar Usuario",
            text: "¿Realmente Deseas Inactivar el Usuario " + nom_usuari + "?",
            type: "input",
            showCancelButton: true,
            closeOnConfirm: false,
            animation: "slide-from-top",
            inputPlaceholder: "Justificación"
        },
        function(inputValue) {
            if (inputValue === false) return false;

            if (inputValue === "") {
                swal.showInputError("Por favor justifica la operación");
                return false
            }
            $("#obs_histor").val(inputValue);
            ajaxOperacion("0", cod_consec);
        });

}

/* ! \fn: ajaxOperacion
 *  \brief: funcion que activa o inactiva un usuario
 *  \author: Ing. Alexander Correa
 *  \date: 11/04/2016
 *  \date modified: dia/mes/año
 *  \param: ind_estado => string => opcion de activacion("1") o inactivacion ("0")    
 *  \param: cod_usuari => string => usuario a modificar  
 *  \return alerta con el resultado de la operacion
 */
function ajaxOperacion(ind_estado, cod_consec) {
    var conn = checkConnection();
    var operacion;
    if (ind_estado == "1") {
        operacion = "Activar";
    } else {
        operacion = "Inactivar";
    }
    if (conn) {
        var obs_histor = $("#obs_histor").val();
        $.ajax({
            type: "POST",
            url: "../" + standa + "/seguridad/ajax_perfil_perfil.php",
            data: "&Ajax=on&Option=cambiarEstadoUsuario&standa=" + standa + "&ind_estado=" + ind_estado + "&cod_consec=" + cod_consec + "&obs_histor=" + obs_histor,
            async: true,
            success: function(data) {
                if (data == 1) {
                    swal({
                        title: operacion + " Usuario",
                        text: "Datos Registrados con Éxito",
                        type: "success",
                        showCancelButton: true,
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true,
                    }, function() {
                        $("#opcion").val("");
                        $("#form_searchID").submit();

                    });
                } else {
                    swal({
                        title: operacion + " Usuario",
                        text: "Error al Registrar los datos. Por Favor Intenta Nuevamente. Si el Error Persiste Por Favor Informar al Proveedor",
                        type: "error"
                    });
                }
            }
        });
    } else {
        swal({
            title: "Registrar Usuario",
            text: "Por favor verifica tu conexión a internet.",
            type: "error"
        });
    }
}

/* ! \fn: mostrarOcultos
 *  \brief: muestra dos imputs ocultos dependiendo del perfil
 *  \author: Ing. Alexander Correa
 *  \date: 11/04/2016
 *  \date modified: dia/mes/año
 *  \param: 7,8,713
 *  \return 
 */
function mostrarOcultos() {
    var cod_perfil = $("#cod_perfil").val();
    var cod_usuari = $("#cod_usuari").val();
    if (cod_perfil == 7 || cod_perfil == 8 || cod_perfil == 669 || cod_perfil == 728 || cod_perfil == 1 || cod_perfil == 73) {
        if (cod_perfil != 699) {
            $("#gru_pri").fadeIn(530);
            $("#cod_priori").attr("obl", 1);
            $("#cod_grupox").attr("obl", 1);
        }else {
            $("#7").attr("obl", 1);
            $("#7").attr("validate", "select");
        }
    } else {
        $("#gru_pri").fadeOut(530);
        $("#cod_priori").removeAttr("obl");
        $("#cod_grupox").removeAttr("obl");
        $("#7").removeAttr("obl");
        $("#7").removeAttr("validate");
    }

    $.ajax({
        type: "POST",
        url: "../" + standa + "/seguridad/ajax_perfil_perfil.php",
        data: "&Ajax=on&Option=getFiltersAsegurad&standa=" + standa + "&cod_perfil=" + cod_perfil+"&cod_usuari="+cod_usuari,
        async: true,
        success: function(data) {
            if (parseInt(data) !== 99) {
                $("servicios").fadeOut(530);
                $("#datos").html("");
                $("#datos").append(data);
                setTimeout(function() {
                    $("#servicios").fadeIn(530);
                }, 531);

            } else {
                $("#servicios").fadeOut(530);
            }
            //getOtherFilters();
        }
    });
}

/* ! \fn: validarLetras
 *  \brief: controla los caracteres que se escriben en el campo de texto
 *  \author: Ing. Andres Torres
 *  \date: 04/03/2019
 *  \date modified: dia/mes/a?o
 *  \param: e => indica que evento es el que llega
 *  \return 
 */
function validarLetras(e) { // 1
    tecla = (document.all) ? e.keyCode : e.which; // 2
     //alert(tecla);
    if (tecla==8)return true; // 3
    if(tecla == 241 || tecla == 209){
        alert("La letra ñ/Ñ no es valida para el Nombre de Usuario");
    }
    patron =/[A-Za-z\s\.]/; // 4
    te = String.fromCharCode(tecla); // 5
    return patron.test(te); // 6
}

/* ! \fn: registrar
 *  \brief: registra o actualiza un usuario en la base de datos
 *  \author: Ing. Alexander Correa
 *  \date: 12/04/2016
 *  \date modified: dia/mes/año
 *  \param: ind     => int => indica si es edicion (2) o registro (1)    
 *  \return 
 */
function registrar(ind) {
    var parametros;
    var pass1 = $("#clv_usuari").val();
    var pass2 = $("#con_passwo").val();
    var val = true;

    if($("#cod_usuari").val().length < 7 || $("#cod_usuari").val().length > 30)
    {
        inc_alerta("cod_usuari", "El valor minimo es de 7 y max 30 caracteres");
        return false;
    }

   

    if ($.trim(pass1) !== "") {
        if (pass1 !== pass2) {
            setTimeout(function() {
                inc_alerta("clv_usuari", "Las contraseñas no coinciden.");
                inc_alerta("con_passwo", "Las contraseñas no coinciden.");
            }, 530);
            val = false;
        } else {
            if (pass1.length < 7) {
                setTimeout(function() {
                    inc_alerta("clv_usuari", "La contraseña mínima es de 7 caracteres.");
                }, 530);
                val = false;
            }
        }
    } else {
        $("#clv_usuari").val("");
    }
    if (!$("input[name='num_diasxx']:radio").is(':checked')) {
        setTimeout(function() {
            inc_alerta("ind_30dias", "Por favor seleccione una opcion.")
            inc_alerta("ind_60dias", "Por favor seleccione una opcion.")
        }, 530);
        val = false;
    }


    val = validaciones();
    if (val) {
        inc_remover_alertas();
        parametros += "&ind=" + ind + "&" + getDataForm();
        $("#services select option:selected").each(function(i, o) {
            if ($(this).val() != "") {
                parametros += "&cod_filtro[" + i + "]=" + $(this).parent().attr("id");
                parametros += "&val_filtro[" + i + "]=" + $(this).val();
            }
        });
        var opera;
        if (ind == 1) {
            opera = "Registrar";
        } else {
            opera = "Editar";
        }
        swal({
            title: opera + " Usuario",
            text: "¿Realmente desea " + opera + " los datos del Usuario?",
            type: "warning",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
        }, function() {
            $.ajax({
                type: "POST",
                url: "../" + standa + "/seguridad/ajax_perfil_perfil.php",
                data: "&Ajax=on&Option=RegisterDataUser&standa=" + standa + parametros,
                async: true,
                success: function(data) {
                    if (data == 1) {
                        swal({
                            title: opera + "  Usuario",
                            text: "Datos registrados con éxito.",
                            type: "success"
                        });
                        $("#opcion").val("");
                        $("#form_searchID").submit();
                    } else if (data === "usuario") {
                        swal({
                            title: opera + "  Usuario",
                            text: "Error al " + opera + " los datos de usuario",
                            type: "error"
                        });

                    } else if (data === "filtros") {
                        swal({
                            title: opera + "  Usuario",
                            text: "Error al " + opera + " los filtros de usuario",
                            type: "error"
                        });

                    } else {
                        swal({
                            title: opera + "  Usuario",
                            text: "Ocurrió un error al registrar  los datos, por favor intenta nuevamente. Si el error persiste por favor informar al proveedor.",
                            type: "error"
                        });

                    }
                }
            });
        });
    }
}


function cambiar_clave()
{
        formulario = document.form_clavex
        if(formulario.clave.checked)
        {
                if(formulario.clv_usuari.value == window.prompt("Digite la Clave Actual",''))
                {
                        formulario.new_clv_usuari.disabled = false
                        formulario.new_clv_usuari.value = ''
                        formulario.new_confirma.disabled = false
                        formulario.new_confirma.value = ''
                        formulario.new_clv_usuari.focus();
                }
                else
                {
                        window.alert("Contraseña no Coincide")
                        formulario.new_clv_usuari.disabled = true
                        formulario.new_confirma.disabled = true
                        formulario.clave.checked = false
                }
        }
                else
                {
                        formulario.new_clv_usuari.disabled = true
                        formulario.new_confirma.disabled = true
                }
}

function validar(mensaje)
{

    var correo = /^(.+\@.+\..+)$/
        formulario = document.form_clavex

         if(!formulario.nom_usuari.value)
        {
                window.alert("Digite el  Nombre")
                  formulario.nom_usuari.focus()
        }
        else if(formulario.usr_emailx.value == "")
        {
                  window.alert("Digite el Correo Electronico")
                  formulario.usr_emailx.focus()
        }
        else if(!correo.test(formulario.usr_emailx.value))
        {
                  window.alert("Correo electronico Invalido")
                  formulario.usr_emailx.focus()
        }

else if(formulario.clave.checked)
{
     if(!formulario.new_clv_usuari.value)
                {
                 window.alert("Digite la Nueva Contraseña")
                   formulario.new_clv_usuari.focus()
                }
     else  if(!formulario.new_confirma.value)
                {
                window.alert(" Confirme Contraseña")
                  formulario.new_confirma.focus()
                }
        else if(formulario.new_confirma.value != formulario.new_clv_usuari.value)
                {
                 window.alert("Contraseña no Coincide")
                   formulario.new_confirma.focus()
                }
        else if (confirm("Desea Cambiar los Datos de "+ formulario.nom_usuari.value))
             {   formulario.actual.value = 1;
                    formulario.submit()
                }

}
        else if (confirm("Desea Cambiar los Datos de "+ formulario.nom_usuari.value))
             {   formulario.actual.value = 1;
                   formulario.submit()
                }

}
