/* ! \file: suspension
 *  \brief: permite visualizar correctamente las vistas en ins_servic_suspen.php ademas de realizar algunas funciones ajax
 *  \author: Ing. Alexander Correa
 *  \author: alexander.correa@intrared.net
 *  \version: 1.0
 *  \date: 08/04/2016
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */

var standa;
$(function() {

    $('#tablaSuspend thead tr th').each( function (i) {
        var title = $(this).text();
        $(this).html( '<label style="display:none;">'+title+'</label><input type="text" placeholder="Buscar '+title+'" />' );
 
        $( 'input', this ).on( 'keyup change', function () {
            if ( table.column(i).search() !== this.value ) {
                table
                    .column(i)
                    .search( this.value )
                    .draw();
            }
        } );
    } );

    minDateFilter = "";
    maxDateFilter = "";
    $.fn.dataTableExt.afnFiltering.push(
      function(oSettings, aData, iDataIndex) {

        if (typeof aData._date == 'undefined') {
          aData._date = new Date(aData[2]).getTime();
        }
        var date = new Date(aData._date);
        date.setDate(date.getDate() + 1);
        if (minDateFilter && !isNaN(minDateFilter)) {
          if (date < minDateFilter) {
            return false;
          }
        }

        if (maxDateFilter && !isNaN(maxDateFilter)) {
          if (date > maxDateFilter) {
            return false;
          }
        }

        return true;
      }
    );
    

    var table = $("#tablaSuspend").DataTable({
        destroy: true,
        deferRender:    true, 
        "autoWidth": false,     
        "search": {
            "regex": true,
            "caseInsensitive": false,
        },
        'paging': true,
        'info': true,
        'filter': true,
        'orderCellsTop': true,
        'fixedHeader': true,
        'language' : {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        dom: "<'row'<'col-md-4'B><'col-md-4'l><'col-md-4'f>r>t<'row'<'col-md-4'i>><'row'<'#colvis'>p>",
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ],

    });


    $("#fec_rangox").daterangepicker({
          "locale": {
            "format": 'YYYY-MM-DD',
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
            ]
          },
          "opens": "center",
        }, function(start, end, label) {
          maxDateFilter = end;
          minDateFilter = start;
          table.draw();  
        });

    $("#fec_rangox").val("");


    setTimeout(function() {
        $("div").css({
            height: 'auto'
        });

        $("#form3").removeAttr('style');
        $("#form3").css({
            height: ( $(window).height() - 120 ),
            overflow: 'scroll'
        });

        //$("#ui-timepicker-div-hor_inicia0").removeAttr('style');

        standa = $("#standa").val();

        /*$(".date").datepicker({
            dateFormat: 'yy-mm-dd'
        });

        $(".time").timepicker({
            timeFormat: "hh:mm",
            showSecond: false
        });*/
    }, 530);
    standa = $("#standa").val();

});


/* ! \fn: editarEmpresa
 *  \brief: permite cargar el formulario de edicion de la configuracion de suspencion de servicio de una empresa
 *  \author: Ing. Alexander Correa
 *  \date: 18/04/2016
 *  \date modified: dia/mes/año
 *  \param: $row  => object => datos de la empresa a editar    
 *  \return 
 */

function editarEmpresa(row) {
    var objeto = $(row).parent().parent();
    var cod_tercer = objeto.find("input[id^=cod_tercer]").val();
    var nom_tercer = objeto.find("input[id^=nom_tercer]").val();
    var dir_emailx = objeto.find("input[id^=dir_emailx]").val();
    swal({
        title: "Parametrizar Suspenci\u00F3n",
        text: "\u00bfRealmente Desea Parametrizar la Suspenci\u00F3n de la Empresa " + nom_tercer + "?",
        type: "warning",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
    }, function() {
        $("#cod_tercer").val(cod_tercer);
        $("#nom_tercer").val(nom_tercer);
        $("#dir_emailx").val(dir_emailx);
        $("#form_searchID").submit();
    });
}

/* ! \fn: addMail
 *  \brief: adiciona un nuevo campo para registrar un email para las notificaciones
 *  \author: Ing. Alexander Correa
 *  \date: 19/02/2016
 *  \date modified: dia/mes/año
 *  \param: 
 *  \return pinta los campos necesarios
 */

function addMail() {
    var total = parseInt($("#tot_emailxx").val());
    total++;
    var div = "";
    div += "<div id='email" + total + "' class='col-md-12'>";
    div += "<div class='col-md-3'>";
    div += "<input class='ancho text-center' type='text' name='dir_emailx[]' minlength='8' maxlength='50' validate='email' id='dir_emailx" + total + "' placeholder='Nuevo Email'></input>";
    div += "</div>";
    div += "<div class='col-md-8'>";
    div += "<div class='col-md-3'>";
    div += "<input type='text' class='date text-center' obl='1' maxlength='10' minlength='10' validate='date' id='fec_inicia" + total + "' readonly name='fec_inicia[]'></input>";
    div += "</div>";
    div += "<div class='col-md-3'>";
    div += "<input type='text' class='time text-center' obl='1' maxlength='5' minlength='5' validate='dir' onclick='removeStyle(\"hor_inicia" + total + "\")' id='hor_inicia" + total + "' readonly name='hor_inicia[]'></input>";
    div += "</div>";
    div += "<div class='col-md-3'>";
    div += "<input type='text' class='date text-center' obl='1' maxlength='10' minlength='10' validate='date' id='fec_finali" + total + "' readonly name='fec_finali[]'></input>";
    div += "</div>";
    div += "<div class='col-md-3'>";
    div += "<input type='text' class='time text-center' obl='1' maxlength='5' minlength='5' validate='dir' onclick='removeStyle(\"hor_finali" + total + "\")' id='hor_finali" + total + "' readonly name='hor_finali[]'></input>";
    div += "</div>";
    div += "</div>";
    div += "<div class='col-md-i'>";
    div += "<img class='pointer' src='../" + standa + "/images/delete.png' width='14px' height='14px' onclick='removeEmail(" + total + ")'>";
    div += "</div>";
    div += "</div>";
    $("#tot_emailxx").val(total);
    total--;
    $("#emails").append(div);


    $(".date").datepicker({
        dateFormat: 'yy-mm-dd'
    });

    $(".time").timepicker({
        timeFormat: "hh:mm",
        showSecond: false
    });
}

/* ! \fn: removeEmail
 *  \brief: elimina la fila completa de un email del formulario
 *  \author: Ing. Alexander Correa
 *  \date: 19/04/2016
 *  \date modified: dia/mes/año
 *  \param: ind     => int => indicador del div a eliminar    
 *  \return 
 */
function removeEmail(ind) {
    $("#email" + ind).remove();
}

/* ! \fn: registrarSuspencion
 *  \brief: valida el formulario para su registro
 *  \author: Ing. Alexander Correa
 *  \date: 20/04/2016
 *  \date modified: dia/mes/año
 *  \param:     
 *  \return 
 */

function registrarSuspencion() {
    var conn = checkConnection();
    if (conn) {
        var val = validaciones();

        if (!$("#sus_ealxxx").is(':checked') && !$('#sus_monati').is('checked')) {
            setTimeout(function() {
                inc_alerta("sus_ealxxx", "Debes seleccionar por lo menos un servicio a suspender");
                inc_alerta("sus_monati", "Debes seleccionar por lo menos un servicio a suspender");
            }, 530);
        }

        if (val) {
            swal({
                title: "Aviso de Suspenci\u00F3n",
                text: "\u00bfRealmente Desea registrar los datos ingresados?",
                type: "warning",
                showCancelButton: true,
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            }, function() {
                var parametros = getDataForm();
                $.ajax({
                    type: "POST",
                    url: "../" + standa + "/seguridad/ajax_perfil_perfil.php",
                    data: "&Ajax=on&Option=registrarSuspencion&standa=" + standa + "&" + parametros,
                    async: true,
                    success: function(data) {
                        console.log(data);
                        if (data == 1) {
                            swal({
                                title: "Aviso de Suspenci\u00F3n",
                                text: "Datos Registrados Con Exito.",
                                type: "success"
                            }, function() {
                                $("#form_searchID").submit();
                            });
                        } else {
                            swal({
                                title: "Aviso de Suspenci\u00F3n",
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
            title: "Aviso de Suspención",
            text: "Por favor verifica tu conexi\u00F3n a internet.",
            type: "error"
        });
    }
}
