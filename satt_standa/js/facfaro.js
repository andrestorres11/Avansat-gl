
 

function aceptar_lis()
{
  try {
  
    var fecini = document.getElementById('feciniID');
    var fecfin = document.getElementById('fecfinID');
    var transp = document.getElementById('transpID');
    var nit_transport = document.getElementById('nit_transportID');
    
    if(nit_transport.value == ''){
      
      if (transp.value == '') {
        alert('La Transportadora es requerida');
        return transp.focus();
      }
      
    }
    
    if (transp.value == '') {
      if (nit_transport.value == '') {
        alert('El Nit de la Transportadora es requerido');
        return nit_transport.focus();
      }
        
    }
    
    
    if(fecini.value=='' || fecfin.value=='')
      return alert('Las Fechas Son Obligatorias');
    var feciniArray = fecini.value.split("-");
    var fecini = new Date(feciniArray[0],Number(feciniArray[1])-1,feciniArray[2]);
    var fecfinArray = fecfin.value.split("-");
    var fecfin = new Date(fecfinArray[0],Number(fecfinArray[1])-1,fecfinArray[2]);
    
  if(fecini>fecfin)
    alert('La Fecha inicial de Salida  NO puede ser Mayor a la Fecha Final de Salida');
  else
    $("#opcion").val(1);
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
    var datos = $("#tableExcelFacturasFaro").html();
    
    $("#opcionID").val("excel"); 
    $("#exportExcelID").val('');
    $("#exportExcelID").val(datos);
 
    formulario.submit();

  }
  catch(e)
  {
    console.log( "Error Fuction exportarExcel: "+e.message+"\nLine: "+e.lineNumber );
    return false;
  }
}


function excelFacturacionFaro()
{
 try{

  $("#exportExcelID").val(datos);
    form_facFaroID.submit();
  }
  catch(e)
  {
    console.log( "Error Function exportTableExcel: "+e.message+"\nLine: "+e.lineNumber );
    return false;
  }
}