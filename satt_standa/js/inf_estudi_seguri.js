
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

        var box_checke = $("input[type=checkbox]:checked");
        var cod_transp = '';
        box_checke.each(function(i, o) {
          if ($(this).attr("name") == 'multiselect_cod_transpID'){
            if(i>1){
              cod_transp += ',';
            }
            if($(this).val()!=''){
              cod_transp += '"' + $(this).val() + '"';
            }
          }  
        })
    
        $("#transp").val(cod_transp);
        $("#cod_transpID").val('prueba');
        $("#optionID").val(99);
        $("#formularioID").submit();
    }
    catch (e){
      alert( "Error Listar " + e.message);
    }
  }

  function exportExcel(){
    
    try {
        window.open("../satt_standa/factur/inf_estudi_seguri_excel.php")
    } catch (e) {
        console.log("Error Fuction pintar: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
  }