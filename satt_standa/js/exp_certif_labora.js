$(function(){
  
  $(document).ready(function(){
    var base = $("#baseID").val();
    $("#cod_tercerID").autocomplete({
       source: '../'+base+'/inform/exp_certif_labora.php?Ajax=on&opcion=searchTercer',
                minLength: 3,
                select: function( event, ui ) {
                var result = ui.item.value.split('-');
                var cod_tercer = result[0].replace(/^\s+/g,'').replace(/\s+$/g,'');
                var nom_tercer = result[1].replace(/^\s+/g,'').replace(/\s+$/g,'');                
                if( cod_tercer == '00000000')
                {
                  alert("El usuario no tiene número de cédula, Por favor actualize los datos del usuario!");
                  $('#cod_tercerID').val(  "" );
                  return false;
                }
                setTimeout(function(){
                  $('#cod_tercerID').val(  cod_tercer );
                  $('#nom_tercerID').val(  nom_tercer );
                }, 200);
              } 
    });
    
  });
  
  ValidaCedula();
 
});

function ValidaCedula ()
{
  try
  {
    var base = $("#baseID").val();
    $.ajax({
      type   : 'POST',
      url    : '../'+base+'/inform/exp_certif_labora.php?Ajax=on&opcion=ValidaCedula',
      success: function( data )
      {
        if(data == 'no')
        {          
          var html  = '<table align="center" width="40%" align="center" style="border:2px solid #FF0400; border-radius: 15px;" cellspacing="0" >';
                html += '<tr>';
                  html += '<th class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" ><span style="color: #ffffff">VALIDACIÓN CÉDULA</span></th>';
                html += '</tr>'; 
                html += '<tr>';
                  html += '<td>';
                    html += '<span style="font-size:16px"><b>USTED NO TIENE GUARDADO SU NÚMERO DE CÉDULA<br>POR FAVOR ACTUALIZE SUS DATOS EN LA OPCIÓN:<br>&nbsp;&nbsp;&nbsp;seguridad -> Cambio de Clave</b></span>';
                    html += '</span>';
                  html += '</td>';
                html += '</tr>';
              html += '<table>';
         $(document).find("form[name='form_insert']").html(  $("#resultado").html( html.replace("no", "si") ) );
        }
      }
    });
  }
  catch(e)
  {
    alert("Error en ValidaCedula: "+e.message+"\nLine: "+e.lineNumber);
  }
}

function Numeric( fEvent )     
{        
   fEvent = (fEvent) ? fEvent : window.event 
   var charCode = (fEvent.which) ? fEvent.which : fEvent.keyCode 
   if (charCode > 31 && (charCode < 48 || charCode > 57)) 
     return false 
     
}

function ClearNombre(fEvent)
{
  try
  {  
     fEvent = (fEvent) ? fEvent : window.event 
     var charCode = (fEvent.which) ? fEvent.which : fEvent.keyCode;
     if (charCode == 8 && $("#cod_tercerID").attr("readonly") == false) 
      $("#nom_tercerID").val("");
  }
  catch(e)
  {
    alert("Error en ClearNombre: "+e.message+"\nLine: "+e.lineNumber);
  }
}

function Validar()
{
  try
  {
    $("#resultado").html("");
    var cod_tercer = $("#cod_tercerID");
    var nom_tercer = $("#nom_tercerID");
    var nom_dirigi = $("#nom_dirigiID");
    var user_id = $("#user_idID");
    var base = $("#baseID").val();
    
    if( cod_tercer.val() == '')
    {
      alert("La identificación del usuario es requerida!");
      cod_tercer.focus();
      return false;
    }
    if( nom_dirigi.val() == '')
    {
      alert("El campo Dirigido A es obligatorio!");
      nom_dirigi.focus();
      return false;
    }
  
    var Opcion = 'Ajax=on&opcion=getCertif';
    Opcion +='';
    $(":checkbox").each(function(o,i){
      if($(this).is(":checked"))
        Opcion +="&opcion"+o+"="+$(this).val();
    }); 
    
    Opcion += "&diridi="+nom_dirigi.val()+"&tercero="+cod_tercer.val()+"&user_id="+user_id.val();
    $.ajax({
      type  : 'post',
      url   : '../'+base+'/inform/exp_certif_labora.php',
      data  : Opcion,
      async : false,
      beforeSend: function()
      {
        LockJquery('Lock');
      },
      success: function( data )
      {
        LockJquery('Unlock');
        if(data == '1000')
        {
           var html  = '<table align="center" width="40%" align="center" style="border:2px solid #3A8104; border-radius: 15px;" cellspacing="0" >';
                html += '<tr>';
                  html += '<th class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" ><span style="color: #ffffff">ARCHIVO PDF GENERADO</span></th>';
                html += '</tr>'; 
                html += '<tr>';
                  html += '<td>';
                    html += '<span style="font-size:16px"><b>';
                      html += 'SE GENERÓ DE MANERA EXITOSA EL CERTIFICADO PARA:<br><br>';
                      html += '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color: green;">'+nom_tercer.val()+"</span><br><br>";
                      html += 'Para ver las descargas presione ( ctrl + j )</b>';
                    html += '</span>';
                  html += '</td>';
                html += '</tr>';
              html += '<table>';
              
          $("#resultado").html(html).effect( 'slide' );
          location.href = '../'+base+'/inform/exp_certif_labora.php?Ajax=on&opcion=ShowPdf&tercero='+cod_tercer.val();
        }
        else
        {
         var html  = '<table align="center" width="40%" align="center" style="border:2px solid #FF0400; border-radius: 15px;" cellspacing="0" >';
                html += '<tr>';
                  html += '<th class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" ><span style="color: #ffffff">ERROR SOLICITUD</span></th>';
                html += '</tr>'; 
                html += '<tr>';
                  html += '<td>';
                    html += '<span style="font-size:16px"><b>';
                      html += 'LA C&Eacute;DULA INGRESADA NO EXISTE :<br><br>';
                      html += "SI EL PROBLEMA PERSISTE POR FAVOR COMUNIQUESE CON UN ADMINISTRADOR<br><br>";
                    html += '</span>';
                  html += '</td>';
                html += '</tr>';
              html += '<table>';
          $("#resultado").html( html ).effect( 'slide' );
        } 
      }
    });
    
    cod_tercer.val("");
    nom_tercer.val("");
    nom_dirigi.val("");

  }
  catch(e)
  {
    alert("Error en Validar: "+e.message+"\nLine: "+e.lineNumber);
  }
}


function LockJquery(op)
{
  try
  {
    var base = $("#baseID").val();
    if( op == 'Lock')
    {
      var html  = '<table align="center">';
          html += '<tr>';
          html += '<td valign="top"><img src="../'+base+'/imagenes/ajax-loader2.gif"></td>';
          html += '<td valign="middle"><span style=\"color: #ffffff; font-size: 16px;\"><b>GENERANDO CERTIFICADO ...</b></span></td>';
          html += '</tr>';
          html += '<table>';
      $("<div id=\"LockBoxID\">"+html+"</div>").dialog({
        modal    : true,
        resizable: false,
        height   : 60,
        open: function(){ $(".ui-dialog-titlebar").remove(); $("#LockBoxID").css({ height : "29px", width:"310px"});}
      });
    }
    else
    {
      $("#LockBoxID").dialog("destroy").remove();
    }
  }
  catch(e)
  {
    alert("Error en LockJquery: "+e.message+"\nLine: "+e.lineNumber);
  }
}







































