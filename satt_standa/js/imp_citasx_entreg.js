$(document).ready(function() 
{
  //var Standa = $( "#StandaID" ).val();
  //var filter = $( "#filterID" ).val();
  
  //$( "#cod_transpID" ).autocomplete({
  //  source: "../"+ Standa +"/desnew/ajax_desnew_despac.php?option=getTransp&standa="+Standa+"&filter="+filter,
  //  minLength: 2, 
  //  delay: 100
  //});
  
});

function ValidaImportar()
{
  try
  {
    var Standa = $( "#StandaID" ).val();
    var filter = $( "#fileCitasID" ); 
    var types  = [ 
                   'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',     
                   'application/vnd.ms-excel',
                   'application/msexcel',
                   'application/x-msexcel',
                   'application/x-ms-excel',
                   'application/x-excel',
                   'application/x-dos_ms_excel',
                   'application/xls',
                   'application/x-xls',
                   'text/csv'
                 ]

    if(filter.val() == '')
    {
      return alert('Por favor seleccione un archivo para poderlo importar!');
    }

    var file = document.getElementById('fileCitasID');    
    if(file.files[0].size <= 0)
    {
      return alert("Por favor valide el archivo a importar, puede ser que no contenga datos para importar!");
    }

    if(jQuery.inArray( file.files[0].type, types ) < 0 )
    {
      return alert("Por favor valide el tipo de archivo a importar, debe ser un archivo EXCEL o CSV!");
    }
    
    
    // Por temas de tiempo no se usa el sweet alert

    if(confirm('¿Desea importar el archivo?'))
    {
      $('#OptionID').val('importarArchivo');
      $('form').submit();
    }

    
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}
 

function EditImpacto( cod_transp, cod_impacto )
{
  try
  {
    var Standa = $("#StandaID").val();
    
    $.ajax({
      url: "../"+ Standa +"/impact/ajax_impact_impact.php",
      data : "option=EditImpacto&Standa="+Standa+"&cod_impacto="+cod_impacto+"&cod_transp="+cod_transp,
      method : 'POST',
      success : 
        function ( data ) 
        { 
          $("#FormEditID").html( data );
          $("#FormEditID").fadeIn();
        }
    });
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}