$(function() {
    
    $(".datetimepicker1").datepicker({
        dateFormat: "yy-mm-dd",
        minDate: '+0D',
        monthNames: ['Enero', 'Febrero', 'Marzo',
            'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre',
            'Octubre', 'Noviembre', 'Diciembre'
        ],
        monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
            'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'
        ],
        dayNamesMin: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
        beforeShow: function() {
            setTimeout(function() {
                $('.ui-datepicker').css('z-index', 99999999999999);
            }, 0);
        }
    });
});


$(".datetimepicker1").change(function() {
    reinicioValoresFranja();
    busquedaFranjas();
});

    /*! \fn: loadSelect
    *  \brief: Funcion que Asignar las opciones por cada campo
    *  \author: Luis Carlos Manrique Boada
    *  \date: 2019-08-02
    *  \param:  
    */
    function loadSelect() {
    //Asignar las opciones por cada campo
    $("select").each(function() {
        var select = $(this).attr("id");
        switch (select) {
            case "usuariID":
                var url = '../satt_standa/config/ins_genera_calend.php?Option=1';
                break;
            default:
                var url = "";
                break;
        }
        

        //Ejecuta la opci?n dependendiendo del campo enviado
        if (url != "") {
            $.ajax({
                url: url,
                type: 'post',
                dataType: 'json',
                success: function(data) {
                    $.each(data, function(key, value) {
                        $("#" + select).append("<option value='" + value[0] + "'>" + value[1] + "</option>");
                    });


                    //Asigna el evento multiselecci?n al campo
                    switch (select) {
                        case "usuariID":
                            $("#usuariID").multiselect();
                            break;
                        default:
                            break;
                    }
                }
            });
        }

    });
}

//FUNCION QUE BUSCA LAS FRANJAS DISPONIBLES PARA ESE DIA Y RELLENA EL CAMPO CORRESPONDIENTE
function busquedaFranjas() {
    var fecha_busqueda = $('.datetimepicker1').val();
    var parametros = "Option=busquedaFranja&fechaBusqueda=" + fecha_busqueda;
    var standa = "satt_standa";
    $.ajax({
        url: "../" + standa + "/config/ajax_agenda_calend.php?" + parametros,
        async: false,
        type: "POST",
        dataType: "json",
        success: function(data) {
            if (data['valores'] != "") {
                $('#FranjasID').empty();
                $('#FranjasID').append(data['valores']);
                $('#FranjasID').removeAttr("disabled");
            } else {
                errorSweet('No se encontro una franja con la fecha ingresada. Intente nuevamente con otra fecha');
            }
        }
    });
}

function reinicioValoresFranja() {
    $('#FranjasID').empty();
    $('#FranjasID').append("<option>Seleccione Opci�n</option>");
    $('#FranjasID').attr("disabled", true);
}


function errorSweet(mensaje) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: mensaje
    })
}

function cargando() {
    var standa = "satt_standa";
    Swal.fire({
        title: 'Cargando',
        text: 'Por favor espere...',
        imageUrl: '../' + standa + '/imagenes/ajax-loader.gif',
        imageAlt: 'Custom image',
        showConfirmButton: false,
    })
}

function successSweet(titulo, mensaje) {
    Swal.fire({
        icon: 'success',
        title: titulo,
        text: mensaje
    })
}

function consultaHoraDisponible() {
    var form = new FormData(document.getElementById('form_addAgenPed'));
    var standa = "satt_standa";
    var parametros = "Option=afterInsert&process=search";
    $.ajax({
        url: "../" + standa + "/config/ajax_agenda_calend.php?" + parametros,
        async: false,
        data: form,
        type: "POST",
        dataType: "json",
        processData: false, // tell jQuery not to process the data
        contentType: false,
        beforeSend: function() { cargando(); },
        success: function(data) {
            let usuarios= "";
            data.forEach(data1 => {
                usuarios += data1['usuari']+", "
            })
            Swal.fire({
                title: 'La agenda de usuario',
                html: `<table class="table table-bordered">
                            <tr>
                                <th>Fecha Inicio:</th>
                                <td>` + data[0]['fec_inicio'] + `</td>
                            </tr>
                            <tr>
                                <th>Fecha Fin:</th>
                                <td>` + data[0]['fec_final'] + `</td>
                            </tr>
                            <tr>
                                <th>Hora Inicio:</th>
                                <td>` + data[0]['inicio'] + `</td>
                            </tr>
                            <tr>
                                <th>Hora Fin:</th>
                                <td>` + data[0]['final'] + `</td>
                            </tr>
                            <tr>
                                <th>Usuario:</th>
                                <td>` + usuarios + `</td>
                            </tr>
                        <table>`,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Confirmar '
            }).then((result) => {
                if (result.value) {
                    registrarCitacion();
                }
            })
            $("table th").css({
                "background": "rgb(241, 196, 27)",
                "color": "rgb(255, 255, 255)"
            });
            
        }
    });
    
}

function registrarCitacion() {
    var form = new FormData(document.getElementById('form_addAgenPed'));
    var standa = "satt_standa";
    var parametros = "Option=afterInsert&process=insert";
    $.ajax({
        url: "../" + standa + "/config/ajax_agenda_calend.php?" + parametros,
        async: false,
        data: form,
        type: "POST",
        dataType: "json",
        processData: false, // tell jQuery not to process the data
        contentType: false,
        beforeSend: function() { cargando(); },
        success: function(data) {
            if (data['estado'] == 1) {
                successSweet("Exito", "Se agendo satisfactoriamente.");
                location.reload();
            } else {
                errorSweet("Verifique datos.");
            }

        }
    });
}

function viewObserva(elemento){
    var valor = $(elemento).val();
    if(valor != "1"){
        $('#observaID').css('display', 'block');
    }else{
        $('#observaID').css('display', 'none');
    }
}

function usuariosPerfil(elemento){
    var valor = $(elemento).val();
    var standa = "satt_standa";
    var parametros = "Option=usuariosPerfil&cod_perfil="+valor;
    $.ajax({
        url: "../" + standa + "/config/ajax_agenda_calend.php?" + parametros,
        async: false,
        type: "POST",
        dataType: "html",
        beforeSend: function() { cargando(); },
        success: function(data) {
            $('#usuariDivID').empty();

            $('#usuariDivID').html(data);
            

            $("#usuariID").multiselect();

            $("#usuariID_input").addClass("form-control");
            $("#usuariID_input").addClass("field");
            Swal.close();
        }
    });
}