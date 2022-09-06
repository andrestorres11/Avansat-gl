/* Instrucciones
 antes de enviar de un formulario se deve ejecutar la funcion inc_validar(elementos)
 se debe construir un array en javascript que contiene el elemento y sus parametros
 Ejemplo : [ 
 ['idcampo1','tipo','min','max'], 
 ['idcampo2','tipo','min','max']
 ]
 
 Donde: 
 'idcampo' es el ID del elemento HTML a validar, para inputs tipo RADIO se debe enviar el NAME
 
 'tipo' es la caracteristica a validar del elemento 
 texto     -> Valida solo caracteres entre la a-A y la z-Z, y espacios. EJ: ['nombres','texto',3,15,true]
 email     -> Valida construccion del array de E-mail. EJ: ['email','texto',3,15,true]
 numero   -> Valida solo caracteres entre 0-9 y puntos. EJ: ['hijos','numero',1,2,true]
 alpha    -> Valida solo caracteres entre la a-A y la z-Z, (max,min), espacios y numeros. EJ: ['barrio','alpha',5,15,true]
 dir      -> Valida solo caracteres entre la a-A y la z-Z, (max,min), espacios, comas y (-,_,#,°,.), numeros . EJ: ['direccion','dir',5,15,true]
 password -> Permite todos los caracteres, restriccion por cantidad de caracteres, valida con otro campo similitud. EJ: ['clave','password',8,30,'v_password']
 select   -> Valida que se seleccione una opcion, En el parametro min se envia true si es obligatorio, false si no lo es. EJ: ['ciudad','select',true]
 date     -> Valida que sea una fecha con formato (YYYY-mm-dd). EJ: ['fecha_nacimiento','date',10,10,true]
 compare    -> valida que una fecha no sea mayor que otra EJ: ['fecha_inicio','compare', 'fecha_fin']
 mayor    -> valida que una fecha no sea menor o igual a la fecha actual EJ: ['fecha_inicio','mayor']
 radio    -> Obliga al usuario marcar un check, requiere que se envie el NAME y no el ID. EJ: ['autorizamms','radio']
 checkbox -> Valida que se checkee (min) cajas, se envia como parametro el ID de la tabla que contiene los checks EJ. ['lista_hobbies','checkbox',1]
 onlycheckbox -> Valida que se checkee (min) cajas, se envia como parametro el ID de la tabla que contiene los checks EJ.  ['campo','onlycheck',true],
 textarea -> Valida que el textbox cumpla las parametros EJ. ['observacion','textarea',15,500,true]
 file     -> Valida un input file EJ. ['archivo','file',[jpg,gif]]
 placa     -> valida que sea una placa de vehiculo válida 3 letras seguidas de 3 numeros. EJ: ['num_placa','placa',true]
 
 'min' es el minimo de caracteres permitidos para el campo, en caso de no ser requerido se envia vacio o nulo (null)
 'max' es el maximo de caracteres permitidos para el campo, en caso de no ser requerido se envia vacio o nulo (null)
 
 'obl' true si el campo es obligatorio o false si no lo es, para contraseña parametro que verifica similitud de valores para una contraseña
 alphaarroba -> Valida solo caracteres entre la a-A y la z-Z con anexo @, (max,min), espacios y numeros. EJ: ['barrio','alpha',5,15,true]
 */

//Variable auxiliar que permite el desplazamiento del scroll
var movimiento = true;
/* MAIN QUE RECORRE CADA ELEMENTO HTML EN EL ARRAY Y LO VALIDA DEPENDIENDO SUS PARAMETROS*/
function inc_validar(elementos) {

    movimiento = true;
    var errores = 0; //contador de los errores
    $('.inc_alert').remove(); //limpia los alert

    for (var i = 0; i < elementos.length; i++) {
        var elemento = elementos[i];
        var obj = elemento[0];
        var tipo = elemento[1];
        var min = elemento[2];
        var max = elemento[3];
        var obl = elemento[4]; //obligatorio o para contrasena
        var oblc = elemento[5]; //obligatorio de  contrasena

        if (obj) {
            if (tipo) {
                switch (tipo) {
                    case 'texto':
                        errores += inc_texto(obj, min, max, obl);
                        break;
                    case 'alpha':
                        errores += inc_alpha(obj, min, max, obl);
                        break;
                    case 'alphaarroba':
                        errores += inc_alpha_arroba(obj, min, max, obl);
                        break;
                    case 'numero':
                        errores += inc_numero(obj, min, max, obl);
                        break;
                    case 'dir':
                        errores += inc_dir(obj, min, max, obl);
                        break;
                    case 'date':
                        errores += inc_date(obj, min, max, obl);
                        break;
                    case 'select':
                        errores += inc_select(obj, min);
                        break;
                    case 'email':
                        errores += inc_email(obj, min, max, obl);
                        break;
                    case 'password':
                        errores += inc_password(obj, min, max, obl, oblc);
                        break;
                    case 'compare':
                        errores += inc_compare(obj, min);
                        break;
                    case 'radio':
                        errores += inc_radio(obj);
                        break;
                    case 'checkbox':
                        errores += inc_checkbox(obj, min);
                        break;
                    case 'onlycheck':
                        errores += inc_onlycheck(obj, min);
                        break;
                    case 'textarea':
                        errores += inc_textarea(obj, min, max, obl);
                        break;
                    case 'file':
                        errores += inc_file(obj, min, max);
                        break;
                    case 'mayor':
                        errores += inc_mayor(obj);
                        break;
                    case 'hora':
                        errores += inc_hora(obj, min, max, obl);
                        break;
                    case 'placa':
                        errores += inc_placa(obj, min, max);
                        break;
                }
            } else {
                console.warn("inc_validator: No se ha definido el tipo de parametro para " + obj);
            }
        } else {
            console.warn("inc_validator: Es obligatorio enviar el ID/NAME del objeto a validar");
        }

    }
    if (isNaN(errores)) {
        errores = 0;
    }
    if (errores === 0) {
        inc_remover_alertas(); //remueve todos los mensajes
        return true;
    } else {
        return false;
    }
}
/*FUNCION QUE VALIDA FORMATO DE ARCHIVOS*/
function inc_file(obj, min, obl) {

    if (document.getElementById(obj)) {

        if ($('#' + obj).val() == "" && obl == true) {
            msg = "Debe seleccionar un archivo";
            inc_alerta(obj, msg);
        } else if ($('#' + obj).val() != "") {
            var extension = $('#' + obj).val().split('.').pop().toLowerCase()

            if ($.inArray(extension, min) == -1) {
                $('#' + obj).val("");
                msg = "Formato de archivo no permitido";
                inc_alerta(obj, msg);
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }


    } else {
        console.warn("inc_validator: No se ha encontrado el elemento: " + obj);
        return 1;
    }
}

/* FUNCIONES ESPECIFICAS PARA CADA TIPO DE ELEMENTO*/

function inc_texto(obj, min, max, obl) {

    if (document.getElementById(obj)) {
        if (min >= 0 && max >= 0) {
            var ele = $.trim($('#' + obj).val());
            var tam = ele.length;
            var expreg = /^[A-Za-z\s\xF1\xD1\áÁéÉíÍóÓúÚ]+$/; //Solo texto espacios ñ y Ñ
            if (!expreg.test(ele) && tam > 0) {
                msg = "Se requieren únicamente letras";
                inc_alerta(obj, msg);
                return 1; //suma al error
            } else {
                var cantidad = inc_cantidad(obj, tam, min, max, obl);
                if (cantidad == true) {
                    return 0;
                } else {
                    return 1;
                }
            }
        } else {
            console.warn("inc_validator: Debe definir correctamente los valores de maximo y minimo para " + obj)
            return 1;
        }
    } else {
        console.warn("inc_validator: No se ha encontrado el elemento: " + obj);
        return 1;
    }
}

function inc_numero(obj, min, max, obl) {

    if ($('#' + obj)) {
        if (min >= 0 && max >= 0) {
            var ele = $.trim($('#' + obj).val());
            var tam = ele.length;
            var expreg = /^[0-9\.]+$/; //Solo numeros
            if (!expreg.test(ele) && tam > 0) {
                msg = "Se requieren únicamente numeros";
                inc_alerta(obj, msg);
                return 1; //suma al error
            } else {
                var cantidad = inc_cantidad(obj, tam, min, max, obl);
                if (cantidad == true) {
                    return 0; //suma al error

                } else {
                    return 1; //suma al error
                }
            }


        } else {
            console.warn("inc_validator: Debe definir correctamente los valores de maximo y minimo para " + obj);
            return 1;
        }
    } else {
        console.warn("inc_validator: No se ha encontrado el elemento: " + obj);
        return 1;
    }
}

function inc_hora(obj, min, max, obl) {

    if ($('#' + obj)) {
        if (min >= 0 && max >= 0) {
            var ele = $.trim($('#' + obj).val());
            var tam = ele.length;
            var expreg = /^[0-9\:]+$/; //Solo numeros
            if (!expreg.test(ele) && tam > 0) {
                msg = "Formato de Hora incorrecto 'hh:mm:ss'";
                inc_alerta(obj, msg);
                return 1; //suma al error
            } else {
                var cantidad = inc_cantidad(obj, tam, min, max, obl);
                if (cantidad == true) {
                    return 0; //suma al error

                } else {
                    return 1; //suma al error
                }
            }


        } else {
            console.warn("inc_validator: Debe definir correctamente los valores de maximo y minimo para " + obj);
            return 1;
        }
    } else {
        console.warn("inc_validator: No se ha encontrado el elemento: " + obj);
        return 1;
    }
}

function inc_alpha(obj, min, max, obl) {
    if (document.getElementById(obj)) {
        if (min >= 0 && max >= 0) {

            var ele = $.trim($('#' + obj).val());
            var tam = ele.length;
            var cantidad = inc_cantidad(obj, tam, min, max, obl);
            if (cantidad == true) {

                var expreg = /^[A-Za-z0-9\s\xF1\xD1\áÁéÉíÍóÓúÚ\.\,\-\_]+$/; //Solo letras y numeros espacios ñ y Ñ .,_-
                if (!expreg.test(ele) && tam > 0) {
                    msg = "Se requieren &uacute;nicamente numeros y letras";
                    inc_alerta(obj, msg);
                    return 1; //suma al error
                } else {
                    return 0;
                }

            } else {
                return 1; //suma al error
            }
        } else {
            console.warn("inc_validator: Debe definir correctamente los valores de maximo y minimo para " + obj)
            return 1;
        }
    } else {
        console.warn("inc_validator: No se ha encontrado el elemento: " + obj);
        return 1;
    }
}

function inc_alpha_arroba(obj, min, max, obl) {
    if (document.getElementById(obj)) {
        if (min >= 0 && max >= 0) {

            var ele = $.trim($('#' + obj).val());
            var tam = ele.length;
            var cantidad = inc_cantidad(obj, tam, min, max, obl);
            if (cantidad == true) {

                var expreg = /^[A-Za-z0-9\s\xF1\xD1\áÁéÉíÍóÓúÚ\.\,\@\-\_]+$/; //Solo letras y numeros espacios ñ y Ñ .,_-
                if (!expreg.test(ele) && tam > 0) {
                    msg = "Se requieren &uacute;nicamente numeros,letras y @";
                    inc_alerta(obj, msg);
                    return 1; //suma al error
                } else {
                    return 0;
                }

            } else {
                return 1; //suma al error
            }
        } else {
            console.warn("inc_validator: Debe definir correctamente los valores de maximo y minimo para " + obj)
            return 1;
        }
    } else {
        console.warn("inc_validator: No se ha encontrado el elemento: " + obj);
        return 1;
    }
}

function inc_dir(obj, min, max, obl) {

    if (document.getElementById(obj)) {
        if (min >= 0 && max >= 0) {
            var ele = $.trim($('#' + obj).val());
            var tam = ele.length;
            var cantidad = inc_cantidad(obj, tam, min, max, obl);
            if (cantidad == true) {
                var expreg = /^[A-Za-z0-9\s\xF1\xD1\_\(\)\&\-\/\#\°\.\:\,\áÁéÉíÍóÓúÚ]+$/; //Solo letras y numeros espacios ñ y Ñ
                if (!expreg.test(ele) && tam > 0) {
                    msg = "¡Caracteres no validos!";
                    inc_alerta(obj, msg);
                    return 1; //suma al error
                } else {
                    return 0;
                }
            } else {
                return 1; //suma al error
            }
        } else {
            console.warn("inc_validator: Debe definir correctamente los valores de maximo y minimo para " + obj)
            return 1;
        }
    } else {
        console.warn("inc_validator: No se ha encontrado el elemento: " + obj);
        return 1;
    }
}
//funcion para comparar que la fecha inicial sea menor a la final
function inc_compare(obj, min) {
    var inicio = $.trim($('#' + obj).val());
    var fin = $.trim($('#' + min).val());
    if (inicio >= fin) {
        msg = "La fecha inicial no puede ser mayor o igual a la fecha final";
        inc_alerta(obj, msg);
        return 1;
    } else {
        return 0;
    }
}

//funcion para saber si una fecha es mayor a la actual
function inc_mayor(obj, min) {
    var inicio = $.trim($('#' + obj).val());
    var fin = min;
    var d = new Date();
    var x = "";
    var y = "";
    if ((d.getMonth() + 1) < 10) {
        x = "0";
    }
    if ((d.getDate()) < 10) {
        y = "0";
    }
    var fin = d.getFullYear() + "-" + x + (d.getMonth() + 1) + "-" + y + d.getDate();
    if (Date.parse(inicio) < Date.parse(fin)) {
        msg = "La fecha no puede ser menor a la fecha actual";
        inc_alerta(obj, msg);
        return 1;
    } else {
        return 0;
    }
}

//funcion para saber si una placa es valida
function inc_placa(obj, min, max) {
    var placa = $("#" + obj).val();
    if (placa.length < 6 && placa.length > 0) {
        msg = "La placa debe contener 6 caracteres";
        inc_alerta(obj, msg);
        return 1;
    }
    var expreg = /^[A-Za-z]/; //Solo caracteres alfabeticos
    var expreg2 = /^[0-9]/; //Solo numeros

    if (min == true || placa.length == 6) {
        var placa = $.trim($('#' + obj).val());
        var cod_paisxx = $('#cod_paisxxID').val();
        if(cod_paisxx == 11 && cod_paisxx != undefined && cod_paisxx != ''){
            console.log("entro aqui");
            var inicio = placa.substring(0, 2);
            var medio = placa.substring(2, 4);
            var fin = placa.substring(4, 6);
            if(!expreg.test(inicio) || (!expreg.test(medio) && !expreg2.test(medio)) || !expreg2.test(fin)){
                msg = "La placa introducida no es v�lida";
                inc_alerta(obj, msg);
                return 1;
            }else{
                return 0;
            }
        }else{
            var inicio = placa.substring(0, 3);
            var fin = placa.substring(3, 6);
            if (!expreg2.test(fin) || !expreg.test(inicio) || fin.length != 3) {
                msg = "La placa introducida no es v�lida";
                inc_alerta(obj, msg);
                return 1;
            } else {
                return 0;
            }
        }
    } else {
        return 0;
    }
}

function inc_date(obj, min, max, obl) {

    var ele = $.trim($('#' + obj).val());
    var tam = ele.length;
    if (obl == true) {
        if (tam > 0) {
            if (tam == 10 || tam == 16) {
                return 0;
            } else {
                msg = "Ingrese una fecha valida";
                inc_alerta(obj, msg);
                return 1;
            }
        } else {
            msg = "Ingrese una fecha valida";
            inc_alerta(obj, msg);
            return 1;
        }
    } else {
        if (tam == 0) {
            return 0;
        } else if (tam > 0) {
            if (tam == 10 || tam == 16) {
                return 0;
            } else {
                msg = "Ingrese una fecha valida";
                inc_alerta(obj, msg);
                return 1;
            }
        }
    }
}

function inc_select(obj, obl) {
    if (document.getElementById(obj)) {
        var ele = $.trim($('#' + obj).val());
        if (obl == true) { // es obligatorio seleccionar una opcion
            if (ele == '') {
                msg = "Debe seleccionar una opci�n";
                inc_alerta(obj, msg);
                return 1; //suma al error
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    } else {
        console.warn("inc_validator: No se ha encontrado el elemento: " + obj);
        return 1;
    }
}

function inc_password(obj, min, max, com, oblc) {
    if (document.getElementById(obj)) {
        if (min >= 0 && max >= 0) {
            var ele = $.trim($('#' + obj).val());
            var tam = ele.length;
            var cantidad = inc_cantidad(obj, tam, min, max, oblc);
            var cantidad2 = inc_cantidad(com, tam, min, max, oblc);
            if (cantidad == true && cantidad2 == true) {

                if (com !== undefined) {
                    var ele2 = $.trim($('#' + com).val());
                    if (ele !== ele2) {
                        msg = "Las contrase�as no coinciden";
                        inc_alerta(com, msg);
                        return 1; //suma al error
                    } else {
                        return 0;
                    }
                } else {
                    return 0;
                }

            } else {
                return 1; //suma al error
            }

        } else {
            console.warn("inc_validator: Debe definir correctamente los valores de maximo y minimo para " + obj)
            return 1;
        }
    } else {
        console.warn("inc_validator: No se ha encontrado el elemento: " + obj);
        return 1;
    }
}

function inc_email(obj, min, max, obl) {
    if (document.getElementById(obj)) {
        if (min >= 0 && max >= 0) {
            var correos = $('#' + obj).val();
            var myArray = correos.split(',');
            for (var i = 0; i < myArray.length; i++) {
                var ele = $.trim(myArray[i]);
                var tam = ele.length;
                var cantidad = inc_cantidad(obj, tam, min, max, obl);
                if (cantidad == true) {
                    var expreg = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/; //Solo emails
                    if (!expreg.test(ele) && tam > 0) {
                        msg = "Email no valido verifique los datos ingresados";
                        inc_alerta(obj, msg);
                        return 1; //suma al error
                    } else {
                        return 0;
                    }
                } else {
                    return 1; //suma al error
                }
            }

        } else {
            console.warn("inc_validator: Debe definir correctamente los valores de maximo y minimo para " + obj)
            return 1;
        }
    } else {
        console.warn("inc_validator: No se ha encontrado el elemento: " + obj);
        return 1;
    }
}

function inc_email2(obj, min, max, obl) {
    if (document.getElementById(obj)) {
        if (min >= 0 && max >= 0) {

            var ele = $.trim($('#' + obj).val());
            var tam = ele.length;
            var cantidad = inc_cantidad(obj, tam, min, max, obl);
            if (cantidad == true) {

                var expreg = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/; //Solo emails
                if (!expreg.test(ele) && tam > 0) {
                    msg = "Email no valido verifique los datos ingresados";
                    inc_alerta(obj, msg);
                    return 1; //suma al error
                } else {
                    return 0;
                }

            } else {
                return 1; //suma al error
            }
        } else {
            console.warn("inc_validator: Debe definir correctamente los valores de maximo y minimo para " + obj)
            return 1;
        }
    } else {
        console.warn("inc_validator: No se ha encontrado el elemento: " + obj);
        return 1;
    }
}

function inc_radio(obj) {
    if (document.getElementsByName(obj)) {
        obj = $('[name="' + obj + '"]');
        if ($(obj).is(':checked')) {
            return 0;
        } else {
            obj = obj[0].id;
            msg = "Debe seleccionar una opción";
            inc_alerta(obj, msg);
            return 1;
        }
    } else {
        console.warn("inc_validator: No se ha encontrado el elemento con NAME: " + obj);
        return 1;
    }
}

function inc_checkbox(obj, min) {
    if (document.getElementById(obj)) {
        if (min >= 0) {
            var lista = document.getElementById(obj);
            var checks = lista.getElementsByTagName('input');
            var checkeados = 0;
            for (var i = 0; i < checks.length; i++) {
                if (checks[i].type.toLowerCase() == 'checkbox') {
                    if (checks[i].checked) {
                        checkeados++;
                    }
                }
            }

            if (checkeados < min) {
                msg = "Debe checkear minimo " + min + " caja(s) de la lista";
                inc_alerta(obj, msg);
                return 1;
            } else {
                return 0;
            }
        } else {
            console.warn("inc_validator: Debe definir correctamente los valores de minimo para " + obj)
            return 1;
        }
    } else {
        console.warn("No se ha encontrado el elemento: " + obj);
        return 1;
    }
}

function inc_onlycheck(obj, min) {
    if (document.getElementById(obj)) {

        if (!$('#' + obj).is(':checked')) {
            msg = "Debe seleccionar este check";
            inc_alerta(obj, msg);
            return 1;
        } else {
            return 0;
        }
    } else {
        console.warn("inc_validator: No se ha encontrado el elemento: " + obj);
        return 1;
    }

}

function inc_textarea(obj, min, max, obl) {
    if (document.getElementById(obj)) {
        if (min >= 0 && max >= 0) {
            var ele = $.trim($('#' + obj).val());
            var tam = ele.length;
            var cantidad = inc_cantidad(obj, tam, min, max, obl);
            if (cantidad == true) {
                return 0;
            } else {
                return 1; //suma al error
            }
        } else {
            console.warn("inc_validator: Debe definir correctamente los valores de minimo para " + obj)
            return 1;
        }
    } else {
        console.warn("inc_validator: No se ha encontrado el elemento: " + obj);
        return 1;
    }
}

/* FUNCION QUE VALIDA LA CANTIDAD DE CARACTERES DE LOS ELENTOS*/
function inc_cantidad(obj, tam, min, max, obl) {
    var estado = true;
    var msg = 'Se requieren';
    if (obl == true) {

        if (min != null) {
            if (tam < min) {
                estado = false;
                msg += ' minimo ' + min + ' caracteres';
            }
        } else {
            estado = true;
        }

        if (max != null) {
            if (tam > max) {
                estado = false;
                msg += ' maximo ' + max + ' caracteres';
            }
        } else {
            estado = true;
        }

        if (estado == false) {
            inc_alerta(obj, msg);
        }
    } else {
        if (tam > 0) { //si hay por lo menos un caracter lo vuelve obl para validarlo
            return inc_cantidad(obj, tam, min, max, true);
        } else {
            estado = true;
        }

    }
    return estado;
}
/*FUNCION QUE SE ENCARGA DE HACER VISIBLES LOS MENSAJES DE ALERTA*/
function inc_alerta(obj, msg) {
    var elemento = document.getElementById('inc_val_' + obj);
    var evento = 'onclick = "inc_remover_alerta(this)"';
    var error = '<div class="inc_alert" style="display:none; text-align:center" id="inc_val_' + obj + '" ' + evento + '>' + msg + '</div>';
    if (elemento == null)
        $('#' + obj).after(error);
    else
        $('#' + obj).html(msg);
    if (movimiento == true) {
        $('html, body').animate({
            scrollTop: $('#' + obj).offset().top - 100
        }, 'slow');
        movimiento = false;
    }


    $('#inc_val_' + obj).fadeIn(500);
    msg = "";
}


/*FUNCION QUE SE ENCARGA DE REMOVER LAS ALERTAS AL DAR CLICK*/
function inc_remover_alerta(obj) {
    $(obj).fadeOut(500);
    setTimeout(function() {
        $(obj).remove();
    }, 510);
}

function inc_remover_alertas() {

    $('.inc_alert').fadeOut(500);
    setTimeout(function() {
        $('.inc_alert').remove();
    }, 510);
}


/* EVENTOS */


function inc_eventos(elementos) {

    for (var i = 0; i < elementos.length; i++) {
        var elemento = elementos[i];
        var obj = elemento[0];
        var tipo = elemento[1];
        var min = elemento[2];
        var max = elemento[3];
        var obl = elemento[4]; //obligatorio o para contrasena
        var oblc = elemento[5]; //obligatorio de  contrasena

        if (obj) {
            if (tipo) {
                switch (tipo) {
                    case 'texto':
                        document.getElementById(obj).addEventListener('blur', autoTexto(obj, min, max, obl), false);
                        break;
                    case 'alpha':
                        document.getElementById(obj).addEventListener('blur', autoAlpha(obj, min, max, obl), false);
                        break;
                    case 'numero':
                        document.getElementById(obj).addEventListener('blur', autoNumero(obj, min, max, obl), false);
                        break;
                    case 'dir':
                        document.getElementById(obj).addEventListener('blur', autoDir(obj, min, max, obl), false);
                        break;
                    case 'date':
                        document.getElementById(obj).addEventListener('blur', autoFecha(obj, min, max, obl), false);
                        break;
                    case 'select':
                        document.getElementById(obj).addEventListener('blur', autoSelect(obj, min), false);
                        break;
                    case 'email':
                        document.getElementById(obj).addEventListener('blur', autoEmail(obj, min, max, obl), false);
                        break;
                    case 'password':
                        document.getElementById(obj).addEventListener('blur', autoPass(obj, min, max, obl, oblc), false);
                        break;
                    case 'radio':
                        //evento que se ejcuta al enviar el formulario
                        break;
                    case 'checkbox':
                        //evento que se ejcuta al enviar el formulario
                        break;
                    case 'onlycheck':
                        //evento que se ejcuta al enviar el formulario
                        break;
                    case 'textarea':
                        document.getElementById(obj).addEventListener('blur', autoTextArea(obj, min, max, obl), false);
                        break;
                    case 'file':
                        document.getElementById(obj).addEventListener('blur', autoFile(obj, min), false);
                        break;
                }
            } else {
                console.warn("inc_validator: No se ha definido el tipo de parametro para " + obj);
            }
        } else {
            console.warn("inc_validator: Es obligatorio enviar el ID/NAME del objeto a validar");
        }
    }
}


function autoTexto(obj, min, max, obl) {

    return function() {

        var val = inc_texto(obj, min, max, obl);
        if (val == 0) {
            autoRemover(obj);
        } else {
            //  $('#'+obj).focus();
        }
    }
}

function autoAlpha(obj, min, max, obl) {

    return function() {
        var val = inc_alpha(obj, min, max, obl);
        if (val == 0) {
            autoRemover(obj);
        } else {
            //   $('#'+obj).focus();
        }
    }

}

function autoNumero(obj, min, max, obl) {
    return function() {
        var val = inc_numero(obj, min, max, obl);
        if (val == 0) {
            autoRemover(obj);
        } else {
            //   $('#'+obj).focus();
        }
    }
}

function autoDir(obj, min, max, obl) {
    return function() {
        var val = inc_dir(obj, min, max, obl);
        if (val == 0) {
            autoRemover(obj);
        } else {
            //    $('#'+obj).focus();
        }
    }

}

function autoFecha(obj, min, max, obl) {
    return function() {
        var val = inc_date(obj, min, max, obl);
        if (val == 0) {
            autoRemover(obj);
        } else {
            //   $('#'+obj).focus();
        }
    }
}

function autoSelect(obj, min) {
    return function() {
        var val = inc_select(obj, min);
        if (val == 0) {
            autoRemover(obj);
        } else {
            //   $('#'+obj).focus();
        }
    }
}

function autoEmail(obj, min, max, obl) {
    return function() {
        var val = inc_email(obj, min, max, obl);
        if (val == 0) {
            autoRemover(obj);
        } else {
            //    $('#'+obj).focus();
        }
    }
}

function autoPass(obj, min, max, obl, oblc) {
    return function() {
        var val = inc_password(obj, min, max, obl, oblc);
        if (val == 0) {
            autoRemover(obj);
        } else {
            //   $('#'+obj).focus();
        }
    }
}

function autoTextArea(obj, min, max, obl) {
    return function() {
        var val = inc_textarea(obj, min, max, obl);
        if (val == 0) {
            autoRemover(obj);
        } else {
            //  $('#'+obj).focus();
        }
    }
}

function autoFile(obj, min) {
    return function() {
        var val = inc_file(obj, min);
        if (val == 0) {
            autoRemover(obj);
        } else {
            //  $('#'+obj).focus();
        }
    }
}

function autoRemover(obj) {
    $('#inc_val_' + obj).fadeOut(500);
    setTimeout(function() {
        $('#inc_val_' + obj).remove();
    }, 510);
}
