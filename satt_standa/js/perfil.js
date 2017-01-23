/* ! \archive: perfil.js
 *  \brief: archivo con multiples funciones para el manejo de perfiles
 *  \author: Ing. Alexander Correa
 *  \author: aleander.correa@intrared.net
 *  \date: 06/04/206
 *  \date modified: dia/mes/año
 *  \warning:   
 *  \ 
 */
var standa, check;
$(function() {
    check = 0;
    standa = $("#standa").val();
    setTimeout(function() {
        $("div").css({
            height: 'auto'
        });
    }, 1000);

})


/* ! \fn: copiarPerfil
 *  \brief: funcion para cargar el formulario de registro de perfil en la opcion copiar
 *  \author: Ing. Alexander Correa
 *  \date: 06/04/206
 *  \date modified: dia/mes/año
 *  \param: row   => object => fila con el perfil que se va a copiar  
 *  \return 
 */
function copiarPerfil(row) {

    var objeto = $(row).parent().parent();
    var cod_perfil = objeto.find("input[id^=cod_perfil]").val();
    var nom_perfil = objeto.find("input[id^=nom_perfil]").val();
    var cod_respon = objeto.find("input[id^=cod_respon]").val();
    swal({
        title: "Copiar Perfil",
        text: "¿Realmente Deseas Copiar el Perfil " + nom_perfil + "?",
        type: "warning",
        showCancelButton: true,
        closeOnConfirm: true,
        showLoaderOnConfirm: true,
    }, function() {
        $("#opcion").val(3);
        $("#cod_perfil").val(cod_perfil);
        $("#nom_perfil").val(nom_perfil);
        $("#cod_respon").val(cod_respon);
        $("#form_searchID").submit();
    });

}

/* ! \fn: editarPerfil
 *  \brief: funcion para cargar el formulario de edicion del perfil seleccionado
 *  \author: Ing. Alexander Correa
 *  \date: 08/04/2016
 *  \date modified: dia/mes/año
 *  \param: row     => object => fila con el perfil a editar    
 *  \return 
 */
function editarPerfil(row) {
    var objeto = $(row).parent().parent();
    var cod_perfil = objeto.find("input[id^=cod_perfil]").val();
    var nom_perfil = objeto.find("input[id^=nom_perfil]").val();
    var cod_respon = objeto.find("input[id^=cod_respon]").val();
    swal({
        title: "Editar Perfil",
        text: "¿Realmente Deseas Editar el Perfil " + nom_perfil + "?",
        type: "warning",
        showCancelButton: true,
        closeOnConfirm: true,
        showLoaderOnConfirm: true,
    }, function() {
        $("#opcion").val(2);
        $("#cod_perfil").val(cod_perfil);
        $("#nom_perfil").val(nom_perfil);
        $("#cod_respon").val(cod_respon);
        $("#form_searchID").submit();
    });
}

/* ! \fn: registrar
 *  \brief: funcion para gestionar la operacion del perfil (creacion, edicion, copia)
 *  \author: Ing. Alexander Correa
 *  \date: 07/04/2016
 *  \date modified: dia/mes/año
 *  \param:   idn = int = indica el tipo de operacion 1: registro, 2: edicion: 3: copiado 
 *  \return 
 */
function registrar(ind) {
    var conn = checkConnection();
    if (conn) {
        var val = validaciones();
       
        if (val) {
            var parametros = getDataForm();
            swal({
                title: "Registrar Perfil",
                text: "¿Realmente Deseas Registrar el Perfil Ingresado?",
                type: "warning",
                showCancelButton: true,
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            }, function() {
                $.ajax({
                    type: "POST",
                    url: "../" + standa + "/seguridad/ajax_perfil_perfil.php",
                    data: "&Ajax=on&Option=gestionarDatos&standa=" + standa + "&ind=" + ind + "&" + parametros,
                    async: true,
                    success: function(data) {
                        console.log(data);
                        if (data == 1) {
                            swal({
                                title: "Registrar Perfil",
                                text: "Datos Registrados con Éxito",
                                type: "success",
                                showCancelButton: true,
                                closeOnConfirm: false,
                                showLoaderOnConfirm: true,
                            }, function() {
                                $("#form_searchID").submit();

                            });
                        } else {
                            swal({
                                title: "Registrar Perfil",
                                text: "Error al Registrar los datos. Por Favor Intenta Nuevamente. Si el Error Persiste Por Favor Informar al Proveedor",
                                type: "error"
                            });
                        }
                    }
                });
            });
        } 

    } else {
        swal({
            title: "Registrar Perfil",
            text: "Por favor verifica tu conexión a internet.",
            type: "error"
        });
    }
}

/* ! \fn: getUsersPerfil
 *  \brief: muestra la lista de usuarios de asociados a un perfil
 *  \author: Ing. Alexander Correa
 *  \date: 26/04/2016
 *  \date modified: dia/mes/año
 *  \param: row     => objeto => indica la linea con los usuarios a mostrar    
 *  \return 
 */
function getUsersPerfil(row) {
    var objeto = $(row).parent().parent();
    var cod_perfil = objeto.find("input[id^=cod_perfil]").val();
    var nom_perfil = objeto.find("input[id^=nom_perfil]").val();

    closePopUp('popID');
    LoadPopupJQNoButton('open', 'Detalle', "auto", ($(window).width() - 40), false, false, true);
    var popup = $("#popID");

    var conn = checkConnection(); //valido la conexion a internet
    if (conn == true) {
        $.ajax({
            type: "POST",
            url: "../" + standa + "/seguridad/ajax_perfil_perfil.php",
            data: "&Ajax=on&Option=listaUsuarios&standa=" + standa + "&cod_perfil=" + cod_perfil + "&nom_perfil=" + nom_perfil,
            async: true,
            beforeSend: function(obj) {
                BlocK('Cargando Usuarios del Perfil ' + nom_perfil, true);
                popup.parent().children().children('.ui-dialog-titlebar-close').hide();
            },
            success: function(data) {
                popup.html(data);
                BlocK();
            }
        });
    } else {
        swal({
            title: "Registrar Perfil",
            text: "Por favor verifica tu conexión a internet.",
            type: "error"
        });
    }
}

/* ! \fn: novedades
 *  \brief: muestra la lista de novedades para asiganar a un perfil
 *  \author: Ing. Alexander Correa
 *  \date: 28/06/2016
 *  \date modified: dia/mes/año
 *  \param: cod_perfil = integer = indicador del perfil para el que aplicaran las noedades, en caso de ser nuevo 
 *  \return 
 */
function novedades(cod_perfil) {
    closePopUp('popID');
    LoadPopupJQNoButton('open', 'Novedades', ($(window).height() - 100), ($(window).width() - 100), false, false, true);
    var popup = $("#popID");
    var conn = checkConnection(); //valido la conexion a internet
    if (conn == true) {
        $.ajax({
            type: "POST",
            url: "../" + standa + "/seguridad/ajax_perfil_perfil.php",
            data: "&Ajax=on&Option=listaNovedades&standa=" + standa + "&cod_perfil=" + cod_perfil,
            async: true,
            beforeSend: function(obj) {
                BlocK('Cargando Novedades', true);
                popup.parent().children().children('.ui-dialog-titlebar-close').hide();
            },
            success: function(data) {
                popup.html(data);
                BlocK();
            }
        });
    } else {
        swal({
            title: "Registrar Perfil",
            text: "Por favor verifica tu conexión a internet.",
            type: "error"
        });
    }
}


/* ! \fn: allOfThem
 *  \brief: selecciona toda slas novedades en a vista 
 *  \author: Ing. Alexander Correa
 *  \date: 28/06/2016
 *  \date modified: dia/mes/año
 *  \param: 
 *  \return 
 */
function allOfThem() {
    console.log(check);
    var i = 0;
    if (check == 0) {
        $('input[id^="nov_"]').each(function() {
            $(this).attr("checked", true);
        });
        check = 1;
    } else {
        $('input[id^="nov_"]').each(function() {
            $(this).removeAttr("checked");
        });
        check = 0;
    }
}

/* ! \fn: setNovedades
 *  \brief: guarda en un hidden las novedades seleccionadas
 *  \author: Ing. Alexander Correa
 *  \date: 28/06/2016
 *  \date modified: dia/mes/año
 *  \param:     
 *  \return 
 */
function setNovedades() {
    var checkboxValues = "";
    $('input[id^="nov_"]:checked').each(function() {
        checkboxValues += $(this).val() + ",";
    });

    if (checkboxValues == "") {
        swal({
            title: "Asignar Novedades",
            text: "Debes seleccionar por lo menos una novedad.",
            type: "warning"
        });
    } else {
        $("#cod_noveda").val(checkboxValues);
        closePopUp();
    }
}

$( function() {

    var Standa = $( "#standa" ).val();
    $("#trans_perfil").autocomplete({
        source: "../" + Standa + "/seguridad/ajax_perfil_perfil.php?Ajax=on&Option=getNomTrans",
        minLength: 3,
        select: function(event, ui) {
            $("#cod_transp").val(ui.item.id);
        }
    });
});