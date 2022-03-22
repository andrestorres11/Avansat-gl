function verifiData()
{
	try 
	{
		var num_despac = $("#num_despacID");
		var cod_manifi = $("#cod_manifiID");
		var fec_busque = $("#fec_busqueID");
		var opcion = $("#opcionID");
		var frm_bitaco = $("#frm_bitacoID");

		if ( !num_despac.val() && !cod_manifi.val() ){
			opcion.val("consulFec");
			frm_bitaco.submit();
		}else{
			frm_bitaco.submit();
		}
	}
	catch(e)
	{
		console.log( "Error Función verifiData: "+e.message+"\nLine: "+Error.lineNumber );
    return false;
	}
}


jQuery(function($) 
{
	$( "#fec_busqueID" ).datepicker({
		changeMonth: true,
		changeYear: true
	});

	$.mask.definitions["A"]="[12]";
	$.mask.definitions["M"]="[01]";
	$.mask.definitions["D"]="[0123]";

	$.mask.definitions["H"]="[012]";
	$.mask.definitions["N"]="[012345]";
	$.mask.definitions["n"]="[0123456789]";

	$( "#fec_busqueID" ).mask("Annn-Mn-Dn");
});

function reloadFecCreaci()
{
	try{
		var data = [];
		$("td[name^='cellFecCreaci']").each(function(i,o){
			data[i] = $(this).html();
		});

		var j = data.length -1;
		var k = 0;

		if( j > 0 ){
			for( var i=j; i>=0; i-- ){
				if( i == j ){
					$("td[name='cellFecCreaci"+i+"']").html( data[0] );
				}else{
					k = i+1;
					$("td[name='cellFecCreaci"+i+"']").html( data[k] );
				}
			}
		}
	}
	catch(e)
	{
		console.log( "Error Function reloadFecCreaci: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}