$( document ).ready(function() {
    $("#cod_transpID").multiselect().multiselectfilter();

    $( "#fec_inicialID,#fec_finalID" ).datepicker();

    $.mask.definitions["A"]="[12]";
    $.mask.definitions["M"]="[01]";
    $.mask.definitions["D"]="[0123]";
    $.mask.definitions["H"]="[012]";
    $.mask.definitions["N"]="[012345]";
    $.mask.definitions["n"]="[0123456789]";

    $( "#fec_inicialID,#fec_finalID" ).mask("Annn-Mn-Dn");

  });

  function listar()
  {
    try {
        let fec_inicial =$( "#fec_inicialID").val();
        if (fec_inicial == '') {
          alert('La Fecha Inicial es Obligatoria');
          return $( "#fec_inicialID").focus();
        }

        var fec_final = $( "#fec_finalID").val();
        if (fec_final == '') {
          alert('La Fecha Final es Obligatoria');
          return $( "#fec_finalID").focus();
        }
        var cod_transp = 0;

        $("input[type=checkbox]:checked").each(function(i,o){
             
          if($(this).attr("name") == 'multiselect_cod_transpID' ){
            if( cod_transp == 0 ){
                cod_transp =  $(this).val() ;
            }else{
                cod_transp += ','+ $(this).val();
            }
          }
        });

        $("#optionID").val(99);
        $("#cod_transpID_").val(cod_transp)
        $("#formularioID").submit();
    }
    catch (e){
      alert( "Error Listar " + e.message);
    }
  }