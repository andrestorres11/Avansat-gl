function MainLoad() {
    try {
        var Standa = $("#StandaID").val();

        $.ajax({
            type: "POST",
            url: "../" + Standa + "/causaf/ajax_causas_saferb.php",
            data: "option=MainLoad&Standa=" + Standa,
            async: false,
            beforeSend: function() {
                $("#mainFormID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
            },
            success: function(data) {
                $("#mainFormID").html(data);

            }
        });
    } catch (e) {
        console.log(e.message);
        return false;
    }
}

function DeleteCausa(elemento) {
    try {
        var Standa = $("#StandaID").val();

        if (confirm("Realmente Desea Eliminar la Homologacion?")) {
            $.ajax({
                type: "POST",
                url: "../" + Standa + "/causaf/ajax_causas_saferb.php",
                data: "option=DeleteCausa&Standa=" + Standa + "&elemento=" + elemento.text(),
                async: false,
                beforeSend: function() {
                    $("#mainFormID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
                },
                success: function(data) {
                    MainLoad();
                    if (data == '1000') {
                        $("#messageID").css({
                            "background": "none repeat scroll 0 0 #CAFFD7",
                            "border": "1px solid #49E981",
                            "border-radius": "4px 4px 4px 4px",
                            "color": "#333333",
                            "display": "none",
                            "font-family": "Arial",
                            "font-size": "12px",
                            "padding": "10px",
                            "width": "100%"
                        });
                        $("#messageID").html("<span>La Homologacion fue Eliminada Correctamente</span>");
                    } else {
                        $("#messageID").css({
                            "background": "none repeat scroll 0 0 #FFCECE",
                            "border": "1px solid #E18F8E",
                            "border-radius": "4px 4px 4px 4px",
                            "color": "#333333",
                            "display": "none",
                            "font-family": "Arial",
                            "font-size": "12px",
                            "padding": "10px",
                            "width": "100%"
                        });
                        $("#messageID").html("<span>La Homologacion no pudo ser Eliminada, por favor Intente Nuevamente</span>");
                    }
                    $("#messageID").show("slide");
                }
            });
        }
    } catch (e) {
        console.log(e.message);
        return false;
    }
}

function SaveRegistros() {
    try {
        $(".ui-button-text-only:first").hide();
        var Standa = $("#StandaID").val();
        var param = '';
        var counter = 0;
        $(".Result").each(function(index) {
            param += '&comb[' + counter + ']=' + $(this).val();
            counter++;
        });

        if (counter == 0) {
            alert("Digite por lo menos un Homologaci\xf3n para Continuar");
            return false;
        } else {
            $.ajax({
                type: "POST",
                url: "../" + Standa + "/causaf/ajax_causas_saferb.php",
                data: "option=SaveRegistros&Standa=" + Standa + param,
                async: false,
                beforeSend: function() {
                    $("#PopUpID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
                },
                success: function(data) {
                    $("#PopUpID").html(data);
                }
            });
        }

    } catch (e) {
        console.log(e.message);
        return false;
    }
}

function CreateRegistro() {
    try {
        var Standa = $("#StandaID").val();

        $("#PopUpID").dialog({
            modal: true,
            resizable: false,
            draggable: false,
            title: "Nueva Homologaci\xf3n",
            width: 800,
            heigth: 500,
            position: ['middle', 25],
            bgiframe: true,
            closeOnEscape: false,
            show: { effect: "drop", duration: 300 },
            hide: { effect: "drop", duration: 300 },
            open: function(event, ui) {
                $(this).parent().children().children('.ui-dialog-titlebar-close').hide();
            },
            buttons: {
                Guardar: function() {
                    SaveRegistros();
                },
                Cerrar: function() {
                    $(this).dialog('close');
                    MainLoad();
                }
            }
        });

        $.ajax({
            type: "POST",
            url: "../" + Standa + "/causaf/ajax_causas_saferb.php",
            data: "option=FormCreate&Standa=" + Standa,
            async: false,
            beforeSend: function() {
                $("#PopUpID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader2.gif" /></td></tr><tr><td></td></tr></table>');
            },
            success: function(data) {
                $("#PopUpID").html(data);
            }
        });

    } catch (e) {
        console.log(e.message);
        return false;
    }
}

function SetHomologacion() {
    try {
        var Standa = $("#StandaID").val();

        var cod_causax = $("#cod_causaxID");
        var nom_causax = $("#nom_causaxID");
        var noveda = $("#novedaID");


        if (cod_causax.val() == '') {
            alert("Por favor Digite el Codigo de la Causa SAFERBO");
            cod_causax.focus();
            return false;
        } else if (nom_causax.val() == '') {
            alert("Por favor Digite el Nombre de la Causa SAFERBO");
            nom_causax.focus();
            return false;
        } else if (noveda.val() == '') {
            alert("Por favor seleccione la novedad SAT GL a Homologar");
            noveda.focus();
            return false;
        } else {
            $.ajax({
                type: "POST",
                url: "../" + Standa + "/causaf/ajax_causas_saferb.php",
                data: "option=ValidaHomologacion&Standa=" + Standa + "&cod_causax=" + cod_causax.val() + "&nom_causax=" + nom_causax.val() + "&noveda=" + noveda.val(),
                async: false,
                beforeSend: function() {
                    $.blockUI();
                },
                success: function(data) {
                    $.unblockUI();
                    if (data != 'y') {
                        alert("La Causa con Codigo " + data.split("||")[0] + " - " + data.split("||")[1] + ", ya se Encuentra Homologada con la novedad " + data.split("||")[2]);
                        cod_causax.val('');
                        nom_causax.val('');
                        noveda.val('');
                        return false;
                    } else {
                        $("#HomologaTableID").append('<tr><td class="CellInfo1" width="15%" align="center"><input type="hidden" class="Result" value="' + cod_causax.val() + "||" + nom_causax.val() + "||" + noveda.val().split('-')[0].trim() + '" />' + cod_causax.val() + '</td><td class="CellInfo1" width="40%" align="center">' + nom_causax.val() + '</td><td class="CellInfo1" width="45%" align="center">' + noveda.val() + '</td></tr>');
                        cod_causax.val('');
                        nom_causax.val('');
                    }
                }
            });
        }
    } catch (e) {
        console.log(e.message);
        return false;
    }
}
