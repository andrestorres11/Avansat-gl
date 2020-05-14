/* ! \file: ins_hojvid_ctxxxx
 *  \brief: permite visualizar correctamente las vistas en ins_genera_bancox.php ademas de realizar algunas funciones ajax
 *  \author: Ing. Luis Manrique
 *  \version: 1.0
 *  \date: 27/04/2020
 *  \bug: 
 *  \warning: 
 */

var standa = 'satt_standa';
$(function() {

  cargarCampos(); 

});

 //---------------------------------------------
  /*! \fn: cargarCampos
  *  \brief: Genera popup con el formulario a diligenciar 
  *  \author: Ing. Luis Manrique
  *  \date: 29/04/2020
  *  \date modified: 
  *  \return html
  */

function cargarCampos(){
    try {
        $.ajax({
          url: "../" + standa + "/sertra/ajax_rentab_produc.php",
          type: "post",
          data: ({option: 'crearCampos'}),
          success: function(data) {
              $("#fil_campos").html(data);
              inputDate('dat_filtro');
              inputList('dat_filtro');
          }
      });
    }
    catch(error) {
      console.error(error);
    }
}

 //---------------------------------------------
  /*! \fn: createTable
  *  \brief: Genera popup con el formulario a diligenciar 
  *  \author: Ing. Luis Manrique
  *  \date: 29/04/2020
  *  \date modified: 
  *  \return html
  */

function createTable(objet){
    try {
      if(validateFields()){
        var form = $("#dat_filtro").serialize();
        form = form+'&option=setRegistros&id='+$(objet).attr("id");
        $.ajax({
            url: "../" + standa + "/sertra/ajax_rentab_produc.php",
            type: "post",
            data: (form),
            success: function(data) {
              if (data == '') {
                    Swal.fire({
                      title: 'Oops...',
                      type: 'info',
                      text: decode_utf8('No se encuentra información con la información diligenciada.'),
                      confirmButtonColor: '#336600',
                      confirmButtonText: "Aceptar",
                      allowOutsideClick: false,
                });
              }else{
                //Varibales Necesarias 
                var id = $(objet).attr("id").split("_")[1];

                //Asigna la tabla al tag referente
                $("#div_"+id).html(data);

                //Asigna los campos de buqueda
                $('#contenedor .table_datatables thead tr .buscar').each( function (i) {
                  var title = $(this).text();
                  $(this).html( '<label style="display:none;">'+title+'</label><input type="text" placeholder="Buscar '+title+'" />' );
           
                  $( 'input', this ).on( 'keyup change', function () {
                      var table = $(this).parents("table").DataTable();
                      if ( table.column(i).search() !== this.value ) {
                          table
                              .column(i)
                              .search( this.value )
                              .draw();
                      }
                  });
                }); 


                $("#div_"+id+" .tab_"+id).each(function(){
                  var id = "#"+$(this).attr("id");
                  var columnas = $(id+" tbody tr:nth-child(1) td");
                  for (var i = 2; i < columnas.length; i++) {
                    $(id).find("tfoot").children("tr").append("<th class='tituloFecha'></th>")
                  }
                });

                //Asigna las funciones dataTable
                $("#contenedor .tab_"+id).DataTable({
                  'processing': true,
                  "deferRender": true, 
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
                  "dom": "<'row'<'col-md-4'B><'col-md-4'l><'col-md-4'f>r>t<'row'<'col-md-4'i>><'row'<'#colvis'>p>", 
                  "buttons": [
                        'copyHtml5',
                        {
                          extend: 'excelHtml5',
                          filename: 'Estado del servicio contratado'
                        },
                        {
                          extend: 'csvHtml5',
                          filename: 'Estado del servicio contratado'
                        },
                        {
                          extend: 'pdfHtml5',
                          orientation: 'landscape',
                          pageSize: 'A2',
                          filename: 'Estado del servicio contratado'
                        }
                    ],
                    "footerCallback": function ( row, data, start, end, display ) {
                        var api = this.api(), data;
             
                        // Remove the formatting to get integer data for summation
                        var intVal = function ( i ) {
                            return typeof i === 'string' ?
                                i.replace(/[\$,]/g, '')*1 :
                                typeof i === 'number' ?
                                    i : 0;
                        };

                        for (var i = 2; i < api.columns(':visible').count(); i++) {
                          // Total over all pages
                          total = api
                              .column( i )
                              .data()
                              .reduce( function (a, b) {
                                  return intVal(a) + intVal(b);
                              }, 0 );
               
                          // Total over this page
                          pageTotal = api
                              .column( i, { page: 'current'} )
                              .data()
                              .reduce( function (a, b) {
                                  return intVal(a) + intVal(b);
                              }, 0 );
               
                          // Update footer
                          $( api.column( i ).footer() ).html(
                              +pageTotal +' ( '+ total +' total)'
                          );
                        }
                    }
                });  
              }
            }
        });
      };
    }
    catch(error) {
      console.error(error);
    }
}

 //---------------------------------------------
  /*! \fn: validateFields
  *  \brief: Toma la función validaciones y actualiza la visual y gestión de los datos
  *  \author: Ing. Luis Manrique
  *  \date: 28/04/2020
  *  \date modified: 
  *  \return boolean
  */

function detalle(fecha, hora, usuario, tipo){
  try {
    $.ajax({
        url: "../" + standa + "/sertra/ajax_rentab_produc.php",
        type: "post",
        data: ({option: 'regDetalle', fecha: fecha, hora: hora, usuario: usuario, tipo: tipo}),
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
            Swal.fire({
              html: data,
              width: 1200,
              padding: '0.2em',
              confirmButtonColor: '#336600',
              confirmButtonText: "Cerrar",
              allowOutsideClick: false,
            });

            $('.table_datadetalle thead tr th').each( function (i) {
              var title = $(this).text();
              $(this).html( '<label style="display:none;">'+title+'</label><input type="text" placeholder="Buscar '+title+'" />' );
       
              $( 'input', this ).on( 'keyup change', function () {
                  var table = $(this).parents("table").DataTable();
                  if ( table.column(i).search() !== this.value ) {
                      table
                          .column(i)
                          .search( this.value )
                          .draw();
                  }
              });
            }); 


            var id = "#tab_"+usuario;
            var columnas = $(id+" tbody tr:nth-child(1) td");
            for (var i = 2; i < columnas.length; i++) {
              $(id).find("tfoot").children("tr").append("<th class='tituloFecha'></th>")
            }

            //Asigna las funciones dataTable
            $("#tab_"+usuario).DataTable({
              'processing': true,
              "deferRender": true, 
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
              "dom": "<'row'<'col-md-4'B><'col-md-4'l><'col-md-4'f>r>t<'row'<'col-md-4'i>><'row'<'#colvis'>p>", 
              "buttons": [
                    'copyHtml5',
                    {
                      extend: 'excelHtml5',
                      filename: 'Estado del servicio contratado'
                    },
                    {
                      extend: 'csvHtml5',
                      filename: 'Estado del servicio contratado'
                    },
                    {
                      extend: 'pdfHtml5',
                      filename: 'Estado del servicio contratado'
                    }
                ],
                "footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;
         
                    // Remove the formatting to get integer data for summation
                    var intVal = function ( i ) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '')*1 :
                            typeof i === 'number' ?
                                i : 0;
                    };

                    for (var i = 2; i < api.columns(':visible').count(); i++) {
                      // Total over all pages
                      total = api
                          .column( i )
                          .data()
                          .reduce( function (a, b) {
                              return intVal(a) + intVal(b);
                          }, 0 );
           
                      // Total over this page
                      pageTotal = api
                          .column( i, { page: 'current'} )
                          .data()
                          .reduce( function (a, b) {
                              return intVal(a) + intVal(b);
                          }, 0 );
           
                      // Update footer
                      $( api.column( i ).footer() ).html(
                          +pageTotal +' ( '+ total +' total)'
                      );
                    }
                }
            }); 
        }
    });
  }
  catch(error) {
    console.error(error);
  }
}


 //---------------------------------------------
  /*! \fn: validateFields
  *  \brief: Toma la función validaciones y actualiza la visual y gestión de los datos
  *  \author: Ing. Luis Manrique
  *  \date: 28/04/2020
  *  \date modified: 
  *  \return boolean
  */

function validateFields(field = null)
{   
    var ban = true;

    if(!validaciones()){
      if(field == null){
        spanDanger();
        ban = false;
      }else{
         $(".inc_alert").each(function(){
          if($(this).siblings().attr("id") != $(field).attr("id")){
            $(this).remove();
            $(this).siblings().css({"border-color":"none"});
          }else{
            $(this).addClass("label label-danger");
            $(this).text(decode_utf8($(this).text())).css({
              "left":"50%", 
              "cursor":"pointer", 
              "position":"absolute", 
              "transform":"translate(-50%)", 
              "bottom":"-12px", 
              "z-index":"10"
            });
            $(this).siblings().css({"border-color":"red"});
            $(this).siblings('span').find('.select2-selection').css({"border-color":"red"});
          }
        });

        $(".validate").each(function(){
          if($(this).attr("id") == $(field).attr("id")){
            if($(this).siblings().attr("id") == undefined){
              $(this).css({"border-color":"green"});
            }
          }
        });

        ban = false;
      }
    }else{
      //Capturar valor de las fechas y horas para realizar validaciones
      var fec_inicia = moment($("#fec_inicia").val()+" "+$("#hor_inicia").val());
      var fec_finalx = moment($("#fec_finalx").val()+" "+$("#hor_finalx").val());

      if(fec_inicia > fec_finalx){
        setTimeout(function() {
          inc_alerta("fec_inicia", "La fecha inicial no puede ser mayor a la final.");
          inc_alerta("hor_inicia", "La hora inicial no puede ser mayor a la final.");
          spanDanger();
        }, 530);
        ban = false;
      }else if(fec_finalx.diff(fec_inicia, 'days') > 30){
        setTimeout(function() {
          inc_alerta("fec_finalx", "El rango de fechas no puede ser mayor a 30 dias.");
          spanDanger();
        }, 530);
        ban = false;
      }else{
        $(".validate").each(function(){
          $(this).css({"border-color":"green"});
        });
      }
    }
          
    return ban;
};

  //---------------------------------------------
  /*! \fn: decode_utf8
  *  \brief: Decodifica textos
  *  \author: Ing. Luis Manrique
  *  \date: 28/04/2020
  *  \date modified: 
  *  \return string
  */

function decode_utf8(word) {
  return decodeURIComponent(escape(word));
}


 //---------------------------------------------
  /*! \fn: inputDate
  *  \brief: Asigna datepicker a los camopos con clase date
  *  \author: Ing. Luis Manrique
  *  \date: 28/04/2020
  *  \date modified: 
  *  \return 
  */
function inputDate(form){
  $("#"+form+" .date").each(function(){
    // Rango de fechas
    $('#'+$(this).attr("id")).datetimepicker({
      format: 'YYYY-MM-DD',
      locale: 'ES'
    });
  });

  $("#"+form+" .hora").each(function(){
    // Rango de fechas
    $('#'+$(this).attr("id")).datetimepicker({
      format: 'HH:00',
      locale: 'ES'
    });
  }); 
}


//---------------------------------------------
  /*! \fn: inputList
  *  \brief: Asigna listas a los campos select
  *  \author: Ing. Luis Manrique
  *  \date: 28/04/2020
  *  \date modified: 
  *  \return string
  */
function inputList(form){

  $("#"+form+" .list").each(function(){
    dependencia("#nom_perfil", "#cod_usuari");
     $('#'+$(this).attr("id")).select2({
        ajax: {
          url: "../" + standa + "/sertra/ajax_rentab_produc.php?option=dataList&file="+$(this).attr("id"),
          dataType: 'json',
          type: "POST",
          processResults: function (data) {
              return {
                  results: $.map(data, function (item) {
                      return {
                          text: item.text,
                          id: item.id
                      }
                  })
              };
          }
        }       
    });
  });

    

  $(".select2.select2-container").attr('style', 'max-width: 100%; width: 100% !important; min-height: 34px;');

  $(".select2-selection").css({
    "max-width": "100%",
    "min-height": "34px"
  });
}

//---------------------------------------------
  /*! \fn: dependencia
  *  \brief: Crea un select2 basado en la información de un canmpo
  *  \author: Ing. Luis Manrique
  *  \date: 28/04/2020
  *  \date modified: 
  *  \return string
  */

function dependencia(campoDependencia, campoDependiente){
  $(campoDependencia).on("change", function (e) { 
     var value = $(e.currentTarget).val();
      $(campoDependiente).select2("destroy");
      $(campoDependiente).select2({
      ajax: {
        url: "../" + standa + "/sertra/ajax_rentab_produc.php?option=dataList&file="+campoDependiente.split("#")[1]+"&dependencia="+value,
        dataType: 'json',
        type: "POST",
        processResults: function (data) {
            return {
                results: $.map(data, function (item) {
                    return {
                        text: item.text,
                        id: item.id
                    }
                })
            };
        }
      }       
    });

    $(".select2.select2-container").attr('style', 'max-width: 100%; width: 100% !important; min-height: 34px;');

    $(".select2-selection").css({
      "max-width": "100%",
      "min-height": "34px"
    });
  });
}

//---------------------------------------------
  /*! \fn: spanDanger
  *  \brief: Asigna los estilos de los span de alerta con boostrap
  *  \author: Ing. Luis Manrique
  *  \date: 28/04/2020
  *  \date modified: 
  *  \return string
  */

function spanDanger(){
  $(".inc_alert").each(function(){
    $(this).addClass("label label-danger");
    $(this).text(decode_utf8($(this).text())).css({
        "left":"50%", 
        "cursor":"pointer", 
        "position":"absolute", 
        "transform":"translate(-50%)", 
        "bottom":"-12px", 
        "z-index":"10"
      });
    $(this).siblings().css({"border-color":"red"});
    $(this).siblings('span').find('.select2-selection').css({"border-color":"red"});
  });
}