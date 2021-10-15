$(function () {

    /* Inicializar los eventos externos
     -----------------------------------------------------------------*/
    function ini_events(ele) {
      ele.each(function () {

        // Crea un eneto como obejto 
        // No necesita tener un comienzo o un final
        var eventObject = {
          title: $.trim($(this).text()) // Usa el texto del elemento como t�tulo del evento
        }

        // Almacenar el objeto de evento en el elemento DOM para que podamos acceder a �l m�s tarde
        $(this).data('eventObject', eventObject)

        // Hacer que el evento sea arrastrable usando jQuery UI
        $(this).draggable({
          zIndex        : 1070,
          revert        : true, // Har� que el evento regrese a su contenedor
          revertDuration: 0  // Posici�n original despu�s del arrastre
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
    var calendarEl = document.getElementById('calendar');

    //Parametros necesarios
    var standa = "satt_standa";
    var parametros = "Option=viewAgenda";

    var calendar = new Calendar(calendarEl, {
      plugins: [ 'bootstrap', 'interaction', 'dayGrid', 'timeGrid' ],
      header    : {
        left  : 'prev,next today',
        center: 'title',
        right : 'dayGridMonth,timeGridWeek,timeGridDay',
      },
      events : "../" + standa + "/config/ajax_agenda_calend.php?"+parametros,
      //droppable : true, // this allows things to be dropped onto the calendar !!!
      //editable  : true,
      eventLimit: true, // when too many events in a day, show the popover
      locale: 'es',
      eventResize: function(obj) {  // Cambiar el tama�o para aumentar o disminuir el tiempo del agendamiento
        updateAgendaDet(obj, standa);
      },
      eventDrop: function(obj) { // Evento para soltar y cambiar las fechas del agendamiento 
        updateAgendaDet(obj, standa);
      },
      eventClick: function(info) { // Evento para ver la informaci�n al detalle del agendamiento
        if(info.event.id=="Cliente Retira"){
          mostrarInformacionFranjaRetira(info);
        }else{
          mostrarPedidosAgendados(info);
        }
      },
      eventRender: function (info) {
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
    $("#add-new-event").on("click", function(){
      addAgendPed(calendar);
    });

    //Evento para crear o modificar agendamientos Cliente retira
    $("#add-new-event-cl").on("click", function(){  
      addAgendPedCl(calendar);

    });
        
    /* Eventos adicionales */
    var colorChooser = $('#color-chooser-btn');
    $('#color-chooser > li > a').click(function (e) {
      e.preventDefault();
      //Save color
      currColor = $(this).css('color')
      //Add color effect to button
      $('#add-new-event').css({
        'background-color': currColor,
        'border-color'    : currColor
      });
    })

    // Rango de fechas
    $('#reservation').daterangepicker()
    var date = new Date();
    // Selector de rango de fechas con selector de tiempo
    $('.rango').daterangepicker({
      minDate: new Date(date.getFullYear(), date.getMonth(), date.getDate()),
      timePicker: false,
      language: 'es',
      timePicker24Hour: false,
      use24hours: false,
      //timePickerIncrement: 30,
      drops: "up",
      locale: {
        format: 'YYYY-MM-DD',
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
    $("#cod_pedido, #nom_pedido").autocomplete({
      source: "../" + standa + "/config/ajax_agenda_calend.php?Option=listCodAgenda",
      minLength: 3,
      select: function(event, ui) {
        $("#cod_pedido").val(ui.item.id);
        $("#nom_pedido").val(ui.item.nom);
        $("#hor_apert_hor_cierre").val(ui.item.fec);
        $("#num_lineax").val(ui.item.lin);
        //Identifica si esta agendado o no el pedido para modificar el formulario y ciertos caracteres.
        /*if(ui.item.fec == ""){
          $("#titulo").text("Crear Pedido");  
          $("#add-new-event").css({"background":"rgb(98, 106, 113)","border-color":"rgb(98, 106, 113)"});
        }else{
          $("#titulo").text("Modificar Pedido");
          $("#add-new-event").css({"background":ui.item.col,"border-color":ui.item.col});
        }*/  
      }
    });
  });

  /*! \fn: mostrarPedidosAgendados
  *  \brief: Muestra la informacion del pedido agendado
  *  \author: Ing. Cristian Andr�s Torres
  *  \date: 16/06/2020
  *  \return n/a
  */ 
  function mostrarPedidosAgendados(info){
    Swal.fire({
      type: 'info',
      title: 'Informaci&oacute;n del Pedido #'+info.event.id,
      html: `<table class="table table-bordered">
              <tr>
                <th>Codigo Pedido:</th>
                <td>`+info.event.id+`</td>
              </tr>
              <tr>
                <th>Titulo Pedido:</th>
                <td>`+info.event.title+`</td>
              </tr>
              <tr>
                <th>Fecha Hora Inicio:</th>
                <td>`+moment(info.event.start).format("YYYY-MM-DD HH:mm")+`</td>
              </tr>
              <tr>
                <th>Fecha Hora Fin:</th>
                <td>`+moment(info.event.end).format("YYYY-MM-DD HH:mm")+`</td>
              </tr>
              <tr>
                <th>Tiempo de Carpado:</th>
                <td>15 Minutos</td>
              </tr>
             <table>`,
      showCloseButton: true,
      confirmButtonText: 'Listo',
      confirmButtonColor: info.event.backgroundColor
    });

    $("table th").css({
        "background": info.event.backgroundColor, 
        "color": info.event.textColor
    });
  }



/*! \fn: addAgendPed
  *  \brief: Funcion para crear o modificar Agendamientos
  *  \author: Ing. Luis Manrique
  *  \date: 20/12/2019
  *  \return n/a
  */ 

function addAgendPed(objet){
  consultaHoraDisponible();
}

/*! \fn: addAgendPed
  *  \brief: Funcion para crear o modificar Agendamientos
  *  \author: Ing. Luis Manrique
  *  \date: 20/12/2019
  *  \return n/a
  */ 

function addAgendPedCl(objet){
    try 
    {
      if(validateFields('form_addAgenPedCl')){
        var form = new FormData(document.getElementById('form_addAgenPedCl'));
        var parametros = "Option=creEditAgendPediCl";
        var standa = "satt_standa";

        Swal.fire({
          title: 'Estas seguro?',
          text: "Estas seguro que desea enviar esta solicitud?",
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#454545',
          cancelButtonColor: '#aaa',
          confirmButtonText: 'Si, confirmar'
        }).then((result) => {
          if (result.value) {
            $.ajax({
              url: "../" + standa + "/config/ajax_agenda_calend.php?"+parametros,
              data: form,
              type: "POST",
              dataType: "json",
              processData: false,  // tell jQuery not to process the data
              contentType: false,   // tell jQuery not to set contentType
              beforeSend: function()
              {
                Swal.fire({
                  title:'Cargando',
                    text: 'Por favor espere...',
                    imageUrl: '../' + standa + '/imagenes/ajax-loader.gif',
                    imageAlt: 'Custom image',
                    showConfirmButton: false,
                })
              },
              success: function(data) {
                //console.log(data);
                if(data['status'] == 1 || data['status'] == 2 || data['status'] == 3){
                  Swal.fire({
                      title: data['title'],
                      text: data['text'],
                      type: data['type'],
                      confirmButtonColor: '#454545'
                    }).then((result) => {
                      if (result.value) {
                        location.reload();
                         /*calendar.addEvent('renderEvent',
                         {
                             id: data['data']['id'],
                             title: data['data']['title'],
                             start: data['data']['start'],
                             end: data['data']['end'],
                             backgroundColor: data['data']['backgroundColor'],
                             borderColor: data['data']['borderColor'],
                             textColor: data['data']['textColor'],
                         });*/
                      }
                  })
                }else{
                  Swal.fire({
                    title:'Error!',
                      text: 'Se a presentado un error en la solicitud, contacte a su administrador del servicio',
                      type: 'error',
                      confirmButtonColor: '#454545'
                  })
                }
              }
            });
          }else{
            location.reload();
          }
        })
      }
    } 
    catch (e) 
    {
      console.error("Error addAgendPed " + e.message);
    }
}


/*! \fn: validateFields
  *  \brief: Funcion valida campos requeridos
  *  \author: Ing. Luis Manrique
  *  \date: 20/12/2019
  *  \return n/a
  */ 

function validateFields(form){
  try 
  {
    var ban = true;

    $("#"+form+" .field").each(function(){
      if($(this).val() == ""){
        Swal.fire({
          type: 'info',
          title: 'Campo Faltante!',
          html: "El campo <b>" + $(this).attr("placeholder") + "</b> esta vacio.",
            confirmButtonColor: '#454545'
        })
        ban = false;
        $(this).removeClass("is-valid");
        $(this).addClass("is-invalid");
        return false;
      }else{
        $(this).removeClass("is-invalid");
        $(this).addClass("is-valid");
      }
    }); 
    return ban;
  } 
  catch (e) 
  {
    console.error("Error addAgendPed " + e.message);
  }
}


/*! \fn: updateAgendaDet
  *  \brief: Funcion que actualiza al momento de mover o soltar agendamientos
  *  \author: Ing. Luis Manrique
  *  \date: 20/12/2019
  *  \return: alert
  */ 

function updateAgendaDet(obj, standa){
  try 
  {
    var values = {
                    Option : "creEditAgendPedi",
                    id : obj.event.id,       
                    start : moment(obj.event.start).format("YYYY-MM-DD HH:mm"),
                    end : moment(obj.event.end).format("YYYY-MM-DD HH:mm"),
                    cod_colorx : obj.event.backgroundColor, 
                  };
    Swal.fire({
      title: 'Estas seguro?',
      text: "Estas seguro que desea enviar esta solicitud?",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#454545',
      cancelButtonColor: '#aaa',
      confirmButtonText: 'Si, confirmar'
    }).then((result) => {
      if (result.value) {
        $.ajax({
          url: "../" + standa + "/config/ajax_agenda_calend.php",
          data: values,
          type: "POST",
          dataType: "json",
          beforeSend: function()
          {
            Swal.fire({
              title:'Cargando',
                text: 'Por favor espere...',
                imageUrl: '../' + standa + '/imagenes/ajax-loader.gif',
                imageAlt: 'Custom image',
                showConfirmButton: false,
            })
          },
          success: function(data) {
            //console.log(data);
            if(data['status'] == 1 || data['status'] == 2 || data['status'] == 3){
              Swal.fire({
                  title: data['title'],
                  text: data['text'],
                  type: data['type'],
                  confirmButtonColor: '#454545'
                })/*.then((result) => {
                  if (result.value) {
                    //location.reload();
                     calendar.addEvent('renderEvent',
                     {
                         id: data['data']['id'],
                         title: data['data']['title'],
                         start: data['data']['start'],
                         end: data['data']['end'],
                         backgroundColor: data['data']['backgroundColor'],
                         borderColor: data['data']['borderColor'],
                         textColor: data['data']['textColor'],
                     });
                  }
              })*/
            }else{
              Swal.fire({
                title:'Error!',
                  text: 'Se a presentado un error en la solicitud, contacte a su administrador del servicio',
                  type: 'error',
                  confirmButtonColor: '#454545'
              })
            }
          }
        });
      }else{
            location.reload();
      }
    })
  } 
  catch (e) 
  {
    console.error("Error updateAgendaDet " + e.message);
  }
}