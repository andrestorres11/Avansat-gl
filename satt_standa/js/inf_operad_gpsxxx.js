/* ! \file: ins_genera_bancox
 *  \brief: permite visualizar correctamente las vistas en inf_operad_gpsxxx.php ademas de realizar algunas funciones ajax
 *  \author: Ing. Cristian Andrés Torres
 *  \version: 1.0
 *  \date: 02/06/2020
 *  \bug: 
 *  \warning: 
 */

var standa = 'satt_standa';
$(function() {

    $('#contenedor #tablaRegistros thead tr th').each( function (i) {
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

    var table = $("#contenedor #tablaRegistros").DataTable({
        "ajax": {
            "url": "../" + standa + "/opegps/ajax_infope_gpsxxx.php",
            "data": ({option:'setRegistros'}),
            "type": 'POST'
        },
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
        'orderCellsTop': false,
        'fixedHeader': true,
        'language' : {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "dom": "<'row'<'col-md-4'<'#crear'>>><'row'<'col-md-4'B><'col-md-4'l><'col-md-4'f>r>t<'row'<'col-md-4'i>><'row'<'#colvis'>p>", 
        "buttons": [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5',
        ],
        "order": [[ 0, "asc" ]],
        fnInitComplete: function(){
           $("#crear").html('<div><a tabindex="0" onclick="formRegistro(\'form\', \'xl\')" class="small-box-footer btn btn-success btn-sm" data-toggle="modal" data-target="#modal-xl" aria-controls="tablaRegistros"><span>Crear registro</span></a></div><br>');
        }

    });
}); 

//---------------------------------------------
  /*! \fn: formRegistro
  *  \brief: Genera popup con el formulario a diligenciar 
  *  \author: Ing. Andres Martinez
  *  \date: 29/04/2020
  *  \date modified: 
  *  \return html
  */

  function formRegistro(modulo, tam, cod_operad = null){
      console.log(cod_operad);
    try {
        var boton = cod_operad == null ? 'Crear' : 'Actualizar';
        Swal.fire({
          title: '¿Esta seguro?',
          text: '¿Esta seguro que desea '+boton+' este registro?',
          type: 'question',
          showCancelButton: true,
          confirmButtonColor: '#336600',
          cancelButtonColor: '#aaa',
          confirmButtonText: 'Si, confirmar'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: "../" + standa + "/opegps/ajax_infope_gpsxxx.php",
                    type: "post",
                    data: ({option: modulo, tam: tam, cod_operad:cod_operad}),
                    success: function(data) {
                        Swal.fire({
                          html: data,
                          width: 500,
                          padding: '0.2em',
                          showCancelButton: true,
                          confirmButtonColor: '#336600',
                          cancelButtonColor: '#aaa',
                          confirmButtonText: boton,
                          allowOutsideClick: false,
                          preConfirm: () => {    
                            if(!validateFields()){
                              return false;
                            }  
                          }
                        }).then((result) => {
                          if (result.value) {
                            var form = $("#dat_basico").serialize();
                            form = form+"&option=regForm"
                            $.ajax({
                                url: "../" + standa + "/opegps/ajax_infope_gpsxxx.php",
                                type: "post",
                                data: form,
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
                                    if(data['status'] == 200){
                                        Swal.fire({
                                          title:boton,
                                          text:  data['response'],
                                          type: 'success',
                                          confirmButtonColor: '#336600'
                                        }).then((result) => {
                                            if (result.value) {
                                                var table = $('#contenedor #tablaRegistros').DataTable();
                                                table.ajax.reload();
                                            }
                                        })
                                    }else{
                                        Swal.fire({
                                          title:'Error!',
                                          text:  data['response'],
                                          type: 'error',
                                          confirmButtonColor: '#336600'
                                        })
                                    }
                                }
                            });
                            
                          }
                        })

                        $(".cerrar").on("click", function(){
                            var div = $(this).parents('.fade');
                            setTimeout(function() {
                                $(div).remove();
                            }, 500);
                        });
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
  /*! \fn: updEst
  *  \brief: Genera popup con la confirmaci�n para actualziar estado
  *  \author: Ing. Luis Manrique
  *  \date: 28/04/2020
  *  \date modified: 
  *  \return html
  */

  function updEst(objet){
    var estText = $(objet).attr('data-estado') == 1 ? 'desactivar':'activar';
    try {
        Swal.fire({
          title: '¿Esta seguro?',
          text: "¿Esta seguro que desea "+estText+" este registro?",
          type: 'question',
          showCancelButton: true,
          confirmButtonColor: '#336600',
          cancelButtonColor: '#aaa',
          confirmButtonText: 'Si, confirmar'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: "../" + standa + "/opegps/ajax_infope_gpsxxx.php",
                    type: "post",
                    data: ({option: 'updEst', estado: $(objet).attr('data-estado'), cod_operad:$(objet).val()}),
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
                        if(data['status'] == 200){
                            Swal.fire({
                              title:'Registrado!',
                              text:  data['response'],
                              type: 'success',
                              confirmButtonColor: '#336600'
                            }).then((result) => {
                                if (result.value) {
                                    var table = $('#contenedor #tablaRegistros').DataTable();
                                    table.ajax.reload();
                                }
                            })
                        }else{
                            Swal.fire({
                              title:'Error!',
                              text:  data['response'],
                              type: 'error',
                              confirmButtonColor: '#336600'
                            })
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
        $(".inc_alert").each(function(){
          $(this).addClass("label label-danger");
          $(this).text(decode_utf8($(this).text())).css({"cursor":"pointer", "position":"absolute", "transform":"translate(-70px, 0px)"});
          $(this).siblings().css({"border-color":"red"});
        });
        ban = false;
      }else{
         $(".inc_alert").each(function(){
          if($(this).siblings().attr("id") != $(field).attr("id")){
            $(this).remove();
            $(this).siblings().css({"border-color":"none"});
          }else{
            $(this).addClass("label label-danger");
            $(this).text(decode_utf8($(this).text())).css({"cursor":"pointer", "position":"absolute", "transform":"translate(-70px, 0px)"});
            $(this).siblings().css({"border-color":"red"});
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
      $(".validate").each(function(){
        $(this).css({"border-color":"green"});
      });
    }
          
    return ban;
};



function decode_utf8(word) {
  return decodeURIComponent(escape(word));
}