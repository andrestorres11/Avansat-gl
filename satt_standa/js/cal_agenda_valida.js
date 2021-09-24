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
    var parametros = "Option=busquedaHora";
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
        
            Swal.fire({
                title: 'La agenda de usuario',
                html: `<table class="table table-bordered">
                            <tr>
                                <th>Hora de Cargue:</th>
                                <td>` + data['fec_inicio'] + `</td>
                            </tr>
                            <tr>
                                <th>Hora de Cargue:</th>
                                <td>` + data['fec_final'] + `</td>
                            </tr>
                            <tr>
                                <th>Hora de Cargue:</th>
                                <td>` + data['inicio'] + `</td>
                            </tr>
                            <tr>
                                <th>Hora fin de Cargue:</th>
                                <td>` + data['final'] + `</td>
                            </tr>
                            <tr>
                                <th>Usuario:</th>
                                <td>` + data['usuari'] + `</td>
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
    console.log(form);
    var standa = "satt_standa";
    var parametros = "Option=registraCitacion";
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