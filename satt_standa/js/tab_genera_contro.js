/* ! \file: ins_genera_viasxx
 *  \brief: permite visualizar correctamente las vistas en ins_genera_viasxx.php ademas de realizar algunas funciones ajax
 *  \author: Ing. Andres torres
 *  \version: 1.0
 *  \date: 14/09/2021
 *  \bug: 
 *  \warning: 
 */
var standa = 'satt_standa';



function almacenarDatos() {
    var standa = 'satt_standa';
    var opcion = 'registrar';
    var dataString = 'option=' + opcion;
    $.ajax({
        url: "../" + standa + "/pcontr/ajax_genera_contro.php?" + dataString,
        method: 'POST',
        data: $(".FormularioVia").serialize(),
        async: false,
        dataType: "json",
        success: function(data) {
            if (data['status'] == 200) {
                Swal.fire({
                    title: 'Registrado!',
                    text: data['response'],
                    type: 'success',
                    confirmButtonColor: '#336600'
                }).then((result) => {
                    if (result.value) {
                        location.reload();
                    }
                })
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data['response'],
                    type: 'error',
                    confirmButtonColor: '#336600'
                })
            }

        }
    });
}

function edit(cod) {
    var standa = 'satt_standa';
    var opcion = 'edit-forminput';
    var dataString = 'option=' + opcion + '&cod=' + cod;
    $.ajax({
        url: "../" + standa + "/pcontr/ajax_genera_contro.php?" + dataString,
        method: 'POST',
        data: '',
        async: false,
        dataType: "json",
        success: function(data) {
            $("#modal-edit").empty();
            $("#modal-edit").append(data);
            $("#editService").modal("show");
        }
    });
}

function updEst(objet) {
    var estText = $(objet).attr('data-estado') == 1 ? 'desactivar' : 'activar';
    try {
        Swal.fire({
            title: decode_utf8('¿Estas seguro?'),
            text: decode_utf8("¿Estas seguro que desea " + estText + " este registro?"),
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#336600',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Si, confirmar'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: "../" + standa + "/pcontr/ajax_genera_contro.php",
                    type: "post",
                    data: ({ option: 'updEst', estado: $(objet).attr('data-estado'), cod_tpcont: $(objet).val() }),
                    dataType: "json",
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Cargando',
                            text: 'Por favor espere...',
                            imageUrl: '../' + standa + '/imagenes/ajax-loader.gif',
                            imageAlt: 'Custom image',
                            showConfirmButton: false,
                        })
                    },
                    success: function(data) {
                        if (data['status'] == 200) {
                            Swal.fire({
                                title: 'Registrado!',
                                text: data['response'],
                                type: 'success',
                                confirmButtonColor: '#336600'
                            }).then((result) => {
                                if (result.value) {
                                    var table = $('#contenedor #tablaRegistros').DataTable();
                                    table.ajax.reload();
                                }
                            })
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data['response'],
                                type: 'error',
                                confirmButtonColor: '#336600'
                            })
                        }
                    }
                });
            }
        });
    } catch (error) {
        console.error(error);
    }
}

function decode_utf8(word) {
    return decodeURIComponent(escape(word));
}

function editCont(tipo, objeto) {
	var DLRow = $(objeto).parent().parent();
    var cod_contro = DLRow.find("input[id^=cod_contro]").val();

    if(tipo == 1){
        confirmar('activar',cod_contro);
    }else if (tipo == 2) {
		confirmar('inactivar',cod_contro);
	}else{
        LoadPopupJQNoButton('open', 'Confirmar Operación', 'auto', 'auto', false, false, true);
		var popup = $("#popID");
		var msj = "<div style='text-align:center'>¿Está seguro de <b>editar</b> el Puesto  de control : <b>" + cod_contro + "?</b><br><br><br><br>";
		msj += "<input type='button' name='si' id='siID' value='Si' style='cursor:pointer' onclick='editarPuestoContro("+cod_contro+")' class='crmButton small save'/> &nbsp;&nbsp;&nbsp;&nbsp";
		msj += "<input type='button' name='no' id='noID' value='No' style='cursor:pointer' onclick='closePopUp()' class='crmButton small save'/><div>";

		popup.parent().children().children('.ui-dialog-titlebar-close').hide();
		popup.append(msj); // //lanza el popUp
    }
	/*var num_trayle = DLRow.find("input[id^=num_trayle]").val();
	var nom_propie = DLRow.find("input[id^=nom_propie]").val();
	$("#cod_tercerID").val(num_trayle);
	$("#nom_tercerID").val(nom_propie);

	if (tipo == 1) {
		confirmar('activar');
	} else if (tipo == 2) {
		confirmar('inactivar');
	} else {
		LoadPopupJQNoButton('open', 'Confirmar Operación', 'auto', 'auto', false, false, true);
		var popup = $("#popID");
		var conductor = $("#nom_tercerID").val();
		var msj = "<div style='text-align:center'>¿Está seguro de <b>editar</b> el Trayler de: <b>" + conductor + "?</b><br><br><br><br>";
		msj += "<input type='button' name='si' id='siID' value='Si' style='cursor:pointer' onclick='formulario()' class='crmButton small save'/> &nbsp;&nbsp;&nbsp;&nbsp";
		msj += "<input type='button' name='no' id='noID' value='No' style='cursor:pointer' onclick='closePopUp()' class='crmButton small save'/><div>";

		popup.parent().children().children('.ui-dialog-titlebar-close').hide();
		popup.append(msj); // //lanza el popUp
	}*/
}

function confirmar(operacion,id) {

    LoadPopupJQNoButton('open', 'Confirmar Operaci&oacute;n', 'auto', 'auto', false, false, true);
    var popup = $("#popID");

    var onclick = "onclick='registrar(\"";
    onclick += operacion;
    onclick += "\","+id+")'";

    if (operacion == "inactivar") {
        operacion = "inactivar";
    } else if (operacion == "activar") {
        operacion = "activar";
    }
    var msj = "<div style='text-align:center'>&#191;Est&aacute; seguro de <b>" + operacion + "</b> el puesto de control #: <b>" + id + "?</b><br><br><br><br>";
    msj += "<input type='button' name='si' id='siID' value='Si' style='cursor:pointer' " + onclick + " class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/> &nbsp;&nbsp;&nbsp;&nbsp";
    msj += "<input type='button' name='no' id='noID' value='No' style='cursor:pointer' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><div>";

    popup.parent().children().children('.ui-dialog-titlebar-close').hide();

    popup.append(msj); // //lanza el popUp
}


function registrar(operacion,id) {
    //cierra popUp si hay inicialiado
    LoadPopupJQNoButton('close');
    var standa = $("#standaID").val();


    let estado = 0;
    if (operacion == "inactivar") {
        estado = 1;
    } else if (operacion == "activar") {
        estado = 0;
    }
    LoadPopupJQNoButton('open', 'Resultado de la Operaci&oacute;n', 'auto', 'auto', false, false, true);
    var popup = $("#popID");

    $.ajax({
        url: "../" + standa + "/pcontr/ajax_genera_contro.php",
        type: "post",
        data: ({ option: 'updEst', estado: estado, cod_contro: id }),
        async: false,
        beforeSend: function() {
            popup.parent().children().children('.ui-dialog-titlebar-close')?.hide();
        },
        success: function(data) {
            popup.append(data); // lanza el popUp
        }
    });

}

function nuevoPuestoContro(){

    try {
        var cod_servic = $("#cod_servicID").val();
        var url = "index.php?window=central&cod_servic="+cod_servic+"&option=1";
    
        var opciones = "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=yes, width=1000, height=600, top=30, left=140";
        window.open( url, 'popupBusqVeh', opciones); 
        
    } catch (e) {
        alert("Error " + e.message);
    }

}

function editarPuestoContro(id){

    try {
        var cod_servic = $("#cod_servicID").val();
        var url = "index.php?window=central&cod_servic="+cod_servic+"&option=2&cod="+id;
    
        var opciones = "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=yes, width=1000, height=600, top=30, left=140";
        window.open( url, 'popupBusqVeh', opciones); 
        
    } catch (e) {
        alert("Error " + e.message);
    }

}

function exportExcel(){
    
    try {
        window.open("../satt_standa/pcontr/export_excelx.php")
    } catch (e) {
        console.log("Error Fuction pintar: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
  }