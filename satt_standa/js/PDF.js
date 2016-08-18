function GeneratePDF()
{
  try
  {
    var Standa = $("#central").val();
    var Aplica = $("#bd_aplica").val();
    var Despac = $("#despac").val();
    
    $.ajax({
      type: "POST",
      url: "../" + Standa + "/despac/pdf_planxx_rutaxx.php",
      data: "aplica=" + Aplica + "&standa=" + Standa+ "&despac=" + Despac,
      success: function( datos )
      {
        window.open(datos) ;
      }
    });  
  }
  catch( e )
  {
    console.log("Error en function GeneratePDF: " + e.message );
  }
  finally
  {
    return false;
  }
  
}