jQuery(function($) 
{
  $(".ui-datepicker-week-col").css( "color", "#FFFFFF" );
  $( "#fec_finaliID, #fec_iniciaID" ).datepicker({
    changeMonth: true,
    changeYear: true
  });
  
  $( "#hor_iniciaID, #hor_finaliID" ).timepicker();
  
  $.mask.definitions["A"]="[12]";
  $.mask.definitions["M"]="[01]";
  $.mask.definitions["D"]="[0123]";
  
  $.mask.definitions["H"]="[012]";
  $.mask.definitions["N"]="[012345]";
  $.mask.definitions["n"]="[0123456789]";
  
  $( "#fec_iniciaID, #fec_finaliID" ).mask("Annn-Mn-Dn");
});


function VerifiData()
{
	try 
	{
		var num_despac = $("#num_despacID");
		var dic_remesa = $("#dic_remesaID");
		var ciu_origen = $("#ciu_origenID");
		var ciu_destin = $("#ciu_destinID");
		/*
		if ( !num_despac.val() && !dic_remesa.val() && !ciu_origen.val() && !ciu_destin.val() )
		{
			num_despac.focus();
			alert("Por favor ingrese los parametros para realizar la consulta.");
			return false;
		}
		*/
		var frm_bitaco = $("#frm_paquetID");
		frm_bitaco.submit();
	}
	catch(e)
	{
		console.log( "Error Función VerifiData: "+e.message+"\nLine: "+Error.lineNumber );
    return false;
	}
}
