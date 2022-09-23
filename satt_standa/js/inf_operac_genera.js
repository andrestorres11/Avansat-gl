jQuery(function($) 
{
    $("#opcionID").val(99);
	$( "#fec_iniciaID, #fec_finaliID" ).datepicker({
					changeMonth: true,
					changeYear: true
	});

	$.mask.definitions["A"]="[12]";
	$.mask.definitions["M"]="[01]";
	$.mask.definitions["D"]="[0123]";

	$.mask.definitions["H"]="[012]";
	$.mask.definitions["N"]="[012345]";
	$.mask.definitions["n"]="[0123456789]";

	$( "#fec_iniciaID, #fec_finaliID" ).mask("Annn-Mn-Dn");
});

$(function() {
    $("#cod_transpID").multiselect({maxSelected:5}).multiselectfilter();
    $("input[name=multiselect_cod_transpID]").click(function() {
        let sum_cod_transpID=0;
        $("input[name=multiselect_cod_transpID]:checked").each(function(e) {
            sum_cod_transpID++;  
        });
        if(sum_cod_transpID>5)
        {
            $(this).removeAttr("checked");
            alert('solo se puede escojer maximo 5 empresas!')
        }
    });
    $("#tip_informID").change(function(){
        let value=$('#tip_informID option:selected').val();
        if(value==2){
            $("#nov1").hide();
            $("#nov2").hide();
            $("#opcionID").val(98);
        }else{
            $("#nov1").show();
            $("#nov2").show();
            $("#opcionID").val(99);
        }
    });
    $("input[name=Buscar]").click(function() {
        let cod_transp=0;
        $("input[name=multiselect_cod_transpID]:checked").each(function(e) {
            if( cod_transp == 0 ){
                cod_transp = ''+ $(this).val() +'';
            }else{
                cod_transp += ','+ $(this).val() +'';
            }
        });
        if(cod_transp == 0 ){
            alert('Atenci\xf3n', 'Debe seleccionar por lo menos una transportadora', $(".ui-multiselect-header"));
            return false;
        }
        $("#trans_").val(cod_transp);
        $('#formID').submit();
    });
    
});