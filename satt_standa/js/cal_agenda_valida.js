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

function buscarUsuario(){
    
    var usuario = $("#usuarioID").val();
    
    /* Inicializar los eventos externos
     -----------------------------------------------------------------*/
     function ini_events(ele) {
        ele.each(function() {

            // Crea un eneto como obejto 
            // No necesita tener un comienzo o un final
            var eventObject = {
                title: $.trim($(this).text()) // Usa el texto del elemento como t�tulo del evento
            }

            // Almacenar el objeto de evento en el elemento DOM para que podamos acceder a �l m�s tarde
            $(this).data('eventObject', eventObject)

            // Hacer que el evento sea arrastrable usando jQuery UI
            $(this).draggable({
                zIndex: 1070,
                revert: true, // Har� que el evento regrese a su contenedor
                revertDuration: 0 // Posici�n original despu�s del arrastre
            })

        })
    }

    ini_events($('#external-events div.external-event'))

    /* Inicializa el calendario
     -----------------------------------------------------------------*/
    //Declaraci�n de los agendamientos y generaci�n de las mismas
    var Calendar = FullCalendar.Calendar;
    var Draggable = FullCalendarInteraction.Draggable;

    var containerEl = document.getElementById('external-events');
    var checkbox = document.getElementById('drop-remove');
    //$('#calendarPadre').getElementById();
    document.getElementById('calendarPadre').innerHTML='<div id="calendar"></div>';
    var calendarEl = document.getElementById('calendar');
    //Parametros necesarios
    var standa = "satt_standa";
    var parametros = "Option=viewAgendaUsuari&usuario="+usuario;

    var calendar = new Calendar(calendarEl, {
        plugins: ['bootstrap', 'interaction', 'dayGrid', 'timeGrid'],
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay',
        },
        events: "../" + standa + "/config/ajax_agenda_calend.php?" + parametros,
        //droppable : true, // this allows things to be dropped onto the calendar !!!
        //editable  : true,
        eventLimit: true, // when too many events in a day, show the popover
        locale: 'es',
        eventResize: function(obj) { // Cambiar el tama�o para aumentar o disminuir el tiempo del agendamiento
            updateAgendaDet(obj, standa);
        },
        eventDrop: function(obj) { // Evento para soltar y cambiar las fechas del agendamiento 
            updateAgendaDet(obj, standa);
        },
        eventClick: function(info) { // Evento para ver la informaci�n al detalle del agendamiento
            mostrarDatosTurno(info);
        },
        eventRender: function(info) {
            /*console.log(info.event.title);
            $(info.el).tooltip({ 
                title: info.event.title 
            });

            var tooltip = new Tooltip(info.el, {
              title: "Prueba",
              placement: 'top',
              trigger: 'hover',
              container: 'body'
            });*/

        }
    });

    // Renderizar calendario
    calendar.render();
    // $('#calendar').fullCalendar()


    //Evento para crear o modificar agendamientos
    $("#add-new-event").on("click", function() {
        addAgendPed(calendar);
    });

    //Evento para crear o modificar agendamientos Cliente retira
    $("#add-new-event-cl").on("click", function() {
        addAgendPedCl(calendar);

    });

    /* Eventos adicionales */
    var colorChooser = $('#color-chooser-btn');
    $('#color-chooser > li > a').click(function(e) {
        e.preventDefault();
        //Save color
        currColor = $(this).css('color')
            //Add color effect to button
        $('#add-new-event').css({
            'background-color': currColor,
            'border-color': currColor
        });
    })

    // Rango de fechas
    $('#reservation').daterangepicker()
    var date = new Date();
    // Selector de rango de fechas con selector de tiempo
    $('.rango').daterangepicker({
        minDate: new Date(date.getFullYear(), date.getMonth(), date.getDate()),
        timePicker: true,
        language: 'es',
        timePicker24Hour: true,
        use24hours: true,
        timePickerIncrement: 30,
        drops: "up",
        locale: {
            format: 'YYYY-MM-DD HH:mm',
            "separator": " - ",
            "applyLabel": "Aplicar",
            "cancelLabel": "Cancelar",
            "fromLabel": "DE",
            "toLabel": "HASTA",
            "customRangeLabel": "Custom",
            "daysOfWeek": [
                "Dom",
                "Lun",
                "Mar",
                "Mie",
                "Jue",
                "Vie",
                "S&aacute;b"
            ],
            "monthNames": [
                "Enero",
                "Febrero",
                "Marzo",
                "Abril",
                "Mayo",
                "Junio",
                "Julio",
                "Agosto",
                "Septiembre",
                "Octubre",
                "Noviembre",
                "Diciembre"
            ],
        }
    })

    // Limpiar el campo de tango de fechas 
    $('.rango').val("");

    // Evento que trae los pedidos agendados y sin agendar para luego cargalos en el formulario y porderlos agendar
    
}
/*! \fn: mostrarDatosTurno
 *  \brief: Muestra la informacion del pedido agendado
 *  \author: Ing. Cristian Andr�s Torres
 *  \date: 16/06/2020
 *  \return n/a
 */
function mostrarDatosTurno(info) {
    let observa = "";
    if(info.event._def.extendedProps.cod_novedad != 1){
            observa = `<tr>
                            <th>Novedad:</th>
                            <td>` + info.event._def.extendedProps.nom_novedad + `</td>
                        </tr>
                        <tr>
                            <th>Observacion:</th>
                            <td>` + info.event._def.extendedProps.observacion + `</td>
                        </tr>`;
    }

    Swal.fire({
        type: 'info',
        title: 'Informacion turno' + info.event.id,
        html: `<table class="table table-bordered">
              <tr>
                <th>Codigo turno:</th>
                <td>` + info.event.id + `</td>
              </tr>
              <tr>
                <th>Usuario asignado:</th>
                <td>` + info.event.title + `</td>
              </tr>
              <tr>
                <th>Fecha Hora Inicio:</th>
                <td>` + moment(info.event.start).format("YYYY-MM-DD HH:mm") + `</td>
              </tr>
              <tr>
                <th>Fecha Hora Fin:</th>
                <td>` + moment(info.event.end).format("YYYY-MM-DD HH:mm") + `</td>
              </tr>
              `+observa+`
             <table>`,
    

        showConfirmButton: $("#btnEliminar").val() == 1 ? true: false ,
        showCancelButton: true,
        cancelButtonText: 'Listo',
        cancelButtonColor: info.event.backgroundColor,
        
        confirmButtonText: 'Eliminar',
        confirmButtonColor: '#dc3545',
    }).then((result) => {
        if (result.value) {
            ajaxEliminarTurno(info);
        }
    });;

    $("table th").css({
        "background": info.event.backgroundColor,
        "color": info.event.textColor
    });
}