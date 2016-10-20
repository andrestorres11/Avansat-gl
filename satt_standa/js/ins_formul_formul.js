/*! \file: ins_formul_formul
 *  \brief: JS para todas las acciones del modulo formularios personalizados
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 22/08/2016
 *  \bug: 
 *  \warning: 
 */


/*! \fn: llenarBoceto
 *  \brief: Llena el boceto del campo formulario
 *  \author: Ing. Fabian Salinas
 *  \date: 22/08/2016
 *  \date modified: dd/mm/aaaa
 *  \param: obj  object  objeto
 *  \return: 
 */
function llenarBoceto(obj) {
    try {
        var id = $(obj).attr('id');
        var val = $(obj).val();

        if (id == 'campo_nombre') {
            $("#boceto_label").html(primerLetraMayus(val) + ": ");
        } else if (id == 'campo_tipo') {
            var data = buildHtml(val);
            $("#boceto_input").html(data.html);

            minMaxSujerido(data.min, data.max, data.lock);
        } else {
            return false;
        }
    } catch (e) {
        console.log("Error Fuction llenarBoceto: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: buildHtml
 *  \brief: Construye el HTML para los input
 *  \author: Ing. Fabian Salinas
 *  \date: 22/08/2016
 *  \date modified: dd/mm/aaaa
 *  \param: type  string  Tipo de input
 *  \return: 
 */
function buildHtml(type) {
    try {
        $("#tabOption").hide();
        switch (type) {
            case 'number':
                return { min: '1', max: '12', lock: false, html: '<input class="campo_texto" type="text">' };
            case 'text':
                return { min: '1', max: '30', lock: false, html: '<input class="campo_texto" type="text">' };
            case 'alpha':
                return { min: '1', max: '30', lock: false, html: '<input class="campo_texto" type="text">' };
            case 'date':
                return { min: '', max: '', lock: true, html: '<input class="campo_texto fechapicker" type="text" placeholder="aaaa-mm-dd">' };
            case 'hour':
                return { min: '', max: '', lock: true, html: '<input class="campo_texto horapicker" type="text" placeholder="00:00:00">' };
            case 'radio':
                return { min: '', max: '', lock: true, html: '<input type="radio" value="Si" name="radio"> Si &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" value="No" name="radio"> No' };
            case 'textarea':
                return { min: '1', max: '255', lock: false, html: '<textarea class="campo_texto"></textarea>' };
            case 'checkbox':
                return { min: '', max: '', lock: true, html: '<input type="checkbox">' };
            case 'select':
                $("#tabOption").show();
                return { min: '', max: '', lock: true, html: '<select id="formul_selectID"><option value="">Seleccione una Opción.</option></select>' };
            case 'file':
                return { min: '', max: '', lock: true, html: '<input type="file">' };
            default:
                return { min: '', max: '', lock: false, html: '' };
        }
    } catch (e) {
        console.log("Error Fuction buildHtml: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: minMaxSujerido
 *  \brief: Sujiere el valor minimo y maximo
 *  \author: Ing. Fabian Salinas
 *  \date: 22/08/2016
 *  \date modified: dd/mm/aaaa
 *  \param: min  integer
 *  \return: string
 */
function minMaxSujerido(min, max, lock) {
    try {
        var cMin = $("#campo_min");
        var cMax = $("#campo_max");

        cMin.val(min);
        cMax.val(max);

        if (lock) {
            cMin.attr('disabled', 'disabled');
            cMax.attr('disabled', 'disabled');
        } else {
            cMin.removeAttr('disabled');
            cMax.removeAttr('disabled');
        }
    } catch (e) {
        console.log("Error Fuction minMaxSujerido: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: validateFormFormulCampos
 *  \brief: Valida el formulario de nuevos campos para los formularios dinamicos
 *  \author: Ing. Fabian Salinas
 *  \date: 26/08/2016
 *  \date modified: dd/mm/aaaa
 *  \param:
 *  \return: boolean
 */
function validateFormFormulCampos() {
    try {
        $(".inc_alert").remove();
        var val = validaciones();
        var va2 = true;
        var msj = '';

        if ($("#campo_min").val() > 255 || $("#campo_max").val() > 255) {
            msj = "La longitud no puede exceder los 255 caracteres.";
            va2 = false;

            setTimeout(function() {
                inc_alerta('campo_min', msj);
                inc_alerta('campo_max', msj);
            }, 600);
        }

        if ($("#formul_selectID").html() == '<option value="">Seleccione una Opción.</option>') {
            va2 = false;
            setTimeout(function() {
                inc_alerta('opcionID', 'Digite opciones para la lista.');
            }, 600);
        }

        if (val == true && va2 == true) {
            return true;
        } else {
            return false;
        }
    } catch (e) {
        console.log("Error Fuction validateFormFormulCampos: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: saveCampo
 *  \brief: Genera la petición Ajax para guardar el nuevo campo
 *  \author: Ing. Fabian Salinas
 *  \date: 22/08/2016
 *  \date modified: dd/mm/aaaa
 *  \param: 
 *  \return: 
 */
function saveFormulCampo() {
    try {
        if (!validateFormFormulCampos()) {
            return false;
        }

        var tipo = $("#campo_tipo").val();
        var standa = $("#standaID").val();

        var attributes = 'Ajax=on&Option=saveCampo';
        attributes += "&campo[min]=" + $("#campo_min").val();
        attributes += "&campo[max]=" + $("#campo_max").val();
        attributes += "&campo[tipo]=" + tipo;
        attributes += "&campo[nombre]=" + $("#campo_nombre").val();
        attributes += "&standa=" + standa;

        if (tipo == 'select') {
            attributes += "&campo[html]=" + $("#formul_selectID").html();
        }

        $.ajax({
            url: "../" + standa + "/formul/ins_formul_campos.php",
            type: "POST",
            data: attributes,
            async: true,
            beforeSend: function() {
                BlocK('Guardando...', true);
            },
            success: function(datos) {
                pintarAlertaFormul("Registrar Campo", "Nuevo campo de formulario registrador con exito.", datos);
                if (datos == '1') {
                    printInformCamposRegistrados();
                }
            },
            complete: function() {
                BlocK();
            }
        });
    } catch (e) {
        console.log("Error Fuction saveFormulCampo: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: printInformCamposRegistrados
 *  \brief: Pinta los campos registrados
 *  \author: Ing. Fabian Salinas
 *  \date: 23/08/2016
 *  \date modified: dd/mm/aaaa
 *  \param:     
 *  \return: 
 */
function printInformCamposRegistrados() {
    try {
        var standa = $("#standaID").val();
        var attributes = 'Ajax=on&Option=informCamposRegistrados';
        attributes += "&standa=" + standa;

        $.ajax({
            url: "../" + standa + "/formul/ins_formul_campos.php",
            type: "POST",
            data: attributes,
            async: true,
            beforeSend: function() {
                BlocK('Cargando...', true);
            },
            success: function(datos) {
                $("#infoID").html(datos);
            },
            complete: function() {
                BlocK();
            }
        });
    } catch (e) {
        console.log("Error Fuction printInformCamposRegistrados: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: primerLetraMayus
 *  \brief: Primer letra de un string a mayuscula
 *  \author: Ing. Fabian Salinas
 *  \date: 23/08/2016
 *  \date modified: dd/mm/aaaa
 *  \param: string
 *  \return: string
 */
function primerLetraMayus(string) {
    try {
        return string.charAt(0).toUpperCase() + string.slice(1);
    } catch (e) {
        console.log("Error Fuction primerLetraMayus: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: addOptionFormulCampos
 *  \brief: Agrega opciones al select de Nuevo campo formulario custom
 *  \author: Ing. Fabian Salinas
 *  \date: 23/08/2016
 *  \date modified: dd/mm/aaaa
 *  \param:     
 *  \return: 
 */
function addOptionFormulCampos() {
    try {
        var val = $("#opcionID");
        $("#formul_selectID").append('<option value="' + val.val() + '">' + val.val() + '</option>');
        val.val('');
    } catch (e) {
        console.log("Error Fuction addOptionFormulCampos: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: activarFormulCampo
 *  \brief: Genera la petición ajax para activar o desactivar un Campo de formulario 
 *  \author: Ing. Fabian Salinas
 *  \date: 24/08/2016
 *  \date modified: dd/mm/aaaa
 *  \param: obj  object
 *  \return: 
 */
function activarFormulCampo(obj, estado) {
    try {
        var standa = $("#standaID").val();

        var attributes = 'Ajax=on&Option=activarFormulCampo';
        attributes += "&standa=" + standa;
        attributes += "&estado=" + estado;
        attributes += "&consec=" + $(obj).parent().parent().find("[name^='cod_consec']").val();

        $.ajax({
            url: "../" + standa + "/formul/ins_formul_campos.php",
            type: "POST",
            data: attributes,
            async: true,
            beforeSend: function() {
                BlocK('Cargando...', true);
            },
            success: function(datos) {
                pintarAlertaFormul("Actualizar Estado", "Se actualizo el estado del campo con exito.", datos);
                if (datos == '1') {
                    printInformCamposRegistrados();
                }
            },
            complete: function() {
                BlocK();
            }
        });
    } catch (e) {
        console.log("Error Fuction activarFormulCampo: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: pintarAlertaFormul
 *  \brief: Pinta las alertas para formul campos
 *  \author: Ing. Fabian Salinas
 *  \date: 24/08/2016
 *  \date modified: dd/mm/aaaa
 *  \param: titulo  string  Titulo de la alerta
 *  \param: txt     string  Contenido mensaje exitoso
 *  \param: val     string  Resultado del Ajax
 *  \return: 
 */
function pintarAlertaFormul(titulo, txt, val) {
    try {
        if (val == '1') {
            swal({
                title: titulo,
                text: txt,
                type: "success"
            });
        } else {
            swal({
                title: titulo,
                text: "Ocurrio un error interno, por favor comuníquese con soporte.",
                type: "error"
            });
        }
    } catch (e) {
        console.log("Error Fuction pintarAlertaFormul: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: formularioEditarCampo
 *  \brief: Genera la peticion Ajax para crear el formulario de edicion de campos
 *  \author: Ing. Fabian Salinas
 *  \date: 24/08/2016
 *  \date modified: dd/mm/aaaa
 *  \param: obj  object
 *  \return: 
 */
function formularioEditarCampo(obj) {
    try {
        var standa = $("#standaID").val();

        var attributes = 'Ajax=on&Option=formularioEditarCampo';
        attributes += "&standa=" + standa;
        attributes += "&consec=" + $(obj).parent().parent().find("[name^='cod_consec']").val();

        LoadPopupJQ('open', 'Editar Campo de Formulario', 'auto', 'auto', false, true, true);
        var popup = $("#popID");

        $.ajax({
            url: "../" + standa + "/formul/ins_formul_campos.php",
            type: "POST",
            data: attributes,
            async: true,
            beforeSend: function() {
                popup.parent().children().children('.ui-dialog-titlebar-close').hide();
                popup.html("<center><img src=\"../" + standa + "/imagenes/ajax-loader.gif\"></center>");
            },
            success: function(datos) {
                popup.html(datos);
            }
        });
    } catch (e) {
        console.log("Error Fuction formularioEditarCampo: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: saveEditFormulCampo
 *  \brief: Genera la peticion Ajax para guardar la edicion de un campo de formulario
 *  \author: Ing. Fabian Salinas
 *  \date: 24/08/2016
 *  \date modified: dd/mm/aaaa
 *  \param: 
 *  \return: 
 */
function saveEditFormulCampo(consec, longitud) {
    try {
        var val = true;
        var nombre = $("#edit_campo_nombre");
        var error = inc_texto("edit_campo_nombre", nombre.attr('minlength'), nombre.attr('maxlength'), nombre.attr('obl'));

        if (longitud == 1) {
            var min = $("#edit_campo_min");
            var max = $("#edit_campo_max");

            if (min.val() > 255 || max.val() > 255) {
                val = false;
                msj = "La longitud no puede exceder los 255 caracteres.";

                setTimeout(function() {
                    inc_alerta('edit_campo_min', msj);
                    inc_alerta('edit_campo_max', msj);
                }, 600);
            }
        }

        if (!val || error == 1) {
            return false;
        }

        var standa = $("#standaID").val();

        var attributes = 'Ajax=on&Option=saveEditCampo';
        attributes += "&campo[min]=" + $("#edit_campo_min").val();
        attributes += "&campo[max]=" + $("#edit_campo_max").val();
        attributes += "&campo[consec]=" + consec;
        attributes += "&campo[nombre]=" + $("#edit_campo_nombre").val();

        $.ajax({
            url: "../" + standa + "/formul/ins_formul_campos.php",
            type: "POST",
            data: attributes,
            async: true,
            beforeSend: function() {
                BlocK('Cargando...', true);
            },
            success: function(datos) {
                pintarAlertaFormul("Editar Campo", "Se editó el campo con exito.", datos);
                if (datos == '1') {
                    closePopUp();
                    printInformCamposRegistrados();
                }
            },
            complete: function() {
                BlocK();
            }
        });
    } catch (e) {
        console.log("Error Fuction saveEditFormulCampo: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: pintarBocetoFormulario
 *  \brief: Pinta el Boceto del formulario
 *  \author: Ing. Fabian Salinas
 *  \date: 31/08/2016
 *  \date modified: dd/mm/aaaa
 *  \param: 
 *  \return: 
 */
function pintarBocetoFormulario(id) {
    try {
        var tab = $("#tabBoceto" + id);
        var html = '';
        var cerrar = true;
        var checked;

        $("#formul_selected" + id + " li").each(function(i) {
            if (i == 0 || (!i % 2 && i != 1)) {
                html += '<tr>';
            }

            if ($(this).attr('obl') == 1) {
                checked = 'checked';
            } else {
                checked = '';
            }

            html += '<td width="20%" align="right" class="cellInfo1">';
            html += '<input type="checkbox" class="campo_texto" value="' + $(this).attr('cod_consec') + '" id="obligatorio_' + $(this).attr('cod_consec') + '" ' + checked + ' >';
            html += '</td>';
            html += '<td width="5%" align="left" class="cellInfo1">';
            html += '<label>' + $(this).html() + '</label>';
            html += '</td>';
            html += '<td width="25%" align="left" class="cellInfo1">';
            html += $(this).attr('val_htmlxx');
            html += '</td>';

            if (i % 2) {
                html += '</tr>';
                cerrar = false;
            } else {
                cerrar = true;
            }
        });

        if (cerrar) {
            html += '<td width="50%" colspan="3" class="cellInfo1">&nbsp;</td>';
            html += '</tr>';
        }

        tab.html(html);
    } catch (e) {
        console.log("Error Fuction pintarBocetoFormulario: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: llenarBocetoFormulario
 *  \brief: Agrega el Titulo del Formulario
 *  \author: Ing. Fabian Salinas
 *  \date: 31/08/2016
 *  \date modified: dd/mm/aaaa
 *  \param: obj  Object
 *  \return: 
 */
function llenarBocetoFormulario(obj) {
    try {
        var val = $(obj).val();

        if (val.length > 4) {
            $("#tituloBoceto").html(val);
        } else {
            $("#tituloBoceto").html('Boceto del Formulario');
        }
    } catch (e) {
        console.log("Error Fuction llenarBocetoFormulario: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: saveFormul
 *  \brief: Genera la peticion ajax para guardar el nuevo formulario
 *  \author: Ing. Fabian Salinas
 *  \date: 31/08/2016
 *  \date modified: dd/mm/aaaa
 *  \param:
 *  \return: 
 */
function saveFormul() {
    try {
        var val = validaciones();
        var standa = $("#standaID").val();
        var attributes = 'Ajax=on&Option=saveFormul';
        var n = 0;
        var obl;

        $("#formul_selected li").each(function(i) {
            consec = $(this).attr('cod_consec');

            if ($("#obligatorio_" + consec).is(":checked")) {
                obl = 1;
            } else {
                obl = 0;
            }

            attributes += "&campos[" + consec + "]=" + obl;
            n++;
        });

        if (n < 2) {
            val = false;

            setTimeout(function() {
                inc_alerta('formul_selected', "Seleccione minimo dos campos para el formulario.");
            }, 500);
        }

        if (!val) {
            return false;
        }

        attributes += "&nombre=" + $("#formul_nombre").val();
        attributes += "&tipo=" + $("#formul_tipo").val();

        $.ajax({
            url: "../" + standa + "/formul/ins_formul_formul.php",
            type: "POST",
            data: attributes,
            async: true,
            beforeSend: function() {
                BlocK('Cargando...', true);
            },
            success: function(datos) {
                pintarAlertaFormul("Nuevo Formulario", "Se guardo el nuevo formulario con exito.", datos);
                if (datos == '1') {
                    closePopUp();
                    window.location.reload();
                }
            },
            complete: function() {
                BlocK();
            }
        });
    } catch (e) {
        console.log("Error Fuction saveFormul: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: printInformFormulariosRegistrados
 *  \brief: Pinta los campos registrados
 *  \author: Ing. Fabian Salinas
 *  \date: 23/08/2016
 *  \date modified: dd/mm/aaaa
 *  \param:     
 *  \return: 
 */
function printInformFormulariosRegistrados() {
    try {
        var standa = $("#standaID").val();
        var attributes = 'Ajax=on&Option=informFormulariosRegistrados';
        attributes += "&standa=" + standa;

        $.ajax({
            url: "../" + standa + "/formul/ins_formul_formul.php",
            type: "POST",
            data: attributes,
            async: true,
            beforeSend: function() {
                BlocK('Cargando...', true);
            },
            success: function(datos) {
                $("#infoID").html(datos);
            },
            complete: function() {
                BlocK();
            }
        });
    } catch (e) {
        console.log("Error Fuction printInformFormulariosRegistrados: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: activarFormul
 *  \brief: Genera la petición ajax para activar o desactivar un Campo de formulario 
 *  \author: Ing. Fabian Salinas
 *  \date: 24/08/2016
 *  \date modified: dd/mm/aaaa
 *  \param: obj  object
 *  \return: 
 */
function activarFormul(obj, estado) {
    try {
        var standa = $("#standaID").val();

        var attributes = 'Ajax=on&Option=activarFormul';
        attributes += "&estado=" + estado;
        attributes += "&consec=" + $(obj).parent().parent().find("[name^='cod_consec']").val();

        $.ajax({
            url: "../" + standa + "/formul/ins_formul_formul.php",
            type: "POST",
            data: attributes,
            async: true,
            beforeSend: function() {
                BlocK('Cargando...', true);
            },
            success: function(datos) {
                pintarAlertaFormul("Actualizar Estado", "Se actualizo el estado del formulario con exito.", datos);
                if (datos == '1') {
                    printInformFormulariosRegistrados();
                }
            },
            complete: function() {
                BlocK();
            }
        });
    } catch (e) {
        console.log("Error Fuction activarFormul: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: formularioEditarFormul
 *  \brief: Genera la petición ajax para pintar el formulario de edicion de formularios personalizados
 *  \author: Ing. Fabian Salinas
 *  \date: 01/09/2016
 *  \date modified: dd/mm/aaaa
 *  \param:
 *  \return: 
 */
function formularioEditarFormul(obj) {
    try {
        var standa = $("#standaID").val();

        var attributes = 'Ajax=on&Option=formularioEditarFormul';
        attributes += "&consec=" + $(obj).parent().parent().find("[name^='cod_consec']").val();

        //Load PopUp
        LoadPopupJQ('open', 'Editar Formulario', ($(window).height() - 50), ($(window).width() - 50), false, false, true);
        var popup = $("#popID");

        $.ajax({
            url: "../" + standa + "/formul/ins_formul_formul.php",
            type: "POST",
            data: attributes,
            async: false,
            beforeSend: function() {
                popup.parent().children().children('.ui-dialog-titlebar-close').hide();
                popup.html("<center><img src=\"../" + standa + "/imagenes/ajax-loader.gif\"></center>");
            },
            success: function(data) {
                popup.html(data);
                popup.css('overflow-y', 'true');
                popup.css('overflow-x', 'true');
            },
            complete: function() {
                $("#formul_list_edit, #formul_selected_edit").sortable({
                    connectWith: ".connectedSortableEdit",
                    stop: function() {
                        pintarBocetoFormulario("_edit");
                    },
                    out: function(event, ui) {
                        if (ui.item.attr('property') == 'selected') {
                            $("#formul_selected_edit").sortable("cancel");
                        }
                    }
                }).disableSelection();

                pintarBocetoFormulario("_edit");
            }
        });
    } catch (e) {
        console.log("Error Fuction formularioEditarFormul: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: saveEditFormul
 *  \brief: Genera la Peticion ajax para guardar la edicion de un formulario
 *  \author: Ing. Fabian Salinas
 *  \date: 01/09/2016
 *  \date modified: dd/mm/aaaa
 *  \param: 
 *  \return: 
 */
function saveEditFormul(){
    try {
        var val = true;
        var standa = $("#standaID").val();
        var attributes = 'Ajax=on&Option=saveEditFormul';
        var n = 0;
        var obl;
        var nombre = $("#formul_nombre_edit").val();

        $("#formul_selected_edit li").each(function(i) {
            consec = $(this).attr('cod_consec');

            if ($("#tabBoceto_edit #obligatorio_" + consec).is(":checked")) {
                obl = 1;
            } else {
                obl = 0;
            }

            attributes += "&campos[" + consec + "]=" + obl;
            n++;
        });

        if (n < 2) {
            val = false;

            setTimeout(function() {
                inc_alerta('formul_selected_edit', "Seleccione minimo dos campos para el formulario.");
            }, 500);
        }

        if( nombre == '' || nombre.length < 5 ) {
            val = false;

            setTimeout(function() {
                inc_alerta('formul_nombre_edit', "Este Campo es obligatorio y requiere minimo 5 caracteres.");
            }, 500);
        }

        if (!val) {
            return false;
        }

        attributes += "&nombre=" + nombre;
        attributes += "&consec=" + $("#formul_consec_edit").val();

        $.ajax({
            url: "../" + standa + "/formul/ins_formul_formul.php",
            type: "POST",
            data: attributes,
            async: true,
            beforeSend: function() {
                BlocK('Cargando...', true);
            },
            success: function(datos) {
                pintarAlertaFormul("Nuevo Formulario", "Se guardo el nuevo formulario con exito.", datos);
                if (datos == '1') {
                    closePopUp();
                    printInformFormulariosRegistrados();
                }
            },
            complete: function() {
                BlocK();
            }
        });
    } catch (e) {
        console.log("Error Fuction saveEditFormul: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}