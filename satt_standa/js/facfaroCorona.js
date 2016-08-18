  /*! \fn: jQuery
  *  \brief: disparador de Onload en document
  *  \author: Ing. Nelson Liberato
  *  \date: 24/06/2015     
  *  \param:  
  * 
  */
   

  $(document).ready(function(event, obj){
 
      // Coloca los estilos para los tab de jquero UI
       $( "#feciniID,#fecfinID" ).datepicker();    
        $( "#tabsID" ).tabs();

  });

  
  /*! \fn: LoadTab
  *  \brief: Ejecuta el ajax segun tab (pestana)
  *  \author: Ing. Nelson Liberato
  *  \date: 01/07/2015     
  *  \param:  cod_tipdes  : tipo despacho
  *  \param:  cod_option  : Funcion a cargar
  * 
  */
  function LoadTab( cod_tipdes, cod_option )
  {
    try
    {
      var Standa = $("#StandaID").val();
      var fec_ini    = $("#feciniID").val() 
      var fec_fin    = $("#fecfinID").val() 
      var transp     = $("#transpID").val() 
      var cod_agenci = $("#cod_agenciID").val() 

      var param =  'fec_ini='+fec_ini+'&fec_fin='+fec_fin+"&transp="+transp;
          param += '&cod_agenci='+cod_agenci+"&cod_tipdes="+cod_tipdes+"&option="+cod_option;
           
      $.ajax({
        type: 'post',
        url: '../'+Standa+'/factur/ajax_factur_corona.php',
        data: param,
        beforeSend: function(){
          $("#general").html( "<center><img src='../"+Standa+"/imagenes/ajax-loader.gif' /></center>" );
        },
        success: function(data) {
          $("#general").html(data);
        },
        complete: function(){
          $("#TabsGrillaID").tabs();
        }
      });


    } 
    catch(e)
    {
      alert("Error en LoadTab: "+e.message+"  \nLine: "+e.lineNumber);
    }
  }

  /*! \fn: SetAgencia
  *  \brief: Ajax para cargar lista de agencias de la transportadora seleccionada
  *  \author: Ing. Nelson Liberato
  *  \date: 24/06/2015     
  *  \param:  cod_empres  : nit transportadora
  *  \param:  cod_agenci  : codigo agencia
  * 
  */
  function SetAgencia( cod_empres, cod_agenci )
  {
    var Standa = $("#StandaID").val();
    $.ajax({
      type: "POST",
      url: "../" + Standa + "/factur/ajax_factur_corona.php",
      data: "standa="+Standa+"&option=SetAgencia&cod_empres="+cod_empres,
      success: 
        function( datos )
        {
          $("#cod_agenciID").html( datos );
        },
        complete: function(){
          document.getElementById("cod_agenciID").value = cod_agenci;
        }
    });
  }
 
  /*! \fn: aceptar_lis
  *  \brief: Valida los filtros del formulario y hace el submit
  *  \author: Ing. Nelson Liberato
  *  \date: 24/06/2015     
  *  \param:  
  * 
  */
  function aceptar_lis()
  {
    try {
    
      // Carga los datos de los filtros
      var fecini = $('#feciniID');
      var fecfin = $('#fecfinID');
      var transp = $('#transpID');
      var nit_transport = $('#nit_transportID');



      
      if(nit_transport.val() == ''){        
        if (transp.val() == '') {
          alert('La Transportadora es requerida');
          return transp.focus();
        }        
      }
      
      if (transp.val() == '') {
        if (nit_transport.val() == '') {
          alert('El Nit de la Transportadora es requerido');
          return nit_transport.focus();
        }
          
      }
      
      
      if(fecini.val()=='' || fecfin.val()=='') {
        return alert('Las Fechas Son Obligatorias');
      }

      var feciniArray = fecini.val().split("-");
      var fecini = new Date(feciniArray[0],Number(feciniArray[1])-1,feciniArray[2]);
      var fecfinArray = fecfin.val().split("-");
      var fecfin = new Date(fecfinArray[0],Number(fecfinArray[1])-1,fecfinArray[2]);
      
    if(fecini>fecfin)
      alert('La Fecha inicial de Salida  NO puede ser Mayor a la Fecha Final de Salida');
    else
      document.formulario.submit();
      
      
    }
    catch(e)
    {
      alert("Error funcion aceptar_lis " + e.message + '\n' + e.stack);
    }
    
  }


function aceptar()
{
  if (confirm('Esta Seguro que Desea Marcar Como Facturadas Los Despachos?')) {
   document.getElementById('opcion').value = 2;
   formulario.submit();
  }  
}


function exportarExcel()
{
  try
  {
    $("#opcion").val("3"); 
    $("#exportExcelID").val('');
    $("#exportExcelID").val( "<table>"+ $("form").find("table").next().next().next().next().next().find(".formulario").first().html()+"</table>" ); 
    /*console.log( "<table>"+ $("form").find("table").next().next().next().next().next().find(".formulario").first().html()+"</table>" ); */
    formulario.submit();

  }
  catch(e)
  {
    console.log( "Error Fuction exportarExcel: "+e.message+"\nLine: "+e.lineNumber );
    return false;
  }
}

/*! \fn: LoadDetail
*  \brief: ejecuta un popup para mostrar los despachos segun el total 
*  \author: Ing. Nelson Liberato
*  \date: 02/07/2015     
*  \param:  tip_despac  : tipo despacho
*  \param:  tip_etapax  : tipo etapa
*  \param:  cod_noveda  : novedad
* 
*/
function LoadDetail(tip_despac, tip_etapax, cod_noveda, type)
{
  try
  {
    var option = type == 'despac' ? 'LoadDespacList' : 'LoadDetail';
     
    // Carga el Popup de Jquery
    LoadPopupJQ("open");

    // Ejecuta ajax para llenar el Popup
    var fec_ini    = $("#feciniID").val() 
    var fec_fin    = $("#fecfinID").val() 
    var transp     = $("#transpID").val() 
    var cod_agenci = $("#cod_agenciID").val() 

    var param =  'fec_ini='+fec_ini+'&fec_fin='+fec_fin+"&transp="+transp;
        param += '&cod_agenci='+cod_agenci+"&cod_tipdes="+tip_despac+"&cod_etapax="+tip_etapax+"&cod_noveda="+cod_noveda+"&option="+option;
       

    var Standa = $("#StandaID").val();
    $.ajax({
      url: '../'+Standa+'/factur/ajax_factur_corona.php',
      type: 'POST',
      data: param,
      beforeSend: function(){
        $("#DialogPoUpID").html( "<center><img src='../"+Standa+"/imagenes/ajax-loader.gif' /></center>" );
      },
      success: function(data){
        $("#DialogPoUpID").html( data );
      },
      complete: function(){
        var Div = $("#DialogPoUpID").find(".StyleDIV")
        var Tbl = Div.find("table");
        // Redimensiona el Div del fondo de la tabla ya que no se ajusta al ancho de la tabla

        Div.css({ "width": Tbl.width()+"px" });
      }

    });



  }
  catch(e)
  {
    console.log( "Error Fuction LoadDetail: "+e.message+"\nLine: "+e.lineNumber );
    return false;
  }
}


/*! \fn: LoadPopupJQ
*  \brief: Abre un popup de Jquery
*  \author: Ing. Nelson Liberato
*  \date: 03/07/2015     
*  \param:  type  : abre o cierra el popup
* 
*/
function LoadPopupJQ(type )
{
  try
  {
      if(type == 'open')
      {
          $('<div id="DialogPoUpID" >Cargando...</div>').dialog({
              modal: true,
              resizable: false,
              draggable: false,
              height: ( $(window).height() - 50 ),
              width: ( $(window).width() - 50 ),
              buttons: {
                Cerrar: function(){ LoadPopupJQ('close') }
              },
              close: function(){
                LoadPopupJQ('close')
              }
          });
      }
      else
      {
          $('#DialogPoUpID').dialog("destroy").remove();
      }
  }
  catch(e)
  {
    console.log( "Error Fuction LoadPopupJQ: "+e.message+"\nLine: "+e.lineNumber );
    return false;
  }
}

/*! \fn: LoadExcel
*  \brief: Abre un popup de Jquery
*  \author: Ing. Nelson Liberato
*  \date: 03/07/2015     
*  \param:  type  : abre o cierra el popup
* 
*/
function LoadExcel( type )
{
  try
  {
      var Standa = $("#StandaID").val();
      location.href = '../'+Standa+'/factur/ajax_factur_corona.php?option=LoadExcel&type='+type;
  }
  catch(e)
  {
    console.log( "Error Fuction LoadPopupJQ: "+e.message+"\nLine: "+e.lineNumber );
    return false;
  }
}
 
 



